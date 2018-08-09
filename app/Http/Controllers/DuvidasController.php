<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DuvidasController extends Controller
{
    public function index() {
        $duvidas = \App\Duvidas::get();
        return view('duvidas', compact('duvidas'));
    }
}
