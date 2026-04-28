<?php

namespace App\Http\Controllers;

use App\Models\Service;

class LandingController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        return view('landing', compact('services'));
    }

    public function profil()
    {
        return view('profil');
    }
}
