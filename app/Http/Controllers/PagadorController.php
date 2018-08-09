<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagadorController extends Controller
{
    public function index() {
        $contato = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        return view('interno.pagador', [
            'contato'           => $contato,
            'dados'             => json_decode($contato->dados),
            'prazo'             => date('d/m/Y \à\s H:i:s', strtotime("$contato->data_acesso + 10 days")),
            'estcivil'          => \App\Tradutor::EstadoCivilDaPessoa(),
            'sexo'              => \App\Tradutor::SexoDaPessoa(),
            'orgexpedidores'    => \App\Tradutor::OrgaoExpedidorDaRG(),
            'uf'                => \App\Tradutor::UF(),
            'paises'            => \App\Tradutor::Pais(),
            'codigo_contrato'   => '0',
            'tiplogradouro'     => \App\Tradutor::TipoDeLogradouro(),
            'dependencias'      => \App\Dependencias::where('status', 'A')->get(),
            'plano'             => \App\Planos::where('codigo_plano', $contato->codigo_plano)->first(),
            'uf_permitida'      => \App\Cidades::find(session("codigo_cidade"))->uf_cidade
        ]);
    }

    public function salva() {
        /* Retorna os dados do contato atual */
        $contato        = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        $dados          = json_decode($contato->dados);
        $formdata       = \Request::all();

        $erros      = $this->ValidaDados($formdata['dados_contratante']);
        if ($erros) { return $erros; }

        $dados->contratos[$formdata['codigo_contrato']]->dados_contratante = $this->CorrigeDados($formdata['dados_contratante']);

        $contato->dados = json_encode($dados);
        $contato->save();
        return \Redirect::to('alterar-pagador')->with('mensagem', '<div class="alert alert-success">Registro salvo com sucesso!</div>');
    }

    private function ValidaDados($dados_formulario) {
        if (!\App\Geral::validaCPF($dados_formulario['cpf_contratante'])) {
            return \Redirect::to('alterar-pagador')->withInput()->with('mensagem', '<div class="alert alert-danger">Erro ao salvar o registro. CPF inválido - '.$dados_formulario['cpf_contratante'].'.</div>');
        }
        if (!empty($dados_formulario['cns_contratante']) && !\App\Geral::validaCNS($dados_formulario['cns_contratante'])) {
            return \Redirect::to('alterar-pagador')->withInput()->with('mensagem', '<div class="alert alert-danger">Erro ao salvar o registro. CNS inválido - '.$dados_formulario['cns_contratante'].'.</div>');
        }
        if (strlen($dados_formulario['telcelular_contratante']) != 14) {
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario['telcelular_contratante'].'.</div>');
        }
        if (!empty($dados_formulario['telfixo_contratante']) && strlen($dados_formulario['telfixo_contratante']) != 14) {
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario['telfixo_contratante'].'.</div>');
        }
    }

    private function CorrigeDados($dados_formulario) {
        if (!empty($dados_formulario['nascimento_contratante'])) {
            $dados_formulario['nascimento_contratante'] = date_format(date_create_from_format("d/m/Y", $dados_formulario['nascimento_contratante']), "Y-m-d");
        }
        /* Corrige data de expedição do RG do contratante do formato d/m/Y para o formato Y-m-d */
        if (!empty($dados_formulario['datexpedicao_contratante'])) {
            $dados_formulario['datexpedicao_contratante'] = date_format(date_create_from_format("d/m/Y", $dados_formulario['datexpedicao_contratante']), "Y-m-d");
        }

        /* Formata nomes do formulário para primeira letra maiuscula */
        @$dados_formulario['nome_contratante']      = trim(ucwords(mb_strtolower($dados_formulario['nome_contratante'])));
        @$dados_formulario['mae_contratante']       = trim(ucwords(mb_strtolower($dados_formulario['mae_contratante'])));
        @$dados_formulario['pai_contratante']       = trim(ucwords(mb_strtolower($dados_formulario['pai_contratante'])));
        return $dados_formulario;
    }
}
