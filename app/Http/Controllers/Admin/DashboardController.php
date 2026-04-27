<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $newOrders    = Order::whereDate('created_at', $today)->where('status', 'baru')->count();
        $revenueToday = Order::whereDate('created_at', $today)->where('payment_status', 'lunas')->sum('total');
        $pendingPickup = Order::where('status', 'siap_diambil')->count();
        $totalMembers  = Member::count();

        $recentOrders = Order::with('member')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'newOrders',
            'revenueToday',
            'pendingPickup',
            'totalMembers',
            'recentOrders'
        ));
    }
}
