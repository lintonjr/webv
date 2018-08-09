<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use \App\ContatosOnline;
use App\Mascara;
use Request;
use DB;

class ConfirmacaoCompraController extends Controller
{
    public function index() {
        Request::flash();
        /* Retorna informações do plano selecionado */
        $plano = \App\Planos::where('codigo_plano', Input::get('codigo_plano'))->get();
        /* Transforma os dependentes para um array que facilita a manipulação dos dados */
        $this->dependentes = $this->CriaArrayDeDependentes(Input::get('nome_dependente'), Input::get('nascimento_dependente'), $plano[0]->codigo_infomed);
        /* Calcula idade do titular pela data de nascimento */
        $this->idade_titular = date_diff(date_create_from_format('d/m/Y', Input::get('nascimento_titular')), date_create('now'))->y;
        /* Verifica o valor do plano para o titular */
        $this->webservice = new \App\Webservice;
        $this->valor_titular = $this->webservice->RetornaValorWebservice("Geral", "getValorPlano", array($plano[0]->codigo_infomed, env('CODIGO_DEPENDENCIA_TITULAR'), $this->idade_titular));
        $this->valor_titular = \App\Mascara::FloatVal($this->valor_titular[0]['CVF_VALOR']);
        /* Passa o valor do plano do titular para um metodo somar com o valor do plano dos dependentes */
        $this->valor_total = $this->RetornaValorTotalDoPlano($this->valor_titular, $this->dependentes);
        /* Salva informações do contato na tabela contatos_online */
        $this->SalvaInformacoesDoContatoOnline($this->valor_total, $this->dependentes);
        return view('confirmacao-compra', [
            'nome_plano'        => $plano[0]->nome_plano,
            'codigo_ans'        => $plano[0]->registro_ans,
            'codigo_plano'      => $plano[0]->codigo_plano,
            'nome_titular'      => Input::get('nome_titular'),
            'email_titular'     => Input::get('email'),
            'telefone_titular'  => Input::get('telefone'),
            'valor_titular'     => $this->valor_titular,
            'faixa_titular'     => $this->RetornaFaixaPelaIdade($this->idade_titular),
            'ddd_titular'       => Input::get('ddd'),
            'dependentes'       => $this->dependentes,
            'valor_total'       => number_format($this->valor_total, 2, ",", ".")
        ]);
    }

    private function SalvaInformacoesDoContatoOnline($valor_total) {
        if (!empty(session('codigo_contato'))) {
            /* Irá atualizar um contato online */
            $contato    = ContatosOnline::find(session('codigo_contato'));
        } else {
            /* Irá criar um novo contato online */
            $contato    = new ContatosOnline();
        }
        /* Salva na tabela contatos_online informações deste contato para uso posterior */
        $contato->email                 = Input::get('email');
        $contato->ddd                   = Input::get('ddd');
        $contato->celular               = Input::get('telefone');
        $contato->data_acesso           = date('Y-m-d H:i:s');
        $contato->ip_acesso             = $_SERVER['REMOTE_ADDR'];
        $contato->codigo_cidade         = session('codigo_cidade');
        $contato->nome_titular          = Input::get('nome_titular');
        $contato->nascimento_titular    = date_format(date_create_from_format("d/m/Y", Input::get('nascimento_titular')), "Y-m-d");
        $contato->valor_plano           = $valor_total;
        $contato->codigo_plano          = Input::get('codigo_plano');
        $contato->dados                 = json_encode(array(
                                        	"contratos" => array(
                                                array(
                                        			"dados_titular" => array(
                                        				  "nome_titular" => Input::get('nome_titular') ,
                                        				  "faixa_titular" => $this->RetornaFaixaPelaIdade($this->idade_titular) ,
                                        				  "valor_titular" => $this->valor_titular,
                                        				  "nascimento_titular" => $contato->nascimento_titular
                                        				),
                                        			"dados_dependentes" => $this->dependentes
                                                )
                                        	)
                                        ));

        $contato->save();
        $this->GeraProtocolo($contato);
        /* Salva na sessão o código do contato para uso posterior */
        session(['codigo_contato' => $contato->codigo_contato]);
    }

