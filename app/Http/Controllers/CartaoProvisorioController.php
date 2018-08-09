<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartaoProvisorioController extends Controller
{

    public function __construct() {
        $this->webservice = new \App\Webservice;
    }

    public function impresso() {
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $contrato = \App\Contratos::find($contato->codigo_contrato);
        $_codigo_contrato = $contrato->codigo_contrato;
        $_beneficiarios_do_contrato = $this->webservice->RetornaValorWebservice("Beneficiario", "beneficiario_contrato", array($contrato->codigo_contrato_infomed));
		if (empty($_beneficiarios_do_contrato)) {
			$_beneficiarios_do_contrato = $this->webservice->RetornaValorWebservice("Beneficiario", "beneficiario_contrato", array($_codigo_contrato + 98500000));
			if (empty($_beneficiarios_do_contrato)) {
				echo "Erro - Cartão Provisório não disponível."; die();
			}
		}

		define("FPDF_FONTPATH", app_path() . "/Http/Controllers/fpdf181/font/");
		define("NUMERO",chr(176));

		require(app_path() . "/Http/Controllers/fpdf181/fpdf.php");

		$pdf=new \PDF("P","mm",array(210,297));

		$pdf->SetLeftMargin(15) ;
		$pdf->SetRightMargin(15) ;
		$pdf->SetAutoPageBreak(true,"5");

		foreach ($_beneficiarios_do_contrato as $_beneficiario) {

			$_validade = $this->webservice->RetornaValorWebservice("Beneficiario", "pega_data_segunda_via", array($_beneficiario['BEN_COD_BENEFICIARIO']));
			$_carencias = $this->webservice->RetornaValorWebservice("Beneficiario", "RetornaMaxCarenciaCob", array($_beneficiario['BEN_COD_BENEFICIARIO']));
			$_contratante = $this->webservice->RetornaValorWebservice("Beneficiario", "RetornaContratante", array($_beneficiario['BEN_COD_BENEFICIARIO']));
			$_parametros_infomed = $this->webservice->RetornaValorWebservice("Geral", "getParametrosInfomed", array());

			$pdf->AddPage("P");

			$pdf->Image(url('/')."/img/Logo_NomeCarteira.jpg",40,10,50,10);
			$pdf->Image(url('/')."/img/Logo_Carteira.jpg",20,11,14,7);
			$pdf->Celula(86,17,"",1);
			$pdf->Celula(4,17,"",0);
			$pdf->Celula(86,44,"",1);
			$pdf->SetFont('Times','B',10);
			$pdf->Text(21,25,$this->acento($_parametros_infomed[0]['PIN_CABECALHO_RELATORIO_1']));
			$pdf->Ln(19);
			$pdf->Celula(42,6,"",1);
			$pdf->Celula(2,6,"",0);
			$pdf->Celula(42,6,"",1);
			$pdf->Celula(4,6,"",0);
			$pdf->Ln(8);
			$pdf->Celula(86,6,"",1);
			$pdf->Ln(7);
			$pdf->Celula(86,20,"",1);

			$pdf->SetY(56);
			$pdf->SetX(105);
			$pdf->Celula(86,8,"",1);

			$pdf->SetFont('Times','B',6);

			$pdf->SetFillColor(255,255,255);
			$pdf->SetY(28);
			$pdf->SetX(31);
			$pdf->Celula(10,2,"CÓDIGO ",0,"","",1);

			$pdf->SetY(28);
			$pdf->SetX(74);
			$pdf->Celula(10,2,"PLANO ",0,"","",1);

			$pdf->SetY(36);
			$pdf->SetX(53);
			$pdf->Celula(10,2,"NOME ",0,"","",1);

			$ans = "ANS N. " . $_parametros_infomed[0]['PIN_CODIGO_OPERADORA_MS'];
			$texto1 = "ESTE CARTÃO É NUMERADO, NOMINATIVO E INTRANSFERÍVEL; E SOB";
			$texto2 = "NENHUMA HIPÓTESE PODERÁ SER CEDIDO OU EMPRESTADO. APRESENTE A";
			$texto3 = "CARTEIRA DE IDENTIDADE SEMPRE QUE FOR SOLICITADO.";

			$pdf->Text(172,53,$ans);
			$pdf->Text(106,59,$this->acento($texto1));
			$pdf->Text(106,61,$this->acento($texto2));
			$pdf->Text(106,63,$this->acento($texto3));

			$pdf->SetFont('Times','B',10);
			$pdf->Text(20,33,$this->acento($_parametros_infomed[0]['PIN_CODIGO_SINGULAR'] . $_beneficiario['BEN_COD_BENEFICIARIO']));
			$pdf->SetFont('Times','B',6);
			$pdf->Text(60,33,$this->acento($_beneficiario['PLC_DESCRICAO_RESUMIDA']));

			$pdf->SetFont('Times','B',6);
			$pdf->Text(16,48,$this->acento("CONTRATANTE : "));
			$pdf->Text(16,54,$this->acento("DATA NASC.: "));
			$pdf->Text(66,54,$this->acento("VALIDADE : "));
			$pdf->Text(16,60,$this->acento("ASSINATURA : "));

			$pdf->SetFont('Times','B',8);
			$pdf->Text(34,48,$this->acento($_contratante));
			$pdf->Text(31,54,$this->acento($_beneficiario['PSF_DATA_NASCIMENTO']));
			$pdf->Text(80,54,$this->acento($_validade));
			$pdf->Text(16,41,$this->acento($_beneficiario['PSF_NOME']));

			$pdf->SetY(11);
			$pdf->SetX(105);

			$texto_carteira = explode(',', str_replace('"', "", $_carencias));
			$texto_carteira = implode("\r\n", $texto_carteira);

			$pdf->MultiCell(100,3,$this->acento($texto_carteira));
			$pdf->Ln();
		}

		$_resultado = $pdf->Output();
		//$_nome_arquivo = date("YmdHis") . str_pad(rand(0, 999), "0", 3, STR_PAD_LEFT) . ".pdf";
		//file_put_contents($_SESSION['configuracoes']['acesso']['diretorio'] . "/view/temp/" . $_nome_arquivo, $_resultado);

		//return $_SESSION['configuracoes']['acesso']['url'] . "/view/temp/" . $_nome_arquivo;
    }

    private function acento($texto) {
		return iconv('utf-8','iso-8859-1',$texto) ;
	}
}
