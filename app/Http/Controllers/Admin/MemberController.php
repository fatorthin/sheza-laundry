<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62' . ltrim($digits, '0');
        }

        if (!str_starts_with($digits, '62')) {
            return '62' . $digits;
        }

        return $digits;
    }

    public function index()
    {
        $members = Member::withCount('orders')->latest()->paginate(20);
        return view('admin.members', compact('members'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'phone' => $this->normalizePhone((string) $request->input('phone', '')),
        ]);

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
        $request->merge([
            'phone' => $this->normalizePhone((string) $request->input('phone', '')),
        ]);

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
