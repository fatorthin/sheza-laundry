<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders');
    }

    public function show(Order $order)
    {
        $order->load(['member', 'items.service', 'user']);
        return view('admin.order-show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,dikerjakan,siap_diambil,selesai',
        ]);

        $statusToPersist = $validated['status'];

        if ($statusToPersist === 'selesai' && $order->payment_status !== 'lunas') {
            return back()->with('error', 'Order belum lunas, tidak bisa diubah ke Selesai.');
        }

        $update = ['status' => $statusToPersist];
        if ($statusToPersist === 'siap_diambil' && !$order->ready_at) {
            $update['ready_at'] = now();
        }
        if ($statusToPersist === 'selesai') {
            $update['picked_up_at'] = now();
        }

        $order->update($update);
        $order->refresh()->loadMissing(['member', 'items', 'user']);

        $this->dispatchWhatsAppStatusNotification($order, $statusToPersist);

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    public function finalizeWeight(Request $request, Order $order)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|min:0.1',
        ]);

        $weight = (float) $validated['weight'];
        $order->load('items');

        foreach ($order->items as $item) {
            if ($item->service_type === 'kiloan') {
                $subtotal = $item->price * $weight;
                $item->update(['weight' => $weight, 'subtotal' => $subtotal]);
            }
        }

        $order->update(['weight' => $weight, 'status' => 'siap_diambil']);
        $order->recalculate();
        $order->refresh()->loadMissing(['member', 'items', 'user']);

        $this->dispatchWhatsAppStatusNotification($order, 'siap_diambil');

        return back()->with('success', 'Berat berhasil diperbarui. Status diubah ke Siap Diambil.');
    }

    public function pos()
    {
        return view('admin.pos');
    }

    private function dispatchWhatsAppStatusNotification(Order $order, string $status): void
    {
        $phone = $order->member?->phone;
        if (!$phone) {
            return;
        }

        if ($status === 'siap_diambil') {
            SendWhatsAppNotification::dispatch('ready_pickup', $phone, $order);
            return;
        }

        $statusLabel = match ($status) {
            'baru' => 'Baru',
            'dikerjakan' => 'Dikerjakan',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            default => $status,
        };

        SendWhatsAppNotification::dispatch('status', $phone, null, [
            'order_number' => $order->order_number,
            'status_label' => $statusLabel,
        ]);
    }
}
