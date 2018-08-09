<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class MinhaContaController extends Controller
{
    public function index() {
        if (Auth::check()) {
            $contato = \App\ContatosOnline::where('codigo_usuario', Auth::id())->first();
            session(['codigo_contato' => $contato->codigo_contato]);
            return redirect('contratos');
        }
        return view('interno.minha-conta');
    }

    public function esqueci() {
        return view('interno.esqueci');
    }
}
