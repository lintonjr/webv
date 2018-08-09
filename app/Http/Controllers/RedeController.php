<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedeController extends Controller
{
    public function index() {
        return view('rede');
    }
}
