<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NovoPlanoController extends Controller
{
    public function index() {
        return view('interno.novo-plano');
    }

    public function novoplanodo() {
        $codigo_usuario = \Auth::id();
        $contato        = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        $codigo_plano   = $contato->codigo_plano;
        return redirect()->route('comprar-plano-get', ['codigo_plano' => $codigo_plano]);
    }
}
