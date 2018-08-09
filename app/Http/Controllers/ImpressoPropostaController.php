<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webservice;

class ImpressoPropostaController extends Controller
{

    public function __construct() {
        $this->webservice = new \App\Webservice;
    }

    public function impresso() {
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $contrato = \App\Contratos::find($contato->codigo_contrato);
        $codigo_plano = $contato->codigo_plano;
        $impresso = \App\ImpressosProposta::where('status', 'A')->where('codigo_plano', $codigo_plano)->first();
        $impresso_proposta = "<meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />" . $impresso->texto_impresso;
        $impresso_proposta = $this->RetornaImpressoDaPropostaPadrao($contrato, $impresso_proposta);

		$nome_impresso = date("YmdHis") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
		file_put_contents(storage_path() . '/app/temp/' . $nome_impresso . ".html", $impresso_proposta);

		$comando = base_path() . '/app/Http/Controllers/wkhtmltox/bin/wkhtmltopdf --footer-center [page]/[topage] ' . storage_path() . '/app/temp/' . $nome_impresso . '.html ' . storage_path() . '/app/temp/' . $nome_impresso . '.pdf';
		exec($comando);

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=contrato.pdf');
        @readfile(storage_path() . '/app/temp/' . $nome_impresso . '.pdf');
    }

