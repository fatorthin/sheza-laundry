<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Pos extends Component
{
    public string $category = 'semua';
    public string $searchService = '';
    public string $searchMember = '';
    public ?int $selectedMemberId = null;
    public string $paymentMethod = 'tunai';
    public bool $showMemberSearch = false;
    public bool $showSuccessModal = false;
    public ?int $lastOrderId = null;

    /** @var array<int, array> */
    public array $cart = [];

    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->when($this->category !== 'semua', fn($q) => $q->where('category', $this->category))
            ->when($this->searchService, fn($q) => $q->where('name', 'like', "%{$this->searchService}%"))
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function members()
    {
        if (strlen($this->searchMember) < 2) return collect();
        return Member::where('name', 'like', "%{$this->searchMember}%")
            ->orWhere('phone', 'like', "%{$this->searchMember}%")
            ->limit(8)->get();
    }

    #[Computed]
    public function selectedMember()
    {
        return $this->selectedMemberId ? Member::find($this->selectedMemberId) : null;
    }

    #[Computed]
    public function hasKiloan(): bool
    {
        return collect($this->cart)->contains('type', 'kiloan');
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cart)
            ->filter(fn($i) => $i['type'] !== 'kiloan')
            ->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    #[Computed]
    public function tax(): float
    {
        return round($this->subtotal * 0.11, 2);
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->tax;
    }

    public function addService(int $serviceId): void
    {
        $service = Service::find($serviceId);
        if (!$service) return;

        $key = "s{$serviceId}";
        if (isset($this->cart[$key])) {
            if ($service->type === 'satuan') {
                $this->cart[$key]['quantity']++;
                $this->cart[$key]['subtotal'] = $this->cart[$key]['price'] * $this->cart[$key]['quantity'];
            }
        } else {
            $this->cart[$key] = [
                'service_id'   => $service->id,
                'name'         => $service->name,
                'type'         => $service->type,
                'price'        => (float) $service->price,
                'unit'         => $service->unit,
                'quantity'     => 1,
                'subtotal'     => $service->type === 'satuan' ? (float) $service->price : 0,
            ];
        }
    }

    public function incrementQuantity(string $key): void
    {
        if (isset($this->cart[$key]) && $this->cart[$key]['type'] === 'satuan') {
            $this->cart[$key]['quantity']++;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['price'] * $this->cart[$key]['quantity'];
        }
    }

    public function decrementQuantity(string $key): void
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['type'] === 'satuan' && $this->cart[$key]['quantity'] > 1) {
            $this->cart[$key]['quantity']--;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['price'] * $this->cart[$key]['quantity'];
        } else {
            $this->removeItem($key);
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function selectMember(int $memberId): void
    {
        $this->selectedMemberId = $memberId;
        $this->showMemberSearch = false;
        $this->searchMember = '';
    }

    public function clearMember(): void
    {
        $this->selectedMemberId = null;
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->selectedMemberId = null;
        $this->paymentMethod = 'tunai';
    }

    public function processOrder(): void
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang masih kosong.');
            return;
        }

        $hasKiloan = $this->hasKiloan;
        $subtotal  = $this->subtotal;
        $tax       = $this->tax;
        $total     = $this->total;

        $order = Order::create([
            'order_number'   => Order::generateOrderNumber(),
            'member_id'      => $this->selectedMemberId,
            'user_id'        => Auth::id(),
            'status'         => 'baru',
            'payment_status' => $hasKiloan ? 'belum_bayar' : 'belum_bayar',
            'payment_method' => $this->paymentMethod,
            'has_kiloan'     => $hasKiloan,
            'subtotal'       => $subtotal,
            'tax'            => $tax,
            'total'          => $total,
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'service_id'   => $item['service_id'],
                'service_name' => $item['name'],
                'service_type' => $item['type'],
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
                'subtotal'     => $item['subtotal'],
            ]);
        }

        $this->lastOrderId = $order->id;
        $this->showSuccessModal = true;
        $this->clearCart();
    }

    public function render()
    {
        return view('livewire.pos');
    }
}
