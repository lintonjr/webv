<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use \App\ContatosOnline;
use DB;

class ComprarPlanoController extends Controller
{
    public function get($codigo_plano) {
        $planos     = \App\Planos::where('codigo_cidade', session('codigo_cidade'))->where('inicio_vigencia', '<=', DB::raw('CURDATE()'))->where('fim_vigencia', '>=', DB::raw('CURDATE()'))->where('exibe_online', '1')->get();
        return view('comprar-plano', compact('planos', 'codigo_plano'));
    }

    public function post() {
        $email      = Input::get('email');
        $ddd        = Input::get('telefone_ddd');
        $telefone   = Input::get('telefone_numero');
        $planos     = \App\Planos::where('codigo_cidade', session('codigo_cidade'))->where('inicio_vigencia', '<=', DB::raw('CURDATE()'))->where('fim_vigencia', '>=', DB::raw('CURDATE()'))->where('exibe_online', '1')->get();

        /* Salva na tabela contatos_online informações deste contato para uso posterior */
        $contato                    = new ContatosOnline();
	    $contato->email             = $email;
        $contato->ddd               = $ddd;
        $contato->celular           = $telefone;
        $contato->data_acesso       = date('Y-m-d H:i:s');
        $contato->ip_acesso         = $_SERVER['REMOTE_ADDR'];
        $contato->codigo_cidade     = session('codigo_cidade');
        $contato->save();
        /* Salva na sessão o código do contato para uso posterior */
        session(['codigo_contato' => $contato->codigo_contato]);

        return view('comprar-plano', compact('email', 'ddd', 'telefone', 'planos'));
    }
}