    private function RetornaImpressoDaPropostaPadrao($_contrato, $_impresso) {
        /* ESSE MÉTODO SÓ EXISTE POR QUE NO SISTEMA WEBVENDAS QUE NÃO FOI FEITO USANDO O LARAVEL, FOI FEITO DA MANEIRA ABAIXO :/ */
        $_codigo_contrato = $_contrato->codigo_contrato;
		$_numero_do_contrato = $_contrato->codigo_contrato_infomed;
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $dados_contato = json_decode($contato->dados);

        $_banco_dados_titular = \DB::Select("SELECT * FROM contratos INNER JOIN contratos_pessoas_fisicas ON contratos_pessoas_fisicas.codigo_contrato = contratos.codigo_contrato INNER JOIN dependencias ON dependencias.codigo_dependencia = contratos_pessoas_fisicas.codigo_dependencia INNER JOIN pessoas_fisicas ON contratos_pessoas_fisicas.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica WHERE contratos.codigo_contrato = ? AND dependencias.codigo_infomed = '0'", array($_codigo_contrato));

		$_banco_dados_endereco_titular = \DB::Select("select * from enderecos_pessoa_fisica where codigo_pessoa_fisica = ? order by tipo_endereco = 'R' desc, correspondencia = 'S' desc limit 1", array($_banco_dados_titular[0]->codigo_pessoa_fisica));

		$_banco_dados_telefone_titular = \DB::Select("select (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 1 limit 0, 1) telefone_1, (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 1 limit 1, 1) telefone_2, (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 3 limit 1) celular from pessoas_fisicas where codigo_pessoa_fisica = ?", array($_banco_dados_titular[0]->codigo_pessoa_fisica));
		$_banco_dados_endereco_titular[0]->codigo_tipo_logradouro_infomed 	= $this->webservice->RetornaValorWebservice("Geral", "getTipoLogradouro", array($_banco_dados_endereco_titular[0]->codigo_tipo_logradouro_infomed, "TPL_NOME"));
		$_banco_dados_endereco_titular[0]->codigo_logradouro_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getLogradouro", array($_banco_dados_endereco_titular[0]->codigo_logradouro_infomed, "NML_NOME"));
		$_banco_dados_endereco_titular[0]->codigo_bairro_infomed 			= $this->webservice->RetornaValorWebservice("Geral", "getBairro", array($_banco_dados_endereco_titular[0]->codigo_bairro_infomed, "BRR_NOME"));
		$_banco_dados_endereco_titular[0]->codigo_municipio_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getMunicipio", array($_banco_dados_endereco_titular[0]->codigo_municipio_infomed, "NUP_NOME"));

		$_banco_dados_dependentes = \DB::Select("SELECT * FROM contratos_pessoas_fisicas INNER JOIN dependencias ON dependencias.codigo_dependencia = contratos_pessoas_fisicas.codigo_dependencia INNER JOIN pessoas_fisicas ON contratos_pessoas_fisicas.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica WHERE contratos_pessoas_fisicas.codigo_contrato = ? AND dependencias.codigo_infomed <> '0'", array($_codigo_contrato));

		$_banco_dados_contratante = \DB::Select("SELECT * FROM contratos INNER JOIN contratos_pessoas_fisicas ON contratos_pessoas_fisicas.codigo_contrato = contratos.codigo_contrato INNER JOIN pessoas_fisicas ON contratos_pessoas_fisicas.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica WHERE contratos.codigo_contrato = ? AND contratos_pessoas_fisicas.contratante = 'S'", array($_codigo_contrato));
		$_banco_dados_endereco_contratante = \DB::Select("select * from enderecos_pessoa_fisica where codigo_pessoa_fisica = ? order by tipo_endereco = 'R' desc, correspondencia = 'S' desc limit 1", array($_banco_dados_contratante[0]->codigo_pessoa_fisica));
		$_banco_dados_telefone_contratante = \DB::Select("select (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 1 limit 0, 1) telefone_1, (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 1 limit 1, 1) telefone_2, (select concat(ddd, ' ', numero) from telefones_pessoa_fisica where telefones_pessoa_fisica.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica and telefones_pessoa_fisica.tipo = 3 limit 1) celular from pessoas_fisicas where codigo_pessoa_fisica = ?", array($_banco_dados_contratante[0]->codigo_pessoa_fisica));
		$_banco_dados_endereco_contratante[0]->codigo_tipo_logradouro_infomed 	= $this->webservice->RetornaValorWebservice("Geral", "getTipoLogradouro", array($_banco_dados_endereco_contratante[0]->codigo_tipo_logradouro_infomed, "TPL_NOME"));
		$_banco_dados_endereco_contratante[0]->codigo_logradouro_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getLogradouro", array($_banco_dados_endereco_contratante[0]->codigo_logradouro_infomed, "NML_NOME"));
		$_banco_dados_endereco_contratante[0]->codigo_bairro_infomed 			= $this->webservice->RetornaValorWebservice("Geral", "getBairro", array($_banco_dados_endereco_contratante[0]->codigo_bairro_infomed, "BRR_NOME"));
		$_banco_dados_endereco_contratante[0]->codigo_municipio_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getMunicipio", array($_banco_dados_endereco_contratante[0]->codigo_municipio_infomed, "NUP_NOME"));

		$_informacoes_do_plano = \DB::Select("SELECT * FROM planos WHERE codigo_plano = ?", array($_banco_dados_titular[0]->codigo_plano));

		$_informacoes_do_vendedor = \DB::Select("SELECT * FROM usuarios WHERE codigo_usuario = ?", array($_banco_dados_titular[0]->codigo_usuario_create));

		$_dados_titular  = "";
		$_dados_titular .= "
			<html>
				<head><meta charset='utf-8'></head>
			<style>
				.campo {
					float: left;
					border-bottom: 1px solid #000;
					border-left: 1px solid #000;
					border-right: 1px solid #000;
					margin-bottom: 7px;
					padding: 0px 3px 2px 3px;
					box-sizing: border-box;
				}
				.titulo {
					width: 100%;
					font-size: 10px;
				}
				.valor {
					width: 100%;
					font-size: 14px;
					min-height: 18px;
				}
				.block {
					width: 100%;
					border-bottom: 1px solid #000;
					border-top: 1px solid #000;
					font-weight: bold;
					text-align: center;
					margin-top: 7px;
					display: inline-block;
					margin-bottom: 7px;
				}
				.dependente {
					border-left: 1px solid #000;
					border-bottom: 1px solid #000;
					display: inline-block;
					width: 100%;
					padding: 3px;
					padding-bottom: -5px;
					padding-bottom: 0px;
					margin-bottom: 6px;
				}
				.dependente .valor {
					font-size: 12px;
				}
				.quadrado {
					width: 10px;
					height: 10px;
					border: 1px solid #000;
					display: inline-block;
				}
				.numero_contrato {
					position: absolute;
					top: 87;
					left: 230;
				}
			</style>

			<div class='numero_contrato'>".$_numero_do_contrato."</div>

			<div class='block'>Dados do Titular</div>

			<div class='campo' style='width: 85%'>
				<div class='titulo'>Nome do Titular</div>
				<div class='valor'>".$_banco_dados_titular[0]->nome."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Valor do Plano</div>
				<div class='valor'>R$ ".number_format($this->RetornaValorDaPessoaFisica($_banco_dados_titular[0]->codigo_pessoa_fisica), 2, ',', '.')."</div>
			</div>

			<div class='campo' style='width: 50%'>
				<div class='titulo'>Nome da Mãe</div>
				<div class='valor'>".$_banco_dados_titular[0]->nome_mae."</div>
			</div>
			<div class='campo' style='width: 50%'>
				<div class='titulo'>Nome do Pai</div>
				<div class='valor'>".$_banco_dados_titular[0]->nome_pai."</div>
			</div>

			<div class='campo' style='width: 25%'>
				<div class='titulo'>CPF</div>
				<div class='valor'>".$_banco_dados_titular[0]->cpf."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Carteira de Identidade</div>
				<div class='valor'>".$_banco_dados_titular[0]->rg."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Orgão</div>
				<div class='valor'>".$_banco_dados_titular[0]->orgao_expedidor."</div>
			</div>
			<div class='campo' style='width: 10%'>
				<div class='titulo'>UF</div>
				<div class='valor'>".$_banco_dados_titular[0]->uf_expedicao."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Expedição</div>
				<div class='valor'>".@date_format(@date_create_from_format("Y-m-d", $_banco_dados_titular[0]->data_expedicao), "d/m/Y")."</div>
			</div>

			<div class='campo' style='width: 25%'>
				<div class='titulo'>Data de Nascimento</div>
				<div class='valor'>".date_format(date_create_from_format("Y-m-d H:i:s", $_banco_dados_titular[0]->data_nascimento), "d/m/Y")."</div>
			</div>
			<div class='campo' style='width: 10%'>
				<div class='titulo'>Sexo</div>
				<div class='valor'>".\App\Tradutor::SexoDaPessoa($_banco_dados_titular[0]->sexo)."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Estado Civil</div>
				<div class='valor'>".\App\Tradutor::EstadoCivilDaPessoa($_banco_dados_titular[0]->estado_civil)."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>CNS</div>
				<div class='valor'>".$_banco_dados_titular[0]->cns."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Declaração de Nascimento</div>
				<div class='valor'>".$_banco_dados_titular[0]->dnv."</div>
			</div>

			<div class='campo' style='width: 100%'>
				<div class='titulo'>Endereço de Correspondência</div>
				<div class='valor'>".
										$_banco_dados_endereco_titular[0]->codigo_tipo_logradouro_infomed.
										" ".
										$_banco_dados_endereco_titular[0]->codigo_logradouro_infomed.
										", ".
										$_banco_dados_endereco_titular[0]->numero.
										" ".
										$_banco_dados_endereco_titular[0]->complemento.
				"</div>
			</div>

			<div class='campo' style='width: 35%'>
				<div class='titulo'>Bairro</div>
				<div class='valor'>".$_banco_dados_endereco_titular[0]->codigo_bairro_infomed."</div>
			</div>
			<div class='campo' style='width: 35%'>
				<div class='titulo'>Cidade</div>
				<div class='valor'>".$_banco_dados_endereco_titular[0]->codigo_municipio_infomed."</div>
			</div>
			<div class='campo' style='width: 10%'>
				<div class='titulo'>UF</div>
				<div class='valor'>".$_banco_dados_endereco_titular[0]->uf_infomed."</div>
			</div>
			<div class='campo' style='width: 20%'>
				<div class='titulo'>CEP</div>
				<div class='valor'>".$_banco_dados_endereco_titular[0]->cep."</div>
			</div>

			<div class='campo' style='width: 34%'>
				<div class='titulo'>Telefone</div>
				<div class='valor'>".$_banco_dados_telefone_titular[0]->telefone_1."</div>
			</div>
			<div class='campo' style='width: 33%'>
				<div class='titulo'>Telefone</div>
				<div class='valor'>".$_banco_dados_telefone_titular[0]->telefone_2."</div>
			</div>
			<div class='campo' style='width: 33%'>
				<div class='titulo'>Celular</div>
				<div class='valor'>".$_banco_dados_telefone_titular[0]->celular."</div>
			</div>
		";

		if (empty($_banco_dados_dependentes)) {
			$_dados_dependentes  = "";
		} else {
			$_dados_dependentes  = "";
			$_dados_dependentes .= "<div class='block'>Dados dos Dependentes</div>";

			foreach ($_banco_dados_dependentes as $_dependentes) {
				$_dados_dependentes .= "<div class='dependente'>";
				$_dados_dependentes .= "
					<div class='campo' style='width: 30%'><div class='titulo'>Nome</div><div class='valor'>".$_dependentes->nome."</div></div>
					<div class='campo' style='width: 10%'><div class='titulo'>Nascimento</div><div class='valor'>".date_format(date_create_from_format("Y-m-d H:i:s", $_dependentes->data_nascimento), "d/m/Y")."</div></div>
					<div class='campo' style='width: 10%'><div class='titulo'>Sexo</div><div class='valor'>".\App\Tradutor::SexoDaPessoa($_dependentes->sexo)."</div></div>
					<div class='campo' style='width: 10%'><div class='titulo'>Est. Civil</div><div class='valor'>".\App\Tradutor::EstadoCivilDaPessoa($_dependentes->estado_civil)."</div></div>
					<div class='campo' style='width: 15%'><div class='titulo'>CPF</div><div class='valor'>".$_dependentes->cpf."</div></div>
					<div class='campo' style='width: 15%'><div class='titulo'>CNS</div><div class='valor'>".$_dependentes->cns."</div></div>
					<div class='campo' style='width: 10%'><div class='titulo'>Valor do Plano</div><div class='valor'>R$ ".number_format($this->RetornaValorDaPessoaFisica($_dependentes->codigo_pessoa_fisica), 2, ',', '.')."</div></div>";

					$_endereco_dependente = \DB::Select("select * from enderecos_pessoa_fisica where codigo_pessoa_fisica = ? order by tipo_endereco = 'R' desc, correspondencia = 'S' desc limit 1", array($_dependentes->codigo_pessoa_fisica));

					foreach ($_endereco_dependente as $_end) {

						$_end->codigo_tipo_logradouro_infomed 	= $this->webservice->RetornaValorWebservice("Geral", "getTipoLogradouro", array($_end->codigo_tipo_logradouro_infomed, "TPL_NOME"));
						$_end->codigo_logradouro_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getLogradouro", array($_end->codigo_logradouro_infomed, "NML_NOME"));
						$_end->codigo_bairro_infomed 			= $this->webservice->RetornaValorWebservice("Geral", "getBairro", array($_end->codigo_bairro_infomed, "BRR_NOME"));
						$_end->codigo_municipio_infomed 		= $this->webservice->RetornaValorWebservice("Geral", "getMunicipio", array($_end->codigo_municipio_infomed, "NUP_NOME"));

						$_dados_dependentes .= "<div class='campo' style='width: 60%'><div class='titulo'>Endereço</div><div class='valor'>".$_end->codigo_tipo_logradouro_infomed . " " . $_end->codigo_logradouro_infomed . ", ". $_end->numero. " ". $_end->complemento. " CEP: ". $_end->cep." - " . $_end->codigo_bairro_infomed . " - " . $_end->codigo_municipio_infomed . " / " . $_end->uf_infomed . "</div></div>";
					}
					$_dados_dependentes .= "<div class='campo' style='width: 40%'><div class='titulo'>Nome da Mãe</div><div class='valor'>".$_dependentes->nome_mae."</div></div>";
				$_dados_dependentes .= "</div>";
			}
		}

		$_segmentacao_contratual  = "<div class='block'>Segmentação Contratual</div>";
		$_segmentacao_contratual .= "<div class='campo' style='width: 20%'><div class='titulo'>Segmentação</div><div class='valor'>Individual/Familiar</div></div>";
		$_segmentacao_contratual .= "<div class='campo' style='width: 20%'><div class='titulo'>Nº de Registro do Produto</div><div class='valor'>".$_informacoes_do_plano[0]->registro_ans."</div></div>";
		$_segmentacao_contratual .= "<div class='campo' style='width: 20%'><div class='titulo'>Abrangência do Plano</div><div class='valor'>".\App\Tradutor::AbrangenciaDoPlano($_informacoes_do_plano[0]->abrangencia)."</div></div>";
		$_segmentacao_contratual .= "<div class='campo' style='width: 20%'><div class='titulo'>Dia de Vencimento</div><div class='valor'>".$_banco_dados_titular[0]->dia_vencimento."</div></div>";
		$_segmentacao_contratual .= "<div class='campo' style='width: 20%'><div class='titulo'>Acomodação</div><div class='valor'>".\App\Tradutor::AcomodacaoDoPlano($_informacoes_do_plano[0]->acomodacao)."</div></div>";

		$_dados_contratante  = "";
		$_dados_contratante .= "
			<div class='block'>Dados do Contratante</div>

			<div class='campo' style='width: 75%; margin-top: 10px'>
				<div class='titulo'>Responsável Legal / Contratante</div>
				<div class='valor'>".$_banco_dados_contratante[0]->nome."</div>
			</div>
			<div class='campo' style='width: 10%; margin-top: 10px'>
				<div class='titulo'>Sexo</div>
				<div class='valor'>".\App\Tradutor::SexoDaPessoa($_banco_dados_contratante[0]->sexo)."</div>
			</div>
			<div class='campo' style='width: 15%; margin-top: 10px'>
				<div class='titulo'>Valor do Plano</div>
				<div class='valor'>R$ ".number_format($this->RetornaValorDoContrato($_banco_dados_contratante[0]->codigo_contrato), 2, ',', '.')."</div>
			</div>

			<div class='campo' style='width: 35%'>
				<div class='titulo'>Nome da Mãe</div>
				<div class='valor'>".$_banco_dados_contratante[0]->nome_mae."</div>
			</div>
			<div class='campo' style='width: 35%'>
				<div class='titulo'>Nome do Pai</div>
				<div class='valor'>".$_banco_dados_contratante[0]->nome_pai."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Valor Taxa de Inscr.</div>
				<div class='valor'>R$ ".@number_format($this->RetornaTaxaInscricaoDoContrato($_banco_dados_contratante[0]->codigo_contrato), 2, ',', '.')."</div>
			</div>

			<div class='campo' style='width: 15%'>
				<div class='titulo'>Estado Civil</div>
				<div class='valor'>".\App\Tradutor::EstadoCivilDaPessoa($_banco_dados_contratante[0]->estado_civil)."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Data de Nascimento</div>
				<div class='valor'>".date_format(date_create_from_format("Y-m-d H:i:s", $_banco_dados_contratante[0]->data_nascimento), "d/m/Y")."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Carteira de Identidade</div>
				<div class='valor'>".$_banco_dados_contratante[0]->rg."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>Orgão</div>
				<div class='valor'>".$_banco_dados_contratante[0]->orgao_expedidor."</div>
			</div>
			<div class='campo' style='width: 5%'>
				<div class='titulo'>UF</div>
				<div class='valor'>".$_banco_dados_contratante[0]->uf_expedicao."</div>
			</div>
			<div class='campo' style='width: 10%'>
				<div class='titulo'>Expedição</div>
				<div class='valor'>".@date_format(@date_create_from_format("Y-m-d", $_banco_dados_contratante[0]->data_expedicao), "d/m/Y")."</div>
			</div>
			<div class='campo' style='width: 15%'>
				<div class='titulo'>CPF</div>
				<div class='valor'>".$_banco_dados_contratante[0]->cpf."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>CNS</div>
				<div class='valor'>".$_banco_dados_contratante[0]->cns."</div>
			</div>

			<div class='campo' style='width: 20%'>
				<div class='titulo'>Declaração de Nascimento</div>
				<div class='valor'>".$_banco_dados_contratante[0]->dnv."</div>
			</div>
			<div class='campo' style='width: 35%'>
				<div class='titulo'>Email</div>
				<div class='valor'>".$_banco_dados_contratante[0]->email."</div>
			</div>
			<div class='campo' style='width: 20%'>
				<div class='titulo'>Autorizo receber SMS</div>
				<div class='valor'>Sim <div class='quadrado'></div> Não <div class='quadrado'></div></div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Celular</div>
				<div class='valor'>".$_banco_dados_telefone_contratante[0]->celular."</div>
			</div>

			<div class='campo' style='width: 100%'>
				<div class='titulo'>Endereço de Correspondência</div>
				<div class='valor'>".
										$_banco_dados_endereco_contratante[0]->codigo_tipo_logradouro_infomed.
										" ".
										$_banco_dados_endereco_contratante[0]->codigo_logradouro_infomed.
										", ".
										$_banco_dados_endereco_contratante[0]->numero.
										" ".
										$_banco_dados_endereco_contratante[0]->complemento.
				"</div>
			</div>

			<div class='campo' style='width: 35%'>
				<div class='titulo'>Bairro</div>
				<div class='valor'>".$_banco_dados_endereco_contratante[0]->codigo_bairro_infomed."</div>
			</div>
			<div class='campo' style='width: 35%'>
				<div class='titulo'>Cidade</div>
				<div class='valor'>".$_banco_dados_endereco_contratante[0]->codigo_municipio_infomed."</div>
			</div>
			<div class='campo' style='width: 10%'>
				<div class='titulo'>UF</div>
				<div class='valor'>".$_banco_dados_endereco_contratante[0]->uf_infomed."</div>
			</div>
			<div class='campo' style='width: 20%'>
				<div class='titulo'>CEP</div>
				<div class='valor'>".$_banco_dados_endereco_contratante[0]->cep."</div>
			</div>

			<div class='campo' style='width: 25%'>
				<div class='titulo'>Telefone</div>
				<div class='valor'>".$_banco_dados_telefone_contratante[0]->telefone_1."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Telefone</div>
				<div class='valor'>".$_banco_dados_telefone_contratante[0]->telefone_2."</div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Portabilidade</div>
				<div class='valor'>Sim <div class='quadrado'></div> Não <div class='quadrado'></div></div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Código Operadora Anterior</div>
				<div class='valor'></div>
			</div>

			<div class='campo' style='width: 25%'>
				<div class='titulo'>Data Exclusão</div>
				<div class='valor'></div>
			</div>
			<div class='campo' style='width: 25%'>
				<div class='titulo'>Último Pagamento</div>
				<div class='valor'></div>
			</div>
			<div class='campo' style='width: 50%'>
				<div class='titulo'>Nome da Empresa</div>
				<div class='valor'></div>
			</div>
			<div class='campo' style='width: 50%'>
				<div class='titulo'>Código Beneficiário</div>
				<div class='valor'></div>
			</div>
			<!--<div class='campo' style='width: 30%'>
				<div class='titulo'>Aproveitamento de Carência</div>
				<div class='valor'><div class='quadrado'></div> Não <div class='quadrado'></div></div>
			</div>-->
			<div class='campo' style='width: 50%'>
				<div class='titulo'>Nº do Registro do Produto</div>
				<div class='valor'></div>
			</div>
		";

		$_carencias = "
			<div class='block'>Carências</div>
			<p style='text-align: justify; font-size: 13px'>24h para Acidentes Pessoais (Eventos exclusivo, com data caracterizada, diretamente externo, súbito, imprevisível, involuntário, violento e causador de lesão física, que, por si só é independente de toda e qualquer outra causa, torne necessário o tratamento médico.); 30 dias para consulta, Exames Laboratoriais Simples e Raio X simples; 180 dias para os demais casos; 300 dias para parto; 24 meses para internação e tratamento de doenças e lesões pré-existentes.</p>
		";

		$_impresso = str_replace("{DADOS_TITULAR}", $_dados_titular, $_impresso);
		$_impresso = str_replace("{DADOS_DEPENDENTES}", $_dados_dependentes, $_impresso);
		$_impresso = str_replace("{SEGMENTACAO_CONTRATUAL}", $_segmentacao_contratual, $_impresso);
		$_impresso = str_replace("{DADOS_CONTRATANTE}", $_dados_contratante, $_impresso);
		$_impresso = str_replace("{CARENCIAS}", $_carencias, $_impresso);

        $_impresso .= "Assinado eletronicamente por " . $dados_contato->contratos[0]->final_nome . " com o usuário " . $dados_contato->contratos[0]->final_login . " em " . date_format(date_create_from_format("Y-m-d H:i:s", $dados_contato->contratos[0]->final_data), 'd/m/Y \à\s H:i:s') . " através do IP " . $dados_contato->contratos[0]->final_ip;
        $_impresso .= "<p><i>* Documento válido apenas após o pagamento da primeira mensalidade.</i></p>";
		return $_impresso;

	}

