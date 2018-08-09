<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarenciasController extends Controller
{
    public function index() {
        return view('carencias');
    }
}
