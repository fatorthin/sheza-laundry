<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'status' => 'required|in:baru,dicuci,disetrika,siap_diambil,selesai',
        ]);
        $order->update($validated);
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

        return back()->with('success', 'Berat berhasil diperbarui. Status diubah ke Siap Diambil.');
    }

    public function pos()
    {
        return view('admin.pos');
    }
}
