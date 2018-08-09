<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContatosController extends Controller
{
    public function salva() {
        $dados      = \Request::all();
        $nome_anexo = "";
        if (!empty($dados['anexo'])) {
            $extensoes_permitidas = array("pdf", "doc", "docx", "png", "jpg", "jpeg", "zip", "rar");
            if (!in_array($dados['anexo']->extension(), $extensoes_permitidas)) {
                return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
            } else {
                $nome_anexo = $dados['anexo']->store('/public');
                $nome_anexo = url('/') . '/' . str_replace("public", "uploads", $nome_anexo);
            }
        }

        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $codigo_contrato = $contato->codigo_contrato;

        $contratocontato                    = new \App\ContratosContatos();
        $contratocontato->assunto           = $dados['assunto'];
        $contratocontato->mensagem          = $dados['mensagem'];
        $contratocontato->endereco_anexo    = $nome_anexo;
        $contratocontato->visualizado       = 0;
        $contratocontato->origem            = "cliente";
        $contratocontato->destino           = "unimed";
        $contratocontato->data              = date('Y-m-d H:i:s');
        $contratocontato->codigo_contrato   = $codigo_contrato;
        $contratocontato->save();

        return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-success">Contato enviado com sucesso.</div>');
    }
}
