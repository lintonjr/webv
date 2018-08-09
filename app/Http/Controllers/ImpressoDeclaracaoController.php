<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImpressoDeclaracaoController extends Controller
{
    public function impresso() {
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $contrato = \App\Contratos::find($contato->codigo_contrato);
        $dados_contato = json_decode($contato->dados);
        $_codigo_contrato = $contrato->codigo_contrato;

    	$_impresso  = "";
    	$_pessoas_fisicas_do_contrato = $this->RetornaPessoasFisicasDoContrato($_codigo_contrato, true);
    	if (empty($_pessoas_fisicas_do_contrato)) { return false; }
    	$_impresso .= "<meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />";
    	$_impresso .= "
                <style>
                        #declaracao_de_saude, #declaracao_de_saude table {
                        	font-size: 13px;
                        }

                        #titulo {
                        	font-size: 23px;
                        	font-weight: bold;
                        	width: 80%;
                        	float: left;
                        }

                        #imagem {
                        	float: left;
                        	width: 20%;
                        }

                        #imagem img {
                        	width: 140px;
                        }

                        p {
                        	text-align: justify;
                        }

                        #numero {
                        	font-weight: bold;
                        }

                        #alto {
                        	font-size: 16px;
                        }

                        .pagebreak {
                        	page-break-before: always;
                        }

                        #titulo_corpo {
                        	font-weight: bold;
                        	background-color: #ccc;
                        	padding: 2px;
                        }

                        td, th {
                        	border: 1px solid #000;
                        	padding: 2px;
                        }

                        table {
                        	width: 100%;
                        }

                        #alert_declaracao {
                        	font-size: 14px;
                        	font-weight: bold;
                        	text-align: center;
                        	border-top: 2px solid rgb(0, 0, 0);
                        	border-bottom: 2px solid rgb(0, 0, 0);
                        	padding: 2px;
                        }

                        #data {
                        	margin-top: 10px;
                        }

                        #data td, #data th {
                        	border: 0px;
                        	text-align: center;
                        }

                        #unimed {
                        	border-top: 1px solid rgb(0, 0, 0);
                        	margin-top: 10px;
                        	padding-top: 10px;
                        	text-align: center;
                        	font-weight: bold;
                        }
                </style>
        ";
        $_quantidade = count($_pessoas_fisicas_do_contrato);
    	foreach ($_pessoas_fisicas_do_contrato as $_index => $_pf) {
    		$_impresso .= "<div id='declaracao_de_saude'>";
    			$_impresso .= "<div id='topo'>";
    				$_impresso .= "<div id='titulo'>DECLARAÇÃO DE SAÚDE</div>";
    				$_impresso .= "<div id='imagem'><img src='".url('/')."/img/fama.png'/></div>";
    				$_impresso .= "<div id='numero'>REFERENTE À PROPOSTA DE ADMISSÃO <span id='alto'>N°</span><br/>Nome: ".$_pf->nome." - CPF: ".$_pf->cpf."</div><hr/>";
    				$_impresso .= "<p>Esta declaração é parte integrante do Contrato que ora se assina, válido para todos os usuários indicados na PROPOSTA DE ADMISSÃO. Estou ciente da minha obrigação de informar nesta oportunidade, em meu nome e dos meus dependentes, se portador ou não de lesões ou doenças pré-existentes sob pena de imputação de fraude.</p>";
    			$_impresso .= "</div>";
    			$_impresso .= "<div id='corpo'>";
    				$_impresso .= "<div id='titulo_corpo'>DECLARAÇÃO DE SAÚDE</div>";
    				$_impresso .= "<table cellspacing='0'>";
    					$_impresso .= "<tr>
    										<th>Ítem</th>
    										<th>Responda as questões abaixo, assinalando com (S) para as respostas afirmativas e (N) para negativas</th>
    										<th>Resposta</th>
    										<th>Complemento</th>
    									</tr>";

    					$_respostas = $this->RetornaRespostasDaDeclaracaoDeSaude($_codigo_contrato, $_pf->codigo_pessoa_fisica);
    					if (!empty($_respostas)) {
    						$_item = 0;
    						foreach ($_respostas as $_resposta) {
    							$_item++;
    							$_impresso .= "<tr>";
    								$_impresso .= "<td>".str_pad($_item, 2, "0", STR_PAD_LEFT)."</td>";
    								$_impresso .= "<td>".$_resposta->pergunta."</td>";
    								if ($_resposta->resposta == 'N') {
    									$_impresso .= "<td>Não</td>";
    								} else if ($_resposta->resposta == 'S') {
    									$_impresso .= "<td>sim</td>";
    								} else {
    									$_impresso .= "<td>".$_resposta->resposta."</td>";
    								}
    								$_impresso .= "<td>".$_resposta->complemento."</td>";
    							$_impresso .= "</tr>";
    						}
    					}
    				$_impresso .= "</table>";
    			$_impresso .= "</div>";
    			$_impresso .= "<div id='final'>";
    				$_impresso .= "<p>Declaro para os devidos fins e efeitos que as informações de saúde relativas a mim e a meus dependentes foram espontanhamente feitas, estando ciente que nos termos da Lei N° 9656/98 a omissão de fatos e informações que possam influir no correto enquadramento das carências/agravos, poderá ser considerada como comportamento fraudulento, implicando na minha responsabilidade pelo pagamento das despesas efetuadas com a assistência médica hospitalar, e penalidade previstos na Lei.Com base nas respostas afirmativas da presente declaração de saúde, caracterizando assim lesão ou doeça pré-existente, ficam estabelecidas as coberturas parciais temporânias ou agravos estabelecidos na Proposta de Adesão.</p>";
                    $_impresso .= "<p>Declaro que as informações da declaração de saúde são a expressão da verdade, pondendo a Unimed considerá-las para análise, aceitação e manutenção das coberturas. Declaro, ainda, que estou ciente de que a omissão de informações sobre a existência de doenças ou lesões preexistentes das quais saiba ser portador(a) no momento do preenchimento deste formulário de declaração de saúde, desde que tal omissão seja comprovada junto à ANS, pode acarretar a suspensão ou o cancelamento do contrato. Neste caso, serei responsável pelo pagamento das despesas realizadas com o tratamento da doença ou lesão omitida, a partir da data em que tiver recebido comunicado ou notificação da Unimed alegando a presença de doença ou lesão preexistente não declarada.</p>";
                    $_impresso .= "<p><b>Cobertura Parcial Temporária (CPT):</b> aquela que admite, por um período initerrupto de até 24 meses, a partir da data da contratação ou adesão ao plano privado de assistência à saúde, a suspensão da cobertura de Procedimentos de Alta Complexidade (PAC), leitos de alta tecnologia e procedimentos cirúrgicos, desde que relacionados exclusivamente às doenças ou lesões preexistentes declaradas pelo beneficiário ou seu representante legal.</p>";
                    $_impresso .= "<p><b>Agravo:</b> qualquer acréscimo no valor da contraprestação paga ao plano privado de assistência à saude, para que o beneficiário tenha direito integral à cobertura contratada, para a doença ou lesão preexistente declarada, após os prazos de carências contratuais, de acordo com as condições negociadas entre a operadora e o beneficiário.</p>";
                    $_impresso .= "<div class='pagebreak'></div>";
                    $_impresso .= "<div id='topo'>";
        				$_impresso .= "<div id='titulo'>DECLARAÇÃO DE SAÚDE</div>";
        				$_impresso .= "<div id='imagem'><img src='".url('/')."/img/fama.png'/></div>";
        				$_impresso .= "<div id='numero'>REFERENTE À PROPOSTA DE ADMISSÃO <span id='alto'>N°</span><br/>Nome: ".$_pf->nome." - CPF: ".$_pf->cpf."</div><hr/>";
        			$_impresso .= "</div>";
    				$_impresso .= "<div id='alert_declaracao'>DECLARAÇÃO</div>";
    				$_impresso .= "<p>Declaro que entre as opções de categoria de plano oferecidas, indico minha opção conforme especificado nas condições gerais do presente Contrato as quais me foram apresentadas e tenho conhecimento, estou ciente que tenho 7 dias para analisá-los, desistindo dos mesmos e recebendo integralmente o valor pago, desde que não tenha utilizado qualquer serviço.</p>";
    				$_impresso .= "<p>Declaro que assumo total responsabilidade pelas informações constantes desta Proposta de Admissão, bem como declaro conhecer os termos do CONTRATO, o qual aceito sem reservas, inclusive sabendo que as carências serão contadas a partir da data do início de vigência:</p>";
    				$_impresso .= "<p>
    								a) 24 (vinte e quatro) horas para urgência e emergência;<br/>
    								b) 30 (trinta) dias para Consulta, Exames Laboratoriais Simples e Raio X Simples;<br/>
    								c) 180 (cento e oitenta) dias para os demais procedimentos;<br/>
    								d) 300 (trezentos) dias para parto a termo;<br/>
    								e) 24 meses para internações e tratamentos de doenças e lesões pré-existentes.
    							   </p>";
    			$_impresso .= "</div>";
    			$_impresso .= "<div id='data'>";
    				$_impresso .= "<p id='unimed'>Rua Rio Amapá, 374 - Conj. Vieiralves - Nossa Senhora das Graças - Manaus / AM - Tel: (92) 3003-8000 - (92) 3003-8009</p>";
    			$_impresso .= "</div>";
    		$_impresso .= "</div>";
            $_impresso .= "<div style='font-size: 13px; text-align: center'>Respondido e assinado eletronicamente por " . $dados_contato->contratos[0]->declaracao_nome . " com o usuário " . $dados_contato->contratos[0]->declaracao_login . " em " . date_format(date_create_from_format("Y-m-d H:i:s", $dados_contato->contratos[0]->declaracao_data), 'd/m/Y \à\s H:i:s') . " através do IP " . $dados_contato->contratos[0]->declaracao_ip . '</div>';
            if (($_index + 1) != $_quantidade) {
                $_impresso .= "<div class='pagebreak'></div>";
            }
    	}

        $nome_impresso = date("YmdHis") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
		file_put_contents(storage_path() . '/app/temp/' . $nome_impresso . ".html", $_impresso);

		$comando = base_path() . '/app/Http/Controllers/wkhtmltox/bin/wkhtmltopdf --footer-center [page]/[topage] ' . storage_path() . '/app/temp/' . $nome_impresso . '.html ' . storage_path() . '/app/temp/' . $nome_impresso . '.pdf';
		exec($comando);

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=declaracao_de_saude.pdf');
        @readfile(storage_path() . '/app/temp/' . $nome_impresso . '.pdf');
    }

    private function RetornaPessoasFisicasDoContrato($_codigo_do_contrato, $_apenas_beneficiarios = false) {
        if (empty($_codigo_do_contrato)) { return false; }

		if ($_apenas_beneficiarios) {
			$_apenas_beneficiarios = " and d.codigo_dependencia is not null ";
		}

		return \DB::select("select * from contratos_pessoas_fisicas c inner join pessoas_fisicas p on c.codigo_pessoa_fisica = p.codigo_pessoa_fisica left join dependencias d on d.codigo_dependencia = c.codigo_dependencia where c.codigo_contrato = ? $_apenas_beneficiarios", array($_codigo_do_contrato));
    }

    private function RetornaRespostasDaDeclaracaoDeSaude($_codigo_contrato, $_codigo_pessoa_fisica) {
		$_query = "
			SELECT DISTINCT
			    pessoas_fisicas_declaracoes_de_saude.*,
			    declaracoes_de_saude.*,
			    pessoas_fisicas.nome
			FROM
			    contratos
			        INNER JOIN
			    contratos_pessoas_fisicas ON contratos.codigo_contrato = contratos_pessoas_fisicas.codigo_contrato
			        INNER JOIN
			    pessoas_fisicas_declaracoes_de_saude ON pessoas_fisicas_declaracoes_de_saude.codigo_pessoa_fisica = contratos_pessoas_fisicas.codigo_pessoa_fisica
			        INNER JOIN
			    declaracoes_de_saude ON pessoas_fisicas_declaracoes_de_saude.codigo_declaracao_de_saude = declaracoes_de_saude.codigo_declaracao_de_saude
			        INNER JOIN
			    pessoas_fisicas ON pessoas_fisicas.codigo_pessoa_fisica = contratos_pessoas_fisicas.codigo_pessoa_fisica
			WHERE
			    contratos.codigo_contrato = ?
			        AND pessoas_fisicas.codigo_pessoa_fisica = ?
			ORDER BY pessoas_fisicas.codigo_pessoa_fisica , declaracoes_de_saude.codigo_declaracao_de_saude
		";
		return \DB::select($_query, array($_codigo_contrato, $_codigo_pessoa_fisica));
    }
}
