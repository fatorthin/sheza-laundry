<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['member', 'items.service', 'user']);
        return view('admin.receipt', compact('order'));
    }
}
