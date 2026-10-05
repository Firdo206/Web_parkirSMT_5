<?php

namespace App\Http\Controllers\adminParkir;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DaruratController extends Controller
{
    public function index(): View
    {
        // TODO: logic buka pintu/palang otomatis saat keadaan darurat
        return view('admin_parkir.darurat');
    }
}