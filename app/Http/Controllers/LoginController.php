<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index() {
        \Request::flash();
        $senha = hash('sha512', Input::get('senha'));
        $usuario = \App\Usuarios::where('login', Input::get('email'))->where('senha', $senha)->where('status', 'A')->first();
        if (empty($usuario)) {
            return view('interno.minha-conta', ['mensagem' => '<span class="fa fa-fw fa-exclamation-circle"></span> Login ou senha inválidos']);
        } else {
            if (Auth::loginUsingId($usuario->codigo_usuario, true)) {
                $usuario->ip_acesso             = \Request::ip();
                $usuario->data_ultimo_acesso    = date('Y-m-d H:i:s');
                $usuario->save();
                $contato = \App\ContatosOnline::where('codigo_usuario', $usuario->codigo_usuario)->first();
                session(['codigo_contato' => $contato->codigo_contato]);
                return redirect('contratos');
            } else {
                return view('interno.minha-conta', ['mensagem' => '<span class="fa fa-fw fa-exclamation-circle"></span> Erro ao fazer login']);
            }
        }
    }

    public function sair() {
        Auth::logout();
        return redirect('/');
    }
}
