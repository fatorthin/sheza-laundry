<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\Order;
use App\Services\WhatsAppService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrderBoard extends Component
{
    public string $search = '';
    public string $filterPayment = '';
    public string $filterStatus = '';
    public string $filterMember = '';
    public array $paymentMethods = [
        'tunai' => 'Tunai',
        'qris' => 'QRIS',
        'transfer' => 'Transfer',
    ];

    public array $statuses = [
        'baru'         => 'Baru',
        'dicuci'       => 'Dicuci',
        'disetrika'    => 'Disetrika',
        'siap_diambil' => 'Siap Diambil',
        'selesai'      => 'Selesai',
    ];

    #[Computed]
    public function orders()
    {
        return Order::with(['member', 'items'])
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('order_number', 'like', "%{$this->search}%")
                    ->orWhereHas('member', fn($m) => $m->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMember !== '', function ($q) {
                if ($this->filterMember === 'guest') {
                    $q->whereNull('member_id');
                    return;
                }

                $q->where('member_id', (int) $this->filterMember);
            })
            ->latest()
            ->get()
            ->groupBy('status');
    }

    #[Computed]
    public function membersForFilter()
    {
        return Member::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $order = Order::with(['member', 'items'])->findOrFail($orderId);

        if ($newStatus === 'selesai' && $order->payment_status !== 'lunas') {
            return;
        }

        $order->update(['status' => $newStatus]);

        if ($newStatus === 'selesai') {
            $order->update(['picked_up_at' => now()]);
        }

        // WhatsApp notification
        $phone = $order->member?->phone;
        if ($phone) {
            $wa = app(WhatsAppService::class);
            if ($newStatus === 'siap_diambil') {
                $wa->notifyReadyForPickupWithInvoice($phone, $order);
            } else {
                $statusLabel = $this->statuses[$newStatus] ?? $newStatus;
                $wa->notifyOrderStatus($phone, $order->order_number, $statusLabel);
            }
        }
    }

    public function markPaid(int $orderId, string $paymentMethod): void
    {
        $order = Order::with('member')->findOrFail($orderId);

        if ($order->status !== 'siap_diambil' || $order->payment_status === 'lunas') {
            return;
        }

        if (!array_key_exists($paymentMethod, $this->paymentMethods)) {
            return;
        }

        $order->update([
            'payment_status' => 'lunas',
            'payment_method' => $paymentMethod,
            'paid_amount' => $order->total,
            'status' => 'selesai',
            'picked_up_at' => now(),
        ]);

        // WhatsApp notification
        $phone = $order->member?->phone;
        if ($phone) {
            app(WhatsAppService::class)->notifyOrderCompleted(
                $phone,
                $order->order_number,
                $paymentMethod
            );
        }
    }

    public function deleteOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $order->delete();
    }

    public function updateWeight(int $orderId, float $weight): void
    {
        $order = Order::with('items')->find($orderId);

        if (!$order || !$order->has_kiloan) {
            return;
        }

        $validatedWeight = max(0, (float) $weight);

        $kiloanItem = $order->items->firstWhere('service_type', 'kiloan');

        if ($kiloanItem) {
            $kiloanItem->quantity = $validatedWeight;
            $kiloanItem->weight = $validatedWeight;
            $kiloanItem->subtotal = $kiloanItem->price * $validatedWeight;
            $kiloanItem->save();

            $order->update(['weight' => $validatedWeight]);
            $order->recalculate();
        }
    }

    public function render()
    {
        return view('livewire.order-board');
    }
}