    private function RetornaValorDoContrato($_codigo_contrato) {
        $_dados_contrato = \DB::select("select *, d.codigo_infomed as dependencia_infomed, l.codigo_infomed as plano_infomed from contratos c inner join contratos_pessoas_fisicas p on c.codigo_contrato = p.codigo_contrato inner join pessoas_fisicas f on p.codigo_pessoa_fisica = f.codigo_pessoa_fisica inner join planos l on l.codigo_plano = c.codigo_plano inner join dependencias d on d.codigo_dependencia = p.codigo_dependencia where c.codigo_contrato = ?", array($_codigo_contrato));
    		if ($_dados_contrato) {
    			$_valor = 0;
    			foreach ($_dados_contrato as $_dado_contrato) {
    				$_dependencia 		= $_dado_contrato->dependencia_infomed;
    				$_data_nascimento 	= $_dado_contrato->data_nascimento;
    				$_codigo_do_plano 	= $_dado_contrato->plano_infomed;

    				$_curdate  = new \DateTimeZone('America/Manaus');
    				$_idade = \DateTime::createFromFormat('Y-m-d H:i:s', $_data_nascimento, $_curdate)->diff(new \DateTime('now', $_curdate))->y;

    				$_valor_retornado = $this->webservice->RetornaValorWebservice("Geral", "getValorPlano", array($_codigo_do_plano, $_dependencia, $_idade));
    				if (empty($_valor_retornado)) { return false; }
    				$_valor += str_replace(",", ".", $_valor_retornado[0]['CVF_VALOR']);
    			}
    			return $_valor;
    		}
    		return false;
    }

