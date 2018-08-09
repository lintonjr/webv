<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PlanosController extends Controller
{
    public function index() {
        $planos = \App\Planos::where('codigo_cidade', session('codigo_cidade'))->where('inicio_vigencia', '<=', DB::raw('CURDATE()'))->where('fim_vigencia', '>=', DB::raw('CURDATE()'))->where('exibe_online', '1')->get();
        $cidade = \App\Cidades::find(session('codigo_cidade'));
        return view('planos', compact('planos', 'cidade'));
    }
}
