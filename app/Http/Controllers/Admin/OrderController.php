<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
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
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.order-show', compact('order', 'services'));
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
            'weight' => 'required|numeric|min:0.001',
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

    public function addItem(Request $request, Order $order)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity'   => 'nullable|numeric|min:0.01',
            'weight'     => 'nullable|numeric|min:0',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        if ($service->type === 'kiloan') {
            $weight   = (float) ($validated['weight'] ?? 0);
            $subtotal = $weight > 0 ? $service->price * $weight : 0;
            OrderItem::create([
                'order_id'     => $order->id,
                'service_id'   => $service->id,
                'service_name' => $service->name,
                'service_type' => $service->type,
                'quantity'     => $weight,
                'weight'       => $weight > 0 ? $weight : null,
                'price'        => $service->price,
                'subtotal'     => $subtotal,
            ]);
            if (!$order->has_kiloan) {
                $order->update(['has_kiloan' => true]);
            }
        } else {
            $quantity = max(1, (int) ($validated['quantity'] ?? 1));
            $subtotal = $service->price * $quantity;
            OrderItem::create([
                'order_id'     => $order->id,
                'service_id'   => $service->id,
                'service_name' => $service->name,
                'service_type' => $service->type,
                'quantity'     => $quantity,
                'weight'       => null,
                'price'        => $service->price,
                'subtotal'     => $subtotal,
            ]);
        }

        $order->load('items')->recalculate();

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function updateItem(Request $request, Order $order, OrderItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|numeric|min:0',
            'weight'   => 'nullable|numeric|min:0',
            'price'    => 'nullable|numeric|min:0',
        ]);

        $price = (float) ($validated['price'] ?? $item->price);

        if ($item->service_type === 'kiloan') {
            $weight   = (float) ($validated['weight'] ?? $item->weight ?? 0);
            $subtotal = $weight > 0 ? $price * $weight : 0;
            $item->update([
                'weight'   => $weight > 0 ? $weight : null,
                'quantity' => $weight,
                'price'    => $price,
                'subtotal' => $subtotal,
            ]);
        } else {
            $quantity = max(1, (float) ($validated['quantity'] ?? $item->quantity ?? 1));
            $item->update([
                'quantity' => $quantity,
                'price'    => $price,
                'subtotal' => $price * $quantity,
            ]);
        }

        $order->load('items')->recalculate();

        return back()->with('success', 'Item berhasil diperbarui.');
    }

    public function deleteItem(Order $order, OrderItem $item)
    {
        $item->delete();
        $order->load('items')->recalculate();

        return back()->with('success', 'Item berhasil dihapus.');
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
