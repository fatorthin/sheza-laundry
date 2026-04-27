<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrderBoard extends Component
{
    public string $search = '';
    public string $filterPayment = '';

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
            ->latest()
            ->get()
            ->groupBy('status');
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $newStatus]);

        if ($newStatus === 'selesai') {
            $order->update(['picked_up_at' => now()]);
        }
    }

    public function markPaid(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['payment_status' => 'lunas']);
    }

    public function render()
    {
        return view('livewire.order-board');
    }
}
