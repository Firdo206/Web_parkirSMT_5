<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function index(): View
    {
        // TODO: logic pendaftaran wajah & plat nomor
        return view('admin_parkir.pendaftaran');
    }
}