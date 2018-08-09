<?php

namespace App;

class Webservice {

	private $Pessoa;
	private $Contrato;
	private $Beneficiario;
	private $Geral;
	private $Financeiro;
	private $Token;
	private $CaminhoWS;

	public function __construct() {
		$this->Token 		= array('login' => env('WSLOGIN'), 'password' => env('WSPASSWORD'));
		$this->CaminhoWS 	= env('WSURL');

		$this->Pessoa 		= array_merge(array('location' => $this->CaminhoWS . '/PessoaService.php', 			'uri' => 'urn:PessoaService'),			$this->Token);
		$this->Contrato 	= array_merge(array('location' => $this->CaminhoWS . '/ContratoService.php', 		'uri' => 'urn:ContratoService'),		$this->Token);
		$this->Beneficiario = array_merge(array('location' => $this->CaminhoWS . '/BeneficiarioService.php', 	'uri' => 'urn:BeneficiarioService'),	$this->Token);
		$this->Geral 		= array_merge(array('location' => $this->CaminhoWS . '/GeralService.php', 			'uri' => 'urn:GeralService'),			$this->Token);
		$this->Financeiro 	= array_merge(array('location' => $this->CaminhoWS . '/FinanceiroService.php', 		'uri' => 'urn:FinanceiroService'),		$this->Token);
	}

	public function RetornaValorWebservice($_servico, $_metodo, $_parametros = array()) {
		if (!property_exists($this, $_servico)) {
			die("Serviço não existe.");
		} else {
			$server = new \SOAPClient(null, $this->$_servico);
			return $server->__soapCall($_metodo, $_parametros);
		}
	}

}

?>
