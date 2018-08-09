<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        if (empty(session('codigo_cidade'))) {
            $cidades = \App\Cidades::all();
            return view('cidade', compact('cidades'));
        } else {
            return view('index');
        }
    }
}