    private function RetornaValorDaPessoaFisica($_codigo_pessoa_fisica) {
        $_dados_pessoa_fisica = \DB::select("select dependencias.codigo_infomed as dependencia_infomed, pessoas_fisicas.data_nascimento, planos.codigo_infomed as plano_infomed from pessoas_fisicas inner join contratos_pessoas_fisicas on contratos_pessoas_fisicas.codigo_pessoa_fisica = pessoas_fisicas.codigo_pessoa_fisica inner join dependencias on dependencias.codigo_dependencia = contratos_pessoas_fisicas.codigo_dependencia inner join contratos on contratos.codigo_contrato = contratos_pessoas_fisicas.codigo_contrato inner join planos on planos.codigo_plano = contratos.codigo_plano where pessoas_fisicas.codigo_pessoa_fisica = ?", array($_codigo_pessoa_fisica));
        		if ($_dados_pessoa_fisica) {
        			$_valor = 0;
        			foreach ($_dados_pessoa_fisica as $_dado_contrato) {
        				$_dependencia 		= $_dado_contrato->dependencia_infomed;
        				$_data_nascimento 	= $_dado_contrato->data_nascimento;
        				$_codigo_do_plano 	= $_dado_contrato->plano_infomed;

        				$_curdate  = new \DateTimeZone('America/Manaus');
        				$_idade = \DateTime::createFromFormat('Y-m-d H:i:s', $_data_nascimento, $_curdate)->diff(new \DateTime('now', $_curdate))->y;

        				$_valor_retornado = $this->webservice->RetornaValorWebservice("Geral", "getValorPlano", array($_codigo_do_plano, $_dependencia, $_idade));
        				if (empty($_valor_retornado)) { return false; }
        				$_valor += str_replace(",", ".", $_valor_retornado[0]['CVF_VALOR']);
        			}
        			return $_valor;
        		}
        		return false;
    }

    private function RetornaTaxaInscricaoDoContrato($_codigo_contrato) {
		$_valor_taxa_inscricao = \DB::select("select contrato_modelo.taxa_de_implantacao from contratos inner join planos on contratos.codigo_plano = planos.codigo_plano inner join contrato_modelo on contrato_modelo.codigo_plano = planos.codigo_plano where contratos.codigo_contrato = ?", array($_codigo_contrato));
		return $_valor_taxa_inscricao[0]->taxa_de_implantacao;
	}
}
