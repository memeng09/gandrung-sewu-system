<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;

class DashboardController extends Controller {
    public function index() {
        return view('penari.dashboard', ['penari' => auth()->user()->penari?->load('kecamatan')]);
    }
}