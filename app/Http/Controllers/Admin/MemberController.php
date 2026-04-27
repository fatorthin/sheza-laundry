<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::withCount('orders')->latest()->paginate(20);
        return view('admin.members', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20|unique:members,phone',
            'address' => 'nullable|string|max:500',
            'email'   => 'nullable|email|max:255',
        ]);
        Member::create($validated);
        return back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20|unique:members,phone,' . $member->id,
            'address' => 'nullable|string|max:500',
            'email'   => 'nullable|email|max:255',
        ]);
        $member->update($validated);
        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus.');
    }
}
