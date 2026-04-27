<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderTracker extends Component
{
    public string $query = '';
    public ?Order $result = null;
    public bool $searched = false;
    public string $errorMsg = '';

    public function track(): void
    {
        $this->searched = true;
        $this->errorMsg = '';
        $this->result = null;

        if (empty(trim($this->query))) {
            $this->errorMsg = 'Masukkan nomor order atau nomor HP.';
            return;
        }

        $this->result = Order::with(['member', 'items'])
            ->where('order_number', $this->query)
            ->orWhereHas('member', fn($q) => $q->where('phone', $this->query))
            ->latest()
            ->first();

        if (!$this->result) {
            $this->errorMsg = 'Order tidak ditemukan. Periksa kembali nomor yang dimasukkan.';
        }
    }

    public function render()
    {
        return view('livewire.order-tracker');
    }
}
