<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webservice;
use App\PessoasFisicas;
use App\DeclaracoesDeSaude;

class ImpressoCptController extends Controller
{

    public function __construct() {
        $this->webservice = new \App\Webservice();
    }

    public function impresso() {
        $contato    = \App\ContatosOnline::find(session('codigo_contato'));
        $contrato   = \App\Contratos::find($contato->codigo_contrato);
        $pessoas    = \App\PessoasFisicas
                            ::join('contratos_pessoas_fisicas', 'pessoas_fisicas.codigo_pessoa_fisica', '=', 'contratos_pessoas_fisicas.codigo_pessoa_fisica')
                            ->join('enderecos_pessoa_fisica', function($join) {
                                $join->on('enderecos_pessoa_fisica.codigo_pessoa_fisica', '=', 'pessoas_fisicas.codigo_pessoa_fisica');
                                $join->on('enderecos_pessoa_fisica.tipo_endereco', '=', \DB::raw("'R'"));
                            })
                            ->join('contratos', 'contratos.codigo_contrato', '=', 'contratos_pessoas_fisicas.codigo_contrato')
                            ->join('planos', 'planos.codigo_plano', '=', 'contratos.codigo_plano')
                            ->where('contratos_pessoas_fisicas.codigo_contrato', $contato->codigo_contrato)
                            ->whereNotNull('codigo_dependencia')->get();
        $dados_contato = json_decode($contato->dados);

        $_estilo = "
        <meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />
        <style>
            * {
                font-size: 20px;
                text-align: justify;
            }
        </style>";
        $_impresso  = $_estilo;
        $_top       = "<img src='".url('/')."/img/top.png' width='1000px'/>";
        $_bottom    = "<img src='".url('/')."/img/bottom_t.png' width='1000px'/>";

        foreach ($pessoas as $pessoa) {
            $respostas = \App\PessoasFisicas
                ::select(\DB::raw('pessoas_fisicas.*, group_concat(concat(pergunta, \' \', \'R: Sim - \', ifnull(complemento, \'\')) SEPARATOR "<br/>") as dlp'))
                ->join('pessoas_fisicas_declaracoes_de_saude', 'pessoas_fisicas_declaracoes_de_saude.codigo_pessoa_fisica', '=', 'pessoas_fisicas.codigo_pessoa_fisica')
                ->where('pessoas_fisicas.codigo_pessoa_fisica', $pessoa->codigo_pessoa_fisica)
                ->where('resposta', 'S')
                ->join('declaracoes_de_saude', 'pessoas_fisicas_declaracoes_de_saude.codigo_declaracao_de_saude', '=', 'declaracoes_de_saude.codigo_declaracao_de_saude')
                ->distinct()->first();

            $pessoa->codigo_tipo_logradouro_infomed     = $this->webservice->RetornaValorWebservice("Geral", "getTipoLogradouro", array($pessoa->codigo_tipo_logradouro_infomed, "TPL_NOME"));
    		$pessoa->codigo_logradouro_infomed 		    = $this->webservice->RetornaValorWebservice("Geral", "getLogradouro", array($pessoa->codigo_logradouro_infomed, "NML_NOME"));
    		$pessoa->codigo_bairro_infomed 			    = $this->webservice->RetornaValorWebservice("Geral", "getBairro", array($pessoa->codigo_bairro_infomed, "BRR_NOME"));
    		$pessoa->codigo_municipio_infomed 		    = $this->webservice->RetornaValorWebservice("Geral", "getMunicipio", array($pessoa->codigo_municipio_infomed, "NUP_NOME"));

            $_meio = "<p style='text-align: center; margin-top: 0;'><b>TERMO DE ACEITAÇAO DE COBERTURA PARCIAL TEMPORÁRIA - CPT</b></p>";

            $_meio .= "
                <p>Eu, ".$respostas->nome.", brasileiro(a), " . ($respostas->rg ? "portador(a) da cédula de identidade nº ".$respostas->rg . " e" : "")." inscrito(a) no CPF sob o nº. ".$respostas->cpf.", residente e domiciliada na ".$pessoa->codigo_tipo_logradouro_infomed." ".$pessoa->codigo_logradouro_infomed.", nº ".$pessoa->numero." Bairro ".$pessoa->codigo_bairro_infomed." - CEP ".$pessoa->cep." na cidade de ".$pessoa->codigo_municipio_infomed.", na condição de proponente para adesão a Plano de Saúde da Unimed Fama, ou na condição de representante legal de proponente menor de idade, para a contratação do plano de saúde de segmentação AMBULATORIAL + HOSPITALAR, registro ANS nº ".$pessoa->registro_ans.", com abrangência ".\App\Tradutor::AbrangenciaDoPlano($pessoa->abrangencia).", e padrão de acomodação em internação ".\App\Tradutor::AcomodacaoDoPlano($pessoa->acomodacao).", devido ao fato de ser portador das seguintes Doenças e Leões Preexistentes:</p>
            ";

            $_meio .= "
                <p>".$respostas->dlp."</p>
            ";

            $_meio .= "
                <p>Para fins de adesão ao plano de saúde em conformidade com o que estabelece a Resolução Normativa nº 162 da ANS, declaro está ciente do cumprimento da CPT – Cobertura Parcial Temporária, e estou ciente de que isso acarretará, durante o prazo de 24 (vinte e quatro) meses após minha adesão de que estarão suspensas as coberturas para procedimentos de alta complexidade (conforme rol ANS), Leitos de Alta Tecnologia e Eventos Cirúrgicos relacionados às doenças acima descritas. Para que não haja dúvidas com relação aos procedimentos que não terei direito durante este prazo após minha adesão, que estão relacionados às doenças declaradas por mim, que posso consultar o Rol da ANS – Agência Nacional de Saúde Suplementar vigente à época de minha contratação, bem como suas atualizações em www.ans.gov.br ou dirigir-me à operadora em caso de dúvidas.</p>
            ";

            $_meio .= "<br/><br/><br/><p style='text-align: center'>Assinado eletronicamente por " . $dados_contato->contratos[0]->declaracao_nome . " com o usuário " . $dados_contato->contratos[0]->declaracao_login . " em " . date_format(date_create_from_format("Y-m-d H:i:s", $dados_contato->contratos[0]->declaracao_data), 'd/m/Y \à\s H:i:s') . " através do IP " . $dados_contato->contratos[0]->declaracao_ip . "</p><br/><br/><br/><br/><br/>";

            $_impresso .= $_top . $_meio . $_bottom;

        }

        $nome_impresso = date("YmdHis") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
		file_put_contents(storage_path() . '/app/temp/' . $nome_impresso . ".html", $_impresso);

		$comando = base_path() . '/app/Http/Controllers/wkhtmltox/bin/wkhtmltopdf --footer-center [page]/[topage] ' . storage_path() . '/app/temp/' . $nome_impresso . '.html ' . storage_path() . '/app/temp/' . $nome_impresso . '.pdf';
		exec($comando);

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=documento.pdf');
        @readfile(storage_path() . '/app/temp/' . $nome_impresso . '.pdf');
    }
}
