<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoberturasController extends Controller
{
    public function index() {
        return view('coberturas');
    }
}
