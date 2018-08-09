<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function index() {
        return view('interno.usuarios', [
            'usuario' => \App\Usuarios::where('codigo_usuario', \Auth::id())->first()
        ]);
    }

    public function salva() {
        $usuario = \App\Usuarios::find(\Auth::id());
        $usuario->nome_real = \Request::input('nome_real');
        if (@\Request::input('nova_senha')) {
            if (@\Request::input('nova_senha') != @\Request::input('repetir_senha')) {
                return \Redirect::to('usuarios')->withInput()->with('mensagem', '<div class="alert alert-danger">Não foi possível salvar a alteração. Senhas não são iguais.</div>');
            }
            $usuario->senha = hash('sha512', \Request::input('nova_senha'));
        }
        $usuario->save();
        return \Redirect::to('usuarios')->withInput()->with('mensagem', '<div class="alert alert-success">Alterações realizadas com sucesso!</div>');
    }
}
