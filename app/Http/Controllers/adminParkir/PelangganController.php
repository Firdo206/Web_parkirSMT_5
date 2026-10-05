<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(): View
    {
        // TODO: logic data pelanggan terdaftar
        return view('admin_parkir.pelanggan');
    }
}