    private function GeraProtocolo($contato) {
        /*
            Composição protocolo:
            4 POSIÇÕES - `codigo_contrato_padrao` na tabela `cidades`
            4 POSIÇÕES - ano atual
            2 POSIÇÕES - mes atual
            2 POSIÇÕES - dia atual
            6 POSIÇÕES - `codigo_contato` na tabela `contatos_online`
            2 POSIÇÕES - numero randomico
         */
        $_codigo_cidade = session('codigo_cidade');
        $cidade = \App\Cidades::find($_codigo_cidade);

        $protocolo  = str_pad($cidade->codigo_contrato_padrao, 4 , "0", STR_PAD_LEFT);
        $protocolo .= date('Y');
        $protocolo .= date('m');
        $protocolo .= date('d');
        $protocolo .= str_pad($contato->codigo_contato, 6 , "0", STR_PAD_LEFT);
        $protocolo .= str_pad(\mt_rand(0, 99), 2 , "0", STR_PAD_LEFT);

        $dados  = json_decode($contato->dados);
        $dados->contratos['0']->protocolo = $protocolo;
        $contato->dados = json_encode($dados);
        $contato->save();

    }

    private function RetornaValorTotalDoPlano($_valor_titular, $_dependentes_array) {
        $_valor_total = \App\Mascara::FloatVal($_valor_titular);
        if (empty($_dependentes_array)) { return $_valor_total; }
        foreach ($_dependentes_array as $_dependente) {
            $_valor_total += \App\Mascara::FloatVal($_dependente['valor_dependente']);
        }
        return $_valor_total;
    }

    private function CriaArrayDeDependentes($_nomes, $_nascimentos, $codigo_infomed) {
        $webservice = new \App\Webservice;
        if (empty($_nomes)) { return array(); }
        $_retorno = "";
        foreach ($_nomes as $_index => $_nome) {
            $_retorno[$_index]['nome_dependente'] = $_nome;
        }
        foreach ($_nascimentos as $_index => $_nascimento) {
            $idade_dependente = date_diff(date_create_from_format("d/m/Y", $_nascimento), date_create('now'))->y;
            $valor_dependente = $webservice->RetornaValorWebservice("Geral", "getValorPlano", array($codigo_infomed, env('CODIGO_DEPENDENCIA'), $idade_dependente));
            $_retorno[$_index]['faixa_dependente']      = $this->RetornaFaixaPelaIdade($idade_dependente);
            $_retorno[$_index]['valor_dependente']      = \App\Mascara::FloatVal(@$valor_dependente[0]['CVF_VALOR']);
            $_retorno[$_index]['nascimento_dependente'] = date_format(date_create_from_format("d/m/Y", $_nascimento), "Y-m-d");
        }
        return $_retorno;
    }

    private function RetornaFaixaPelaIdade($_idade) {
        if ($_idade >= 0 && $_idade <= 18) {
            return "00 - 18";
        } else if ($_idade >= 19 && $_idade <= 23) {
            return "19 - 23";
        } else if ($_idade >= 24 && $_idade <= 28) {
            return "24 - 28";
        } else if ($_idade >= 29 && $_idade <= 33) {
            return "29 - 33";
        } else if ($_idade >= 34 && $_idade <= 38) {
            return "34 - 38";
        } else if ($_idade >= 39 && $_idade <= 43) {
            return "39 - 43";
        } else if ($_idade >= 44 && $_idade <= 48) {
            return "44 - 48";
        } else if ($_idade >= 49 && $_idade <= 53) {
            return "49 - 53";
        } else if ($_idade >= 54 && $_idade <= 58) {
            return "54 - 58";
        } else if ($_idade >= 59) {
            return "59+";
        }
    }
}
