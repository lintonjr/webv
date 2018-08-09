<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dependencias;
use App\ContatosOnline;
use App\ContratosContatos;
use App\DeclaracoesDeSaude;
use App\TelefonesPessoaFisica;

class ContratosController extends Controller
{
    public function index() {
        $contato = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        $objcontrato = \App\Contratos::find($contato->codigo_contrato);
        $outros_contatos = \App\ContatosOnline::where("codigo_usuario", \Auth::id())->get();
        if (count($outros_contatos) == 1) {
            $outros_contatos = false;
        }
        $dados = json_decode($contato->dados);
        $status_atual = @$dados->contratos[0]->status;
        $variaveis = [
            'contato'           => $contato,
            'dados'             => $dados,
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
            'outros_contatos'   => $outros_contatos,
            'uf_permitida'      => \App\Cidades::find(session("codigo_cidade"))->uf_cidade
        ];

        if (strtotime("$contato->data_acesso + 10 days") - strtotime(date('Y-m-d H:i:s')) <= 0 AND $status_atual != "AUDITANDO" AND $status_atual != "FINALIZACAO") {
            return view('interno.foradoprazo', $variaveis);
        }

        if (@$objcontrato->status == 9) {
            return view('interno.cancelado', $variaveis);
        }

        if (empty($status_atual)) {
            /* Entra aqui se o status atual ainda é o inicial (variavel fica vazia), portanto é o de preenchimento de dados */
            if (empty($contato->nome_titular)) {
                $codigo_plano = \App\Planos::first();
                return redirect()->route('comprar-plano-get', ['codigo_plano' => $codigo_plano]);
            }
            return view('interno.contratos', $variaveis);
        } else if ($status_atual == "ENVIAR_COMPROVANTES") {
            return view('interno.comprovantes', $variaveis);
        } else if ($status_atual == "DECLARACAO_DE_SAUDE") {
            $declaracoes_de_saude = \App\DeclaracoesDeSaude::where("codigo_cidade", '6')->get();
            $variaveis['declaracoes_de_saude'] = $declaracoes_de_saude;
            return view('interno.declaracao', $variaveis);
        } else if ($status_atual == "FINALIZACAO") {
            return view('interno.finalizacao', $variaveis);
        } else if ($status_atual == "AUDITANDO") {
            $variaveis['contrato']              = \App\Contratos::where('codigo_contrato', $contato->codigo_contrato)->first();
            $variaveis['contatos']              = \App\ContratosContatos::where('codigo_contrato', $contato->codigo_contrato)->orderby('data', 'desc')->get();
            $variaveis['mensagens_nao_lidas']   = \App\ContratosContatos::where('codigo_contrato', $contato->codigo_contrato)->where('visualizado', '0')->where('origem', 'unimed')->get();
            foreach ($variaveis['mensagens_nao_lidas'] as $mensagens_nao_lidas) {
                $obj = \App\ContratosContatos::find($mensagens_nao_lidas->codigo_contrato_contato);
                $obj->visualizado = 1;
                $obj->save();
            }
            return view('interno.acompanhar', $variaveis);
        }

    }

    public function salva() {
        /* Salva todos os dados do formulário em uma variavel */
        $formdata   = \Request::all();
        /* Retorna registro do contato no banco de dados */
        $contato    = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        /* Transforma em array os dados do contato que está salvo no banco */
        $dados      = json_decode($contato->dados);
        /* Valida os dados antes de fazer qualquer coisa */
        $strerros   = "";
        $erros      = $this->ValidaDados($formdata['dados_titular']);
        if (!empty($erros)) {
            foreach ($erros as $erro) {
                $strerros .= $erro;
            }
        }
        if (@$formdata['dados_dependentes']) {
            $erros = $this->ValidaDados($formdata['dados_dependentes']);
        }
        if (!empty($erros)) {
            foreach ($erros as $erro) {
                $strerros .= $erro;
            }
        }
        if ($strerros) {
            return \Redirect::to('contratos')->withInput()->with('mensagem', $strerros);
        }
        /* Altera o array dos dados do contato para os novos dados do formulário, validando os mesmos usando o método ValidaDados */
        $dados->contratos[$formdata['codigo_contrato']]->dados_titular = $this->CorrigeDados($formdata['dados_titular']);
        if (@$formdata['dados_dependentes']) {
            $dados->contratos[$formdata['codigo_contrato']]->dados_dependentes = $this->CorrigeDados($formdata['dados_dependentes']);
        }
        /* Altera o array para o formato json afim de salvar no banco novamente */

        /* Como vai salvar, altera o status da proposta para ENVIAR_COMPROVANTES */
        $dados->contratos[$formdata['codigo_contrato']]->status = 'ENVIAR_COMPROVANTES';

        $contato->dados = json_encode($dados);
        /* Salva os dados no banco */
        $contato->save();
        return \Redirect::to('contratos')->with('mensagem', '<div class="alert alert-success">Registro salvo com sucesso!</div>');
    }

    private function ValidaDados($dados_formulario) {
        $erros = false;
        if (is_array(@$dados_formulario[0])) {
            foreach ($dados_formulario as $index => $dependente) {
                if (!\App\Geral::validaCPF($dados_formulario[$index]['cpf_dependente'])) {
                    $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. CPF inválido - '.$dados_formulario[$index]['cpf_dependente'].'.</div>';
                }
                if (!\App\Geral::validaCNS($dados_formulario[$index]['cns_dependente'])) {
                    $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. CNS inválido - '.$dados_formulario[$index]['cns_dependente'].'.</div>';
                }
                if (strlen($dados_formulario[$index]['telcelular_dependente']) != 14) {
                    $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario[$index]['telcelular_dependente'].'.</div>';
                }
                if (!empty($dados_formulario[$index]['telfixo_dependente']) && strlen($dados_formulario[$index]['telfixo_dependente']) != 13) {
                    $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario[$index]['telfixo_dependente'].'.</div>';
                }
            }
        } else {
            if (strlen($dados_formulario['telcelular_titular']) != 14) {
                $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario['telcelular_titular'].'.</div>';
            }
            if (!empty($dados_formulario['telfixo_titular']) && strlen($dados_formulario['telfixo_titular']) != 13) {
                $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. Telefone inválido. Tenha certeza de colocar o DDD corretamente. - '.$dados_formulario['telfixo_titular'].'.</div>';
            }
            if (!\App\Geral::validaCPF($dados_formulario['cpf_titular'])) {
                $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. CPF inválido - '.$dados_formulario['cpf_titular'].'.</div>';
            }
            if (!\App\Geral::validaCNS($dados_formulario['cns_titular'])) {
                $erros[] = '<div class="alert alert-danger">Erro ao salvar o registro. CNS inválido - '.$dados_formulario['cns_titular'].'.</div>';
            }
        }
        return $erros;
    }

    private function CorrigeDados($dados_formulario) {
        if (is_array(@$dados_formulario[0])) {
            /* Entra aqui se a validação é para os dependentes */
            foreach ($dados_formulario as $index => $dependente) {
                /* Corrige data de nascimento do titular do formato d/m/Y para o formato Y-m-d */
                if (!empty($dependente['nascimento_dependente'])) {
                    $dados_formulario[$index]['nascimento_dependente'] = date_format(date_create_from_format("d/m/Y", $dependente['nascimento_dependente']), "Y-m-d");
                }
                /* Corrige data de expedição do RG do titular do formato d/m/Y para o formato Y-m-d */
                if (!empty($dependente['datexpedicao_dependente'])) {
                    $dados_formulario[$index]['datexpedicao_dependente'] = date_format(date_create_from_format("d/m/Y", $dependente['datexpedicao_dependente']), "Y-m-d");
                }

                /* Formata nomes do formulário para primeira letra maiuscula */
                if (!empty($dependente['nome_dependente'])) {
                    @$dados_formulario[$index]['nome_dependente']      = trim(ucwords(mb_strtolower($dependente['nome_dependente'])));
                }
                if (!empty($dependente['mae_dependente'])) {
                    @$dados_formulario[$index]['mae_dependente']       = trim(ucwords(mb_strtolower($dependente['mae_dependente'])));
                }
                if (!empty($dependente['pai_dependente'])) {
                    @$dados_formulario[$index]['pai_dependente']       = trim(ucwords(mb_strtolower($dependente['pai_dependente'])));
                }
            }
        } else {
            /* Entra aqui se não for um array - ou seja, se a validação é para o tiular apenas */
            /* Corrige data de nascimento do titular do formato d/m/Y para o formato Y-m-d */
            if (!empty($dados_formulario['nascimento_titular'])) {
                $dados_formulario['nascimento_titular'] = date_format(date_create_from_format("d/m/Y", $dados_formulario['nascimento_titular']), "Y-m-d");
            }
            /* Corrige data de expedição do RG do titular do formato d/m/Y para o formato Y-m-d */
            if (!empty($dados_formulario['datexpedicao_titular'])) {
                $dados_formulario['datexpedicao_titular'] = date_format(date_create_from_format("d/m/Y", $dados_formulario['datexpedicao_titular']), "Y-m-d");
            }

            /* Formata nomes do formulário para primeira letra maiuscula */
            @$dados_formulario['nome_titular']      = trim(ucwords(mb_strtolower($dados_formulario['nome_titular'])));
            @$dados_formulario['mae_titular']       = trim(ucwords(mb_strtolower($dados_formulario['mae_titular'])));
            @$dados_formulario['pai_titular']       = trim(ucwords(mb_strtolower($dados_formulario['pai_titular'])));
        }
        return $dados_formulario;
    }

    public function refaz() {
        $contato = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        $outros_contatos = \App\ContatosOnline::where("codigo_usuario", \Auth::id())->get();
        if (count($outros_contatos) == 1) {
            $outros_contatos = false;
        }
        $dados = json_decode($contato->dados);
        return view('interno.refaz', compact('dados', 'contato'));
    }

    public function refazdo() {
        $contato = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
        $codigo_plano                   = $contato->codigo_plano;
        $contato->codigo_plano          = $codigo_plano;
        $contato->nome_titular          = null;
        $contato->nascimento_titular    = null;
        $contato->dados                 = null;
        $contato->valor_plano           = null;

        session(['codigo_contato' => $contato->codigo_contato]);

        $contato->delete();

        return redirect()->route('comprar-plano-get', ['codigo_plano' => $codigo_plano]);
    }

    public function comprovantes() {
        $arquivos = \Request::all();
        $extensoes_permitidas = array("pdf", "doc", "docx", "png", "jpg", "jpeg", "zip", "rar");
        if (!in_array($arquivos['dados_titular']['anexo_identificacao']->extension(), $extensoes_permitidas)) {
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
        }
        if (!in_array($arquivos['dados_titular']['anexo_residencia']->extension(), $extensoes_permitidas)) {
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
        }
        if (count(@$arquivos['dados_dependentes']) > 0) {
            foreach ($arquivos['dados_dependentes'] as $_dependente) {
                if (!in_array($_dependente['anexo_residencia']->extension(), $extensoes_permitidas)) {
                    return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
                }
                if (!in_array($_dependente['anexo_residencia']->extension(), $extensoes_permitidas)) {
                    return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
                }
            }
        }
        if (!empty(@$arquivos['dados_contratante'])) {
            if (!in_array($arquivos['dados_contratante']['anexo_identificacao']->extension(), $extensoes_permitidas)) {
                return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
            }
            if (!in_array($arquivos['dados_contratante']['anexo_residencia']->extension(), $extensoes_permitidas)) {
                return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Apenas arquivos ZIP, RAR, PDF, DOC, PNG e JPG são permitidos.</div>');
            }
        }

        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $dados_contato = json_decode($contato->dados);

        $dados_contato->contratos[0]->dados_titular->anexo_identificacao = $arquivos['dados_titular']['anexo_identificacao']->store('/public');
        $dados_contato->contratos[0]->dados_titular->anexo_residencia = $arquivos['dados_titular']['anexo_residencia']->store('/public');
        if (count(@$arquivos['dados_dependentes']) > 0) {
            foreach ($arquivos['dados_dependentes'] as $_index => $_dependente) {
                $dados_contato->contratos[0]->dados_dependentes[$_index]->anexo_identificacao = $_dependente['anexo_identificacao']->store('/public');
                $dados_contato->contratos[0]->dados_dependentes[$_index]->anexo_residencia = $_dependente['anexo_residencia']->store('/public');
            }
        }
        if (!empty(@$arquivos['dados_contratante'])) {
            $dados_contato->contratos[0]->dados_contratante->anexo_identificacao = $arquivos['dados_contratante']['anexo_identificacao']->store('/public');
            $dados_contato->contratos[0]->dados_contratante->anexo_residencia = $arquivos['dados_contratante']['anexo_residencia']->store('/public');
        }
        $dados_contato->contratos[0]->status = "DECLARACAO_DE_SAUDE";

        $contato->dados = json_encode($dados_contato);
        $contato->save();

        return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-success">Anexos enviados com sucesso!</div>');

    }

    public function declaracoes() {
        $formdata = \Request::all();
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $dados_contato = json_decode($contato->dados);

        $dados_contato->contratos[0]->dados_titular->declaracao = $formdata['dados_titular']['declaracao'];
        if (count(@$formdata['dados_dependentes']) > 0) {
            foreach ($formdata['dados_dependentes'] as $_index => $_dependente) {
                $dados_contato->contratos[0]->dados_dependentes[$_index]->declaracao = $formdata['dados_dependentes'][$_index]['declaracao'];
            }
        }

        $dados_contato->contratos[0]->status            = "FINALIZACAO";
        $dados_contato->contratos[0]->declaracao_data   = date('Y-m-d H:i:s');
        $dados_contato->contratos[0]->declaracao_login  = \App\Usuarios::where('codigo_usuario', \Auth::id())->first()->login;
        $dados_contato->contratos[0]->declaracao_nome   = \App\Usuarios::where('codigo_usuario', \Auth::id())->first()->nome_real;
        $dados_contato->contratos[0]->declaracao_ip     = \Request::ip();

        $dados_contato->contratos[0]->tipo_de_dlp       = @$formdata['tipo_de_dlp'];
        $dados_contato->contratos[0]->medico_declaracao = @$formdata['medico_declaracao'];

        if ($dados_contato->contratos[0]->medico_declaracao == "SIM") {
            /* Envia email para avisar sobre a escolha do usuário */
            $email_informativo = new \App\Mail\InformaDeclaracao();
            $email_informativo->nome = $contato->nome_titular;
            $email_informativo->email = $contato->email;
            $email_informativo->ddd = $contato->ddd;
            $email_informativo->telefone = $contato->celular;
            $email_informativo->data = date_format(date_create_from_format('Y-m-d H:i:s', $contato->data_acesso), 'd/m/Y H:i:s');
            $email_informativo->valor = number_format($contato->valor_plano, 2, ',', '.');
            $email_informativo->subject = "Declaração acompanhada por um médico - WebVendas Online";
            $email_informativo->protocolo = $dados_contato->contratos['0']->protocolo;
            \Mail::to(explode(',', env('EMAIL_INFORMA_ADM_DECLARACAO')))->bcc("wander@unimedfama.com.br")->send($email_informativo);
        }

        $contato->dados = json_encode($dados_contato);
        $contato->save();

        return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-success">Declaração de saúde preenchida com sucesso!</div>');
    }

    public function finaliza() {
        $formdata = \Request::all();
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $dados_contato = json_decode($contato->dados);
        $dados_contato->contratos[0]->dia_vencimento = $formdata['dia_vencimento'];
        $contato->dados = json_encode($dados_contato);
        $contato->save();

        if ($this->SalvaOsDadosParaOWebVendasVendedor()) {
            $dados_contato->contratos[0]->status        = "AUDITANDO";
            $dados_contato->contratos[0]->final_data    = date('Y-m-d H:i:s');
            $dados_contato->contratos[0]->final_login   = \App\Usuarios::where('codigo_usuario', \Auth::id())->first()->login;
            $dados_contato->contratos[0]->final_nome    = \App\Usuarios::where('codigo_usuario', \Auth::id())->first()->nome_real;
            $dados_contato->contratos[0]->final_ip      = \Request::ip();
            $contato->dados                             = json_encode($dados_contato);
            $contato->save();
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-success">Finalizado com sucesso!</div>');
        } else {
            return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-danger">Não foi possível realizar a integração entre sistemas.</div>');
        }
    }

    public function impresso() {
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $codigo_plano = $contato->codigo_plano;
        $dados_contato = json_decode($contato->dados);
        $impresso = \App\ImpressosContrato::where('status', 'A')->where('codigo_plano', $codigo_plano)->where('impresso_online', '1')->first();
        $texto = "";
        if (!empty($dados_contato->contratos[0]->final_nome)) {
            $texto = "Assinado eletronicamente por " . $dados_contato->contratos[0]->final_nome . " com o usuário " . $dados_contato->contratos[0]->final_login . " em " . date_format(date_create_from_format("Y-m-d H:i:s", $dados_contato->contratos[0]->final_data), 'd/m/Y \à\s H:i:s') . " através do IP " . $dados_contato->contratos[0]->final_ip;
        }
        $texto .= "<p><i>* Documento válido apenas após o pagamento da primeira mensalidade.</i></p>";
		$_impresso_contrato = str_replace("{NOME_CONTRATANTE}", $texto, $impresso->texto_impresso);
		$_nome_impresso = date("YmdHis") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
		file_put_contents(storage_path() . '/app/temp/' . $_nome_impresso . ".html", utf8_decode($_impresso_contrato));

		$_comando = base_path() . '/app/Http/Controllers/wkhtmltox/bin/wkhtmltopdf --footer-center [page]/[topage] ' . storage_path() . '/app/temp/' . $_nome_impresso . '.html ' . storage_path() . '/app/temp/' . $_nome_impresso . '.pdf';
		exec($_comando);

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=contrato.pdf');
        @readfile(storage_path() . '/app/temp/' . $_nome_impresso . '.pdf');
    }

    private function SalvaOsDadosParaOWebVendasVendedor() {

        $contato            = \App\ContatosOnline::find(session('codigo_contato'));
        $dados_contato      = json_decode($contato->dados);
        $dados_contato      = $dados_contato->contratos[0];

        $contrato           = $this->CriaContrato($contato);
        $titular            = $this->CriaTitular($contato, $dados_contato, $contrato);

        if (count(@$dados_contato->dados_dependentes) > 0) {
            foreach ($dados_contato->dados_dependentes as $_index => $dependente) {
                $this->CriaDependente($dependente, $dados_contato, $contrato, $contato, $titular);
            }
        }

        if (@$dados_contato->dados_contratante) {
            $this->CriaContratante($contato, $dados_contato, $contrato, $titular);
        }

        $contato->codigo_contrato = $contrato->codigo_contrato;
        $contato->save();

        return true;
    }

    private function CorrigeTelefone($_numero) {
        /* RECEBE STRING NO FORMATO (XX)XXXX-XXXX E RETORNA ARRAY NO FORMADO ARRAY('XX', 'XXXXXXXX') */
        $obj = new \stdClass;
        $_numero = str_replace("-", "", $_numero);
        $_numero = str_replace("(", "", $_numero);
        $_numero = explode(")", $_numero);
        $obj->ddd = $_numero[0];
        $obj->numero = $_numero[1];
        return $obj;
    }

    private function CriaContrato($contato) {
        $impresso_contrato  = \App\ImpressosContrato::where('status', 'A')->where('codigo_plano', $contato->codigo_plano)->where('impresso_online', '1')->first();
        $impresso_proposta  = \App\ImpressosProposta::where('status', 'A')->where('codigo_plano', $contato->codigo_plano)->first();
        $dados_contato      = json_decode($contato->dados);
        $dados_contato      = $dados_contato->contratos[0];

        $contrato                           = new \App\Contratos();
        $contrato->data_create              = date('Y-m-d H:i:s');
        $contrato->data_update              = date('Y-m-d H:i:s');
        $contrato->codigo_usuario_create    = $contato->codigo_usuario;
        $contrato->codigo_usuario_update    = $contato->codigo_usuario;
        $contrato->codigo_cidade            = $contato->codigo_cidade;
        $contrato->status                   = 3;
        $contrato->codigo_plano             = $contato->codigo_plano;
        $contrato->codigo_impresso_contrato = $impresso_contrato->codigo_impresso_contrato;
        $contrato->codigo_impresso_proposta = $impresso_proposta->codigo_impresso_proposta;
        $contrato->status_boleto            = "A";
        $contrato->dia_vencimento           = $dados_contato->dia_vencimento;
        $contrato->valor_boleto             = $contato->valor_plano;
        $contrato->save();
        return $contrato;
    }

    private function CriaTitular($contato, $dados_contato, $contrato) {
        // DADOS DA TABELA PRINCIPAL PESSOAS_FISICAS
        $titular                            = new \App\PessoasFisicas();
        $titular->nome                      = $dados_contato->dados_titular->nome_titular;
        $titular->nome_mae                  = $dados_contato->dados_titular->mae_titular;
        $titular->cns                       = $dados_contato->dados_titular->cns_titular;
        $titular->cpf                       = $dados_contato->dados_titular->cpf_titular;
        $titular->rg                        = $dados_contato->dados_titular->rg_titular;
        $titular->codigo_usuario_create     = $contato->codigo_usuario;
        $titular->data_create               = date('Y-m-d H:i:s');
        $titular->codigo_usuario_update     = $contato->codigo_usuario;
        $titular->data_update               = date('Y-m-d H:i:s');
        $titular->data_nascimento           = $dados_contato->dados_titular->nascimento_titular;
        $titular->sexo                      = $dados_contato->dados_titular->sexo_titular;
        $titular->email                     = $dados_contato->dados_titular->email_titular;
        $titular->orgao_expedidor           = $dados_contato->dados_titular->orgao_titular;
        $titular->uf_expedicao              = $dados_contato->dados_titular->ufexpedicao_titular;
        $titular->data_expedicao            = $dados_contato->dados_titular->datexpedicao_titular;
        $titular->nome_pai                  = $dados_contato->dados_titular->pai_titular;
        $titular->estado_civil              = $dados_contato->dados_titular->estcivil_titular;
        $titular->dnv                       = $dados_contato->dados_titular->dnv_titular;
        $titular->naturalidade              = $dados_contato->dados_titular->naturalidade_titular;
        $titular->nacionalidade             = $dados_contato->dados_titular->nacionalidade_titular;
        $titular->save();
        if (@$dados_contato->dados_contratante) {
            $contratante = "N";
        } else {
            $contratante = "S";
        }
        // DADOS DA TABELA CONTRATOS_PESSOAS_FISICAS
        $titular->contratos()->save($contrato, ['contratante' => $contratante, 'codigo_dependencia' => \App\Dependencias::where('codigo_infomed', '0')->where('status', 'A')->first(['codigo_dependencia'])->codigo_dependencia]);
        // DADOS DA TABELA TELEFONES_PESSOA_FISICA
        $telefone                       = new \App\TelefonesPessoaFisica();
        $_telefone_corrigido            = $this->CorrigeTelefone($dados_contato->dados_titular->telcelular_titular);
        $telefone->numero               = $_telefone_corrigido->numero;
        $telefone->ddd                  = $_telefone_corrigido->ddd;
        $telefone->codigo_pessoa_fisica = $titular->codigo_pessoa_fisica;
        $telefone->tipo                 = 3;
        $telefone->save();
        if (!empty($dados_contato->dados_titular->telfixo_titular)) {
            $telefone                       = new \App\TelefonesPessoaFisica();
            $_telefone_corrigido            = $this->CorrigeTelefone($dados_contato->dados_titular->telfixo_titular);
            $telefone->numero               = $_telefone_corrigido->numero;
            $telefone->ddd                  = $_telefone_corrigido->ddd;
            $telefone->codigo_pessoa_fisica = $titular->codigo_pessoa_fisica;
            $telefone->tipo                 = 1;
            $telefone->save();
        }
        // DADOS DA TABELA ENDERECOS_PESSOA_FISICA
        $endereco                                       = new \App\EnderecosPessoaFisica();
        $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_residencia;
        $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_residencia;
        $endereco->numero                               = $dados_contato->dados_titular->numero_residencia;
        $endereco->complemento                          = $dados_contato->dados_titular->complemento_residencia;
        $endereco->cep                                  = $dados_contato->dados_titular->cep_residencia;
        $return_endereco = $this->CriadorDeEnderecosNoInfomed(
            $dados_contato->dados_titular->cep_residencia,
            $dados_contato->dados_titular->municipio_residencia,
            $dados_contato->dados_titular->bairro_residencia,
            $dados_contato->dados_titular->tiplogradouro_residencia,
            $dados_contato->dados_titular->logradouro_residencia
        );
        $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
        $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
        $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_residencia;
        $endereco->codigo_pessoa_fisica                 = $titular->codigo_pessoa_fisica;
        $endereco->tipo_endereco                        = 'R';
        if (!empty($dados_contato->dados_titular->cep_correspondencia)) {
            $endereco->correspondencia                      = 'N';
        } else {
            $endereco->correspondencia                      = 'S';
        }
        $endereco->save();
        if (!$dados_contato->dados_titular->mesmo_endereco) {
            $endereco                                       = new \App\EnderecosPessoaFisica();
            $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_correspondencia;
            $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_correspondencia;
            $endereco->numero                               = $dados_contato->dados_titular->numero_correspondencia;
            $endereco->complemento                          = $dados_contato->dados_titular->complemento_correspondencia;
            $endereco->cep                                  = $dados_contato->dados_titular->cep_correspondencia;
            $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                $dados_contato->dados_titular->cep_correspondencia,
                $dados_contato->dados_titular->municipio_correspondencia,
                $dados_contato->dados_titular->bairro_correspondencia,
                $dados_contato->dados_titular->tiplogradouro_correspondencia,
                $dados_contato->dados_titular->logradouro_correspondencia
            );
            $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
            $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
            $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_correspondencia;
            $endereco->codigo_pessoa_fisica                 = $titular->codigo_pessoa_fisica;
            $endereco->tipo_endereco                        = 'C';
            $endereco->correspondencia                      = 'S';
            $endereco->save();
        }
        // DADOS DA TABELA PESSOAS_FISICAS_DECLARACOES_DE_SAUDE
        foreach ($dados_contato->dados_titular->declaracao as $index => $declaracao) {
            $titular->declaracoes()->save(\App\DeclaracoesDeSaude::find($index), ['resposta' => $declaracao->valor, 'complemento' => @$declaracao->complemento]);
        }
        // DADOS DA TABELA ANEXOS
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "S";
        $anexo->endereco                = str_replace("public", "uploads", asset($dados_contato->dados_titular->anexo_identificacao));
        $anexo->codigo_pessoa_fisica    = $titular->codigo_pessoa_fisica;
        $anexo->save();
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "RES";
        $anexo->endereco                = str_replace("public", "uploads", asset($dados_contato->dados_titular->anexo_residencia));
        $anexo->codigo_pessoa_fisica    = $titular->codigo_pessoa_fisica;
        $anexo->save();
        return $titular;
    }

    private function CriaDependente($dependente, $dados_contato, $contrato, $contato, $titular) {
        // DADOS DA TABELA PRINCIPAL PESSOAS_FISICAS
        $pessoa                            = new \App\PessoasFisicas();
        $pessoa->nome                      = $dependente->nome_dependente;
        $pessoa->nome_mae                  = $dependente->mae_dependente;
        $pessoa->cns                       = $dependente->cns_dependente;
        $pessoa->cpf                       = $dependente->cpf_dependente;
        $pessoa->rg                        = $dependente->rg_dependente;
        $pessoa->codigo_usuario_create     = $contato->codigo_usuario;
        $pessoa->data_create               = date('Y-m-d H:i:s');
        $pessoa->codigo_usuario_update     = $contato->codigo_usuario;
        $pessoa->data_update               = date('Y-m-d H:i:s');
        $pessoa->data_nascimento           = $dependente->nascimento_dependente;
        $pessoa->sexo                      = $dependente->sexo_dependente;
        $pessoa->email                     = $dependente->email_dependente;
        $pessoa->orgao_expedidor           = $dependente->orgao_dependente;
        $pessoa->uf_expedicao              = $dependente->ufexpedicao_dependente;
        $pessoa->data_expedicao            = $dependente->datexpedicao_dependente;
        $pessoa->nome_pai                  = $dependente->pai_dependente;
        $pessoa->estado_civil              = $dependente->estcivil_dependente;
        $pessoa->dnv                       = $dependente->dnv_dependente;
        $pessoa->naturalidade              = $dependente->naturalidade_dependente;
        $pessoa->nacionalidade             = $dependente->nacionalidade_dependente;
        $pessoa->save();
        $contratante = "N";
        // DADOS DA TABELA CONTRATOS_PESSOAS_FISICAS
        $pessoa->contratos()->save($contrato, ['contratante' => $contratante, 'codigo_dependencia' => \App\Dependencias::where('codigo_dependencia', $dependente->tipo_parentesco)->where('status', 'A')->first(['codigo_dependencia'])->codigo_dependencia]);
        // DADOS DA TABELA TELEFONES_PESSOA_FISICA
        $telefone                       = new \App\TelefonesPessoaFisica();
        $_telefone_corrigido            = $this->CorrigeTelefone($dependente->telcelular_dependente);
        $telefone->numero               = $_telefone_corrigido->numero;
        $telefone->ddd                  = $_telefone_corrigido->ddd;
        $telefone->codigo_pessoa_fisica = $pessoa->codigo_pessoa_fisica;
        $telefone->tipo                 = 3;
        $telefone->save();
        if (!empty($dependente->telfixo_dependente)) {
            $telefone                       = new \App\TelefonesPessoaFisica();
            $_telefone_corrigido            = $this->CorrigeTelefone($dependente->telfixo_dependente);
            $telefone->numero               = $_telefone_corrigido->numero;
            $telefone->ddd                  = $_telefone_corrigido->ddd;
            $telefone->codigo_pessoa_fisica = $pessoa->codigo_pessoa_fisica;
            $telefone->tipo                 = 1;
            $telefone->save();
        }
        // DADOS DA TABELA ENDERECOS_PESSOA_FISICA
        if ($dependente->mesmo_endereco) {
            $endereco                                       = new \App\EnderecosPessoaFisica();
            $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_residencia;
            $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_residencia;
            $endereco->numero                               = $dados_contato->dados_titular->numero_residencia;
            $endereco->complemento                          = $dados_contato->dados_titular->complemento_residencia;
            $endereco->cep                                  = $dados_contato->dados_titular->cep_residencia;
            $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                $dados_contato->dados_titular->cep_residencia,
                $dados_contato->dados_titular->municipio_residencia,
                $dados_contato->dados_titular->bairro_residencia,
                $dados_contato->dados_titular->tiplogradouro_residencia,
                $dados_contato->dados_titular->logradouro_residencia
            );
            $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
            $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
            $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_residencia;
            $endereco->codigo_pessoa_fisica                 = $pessoa->codigo_pessoa_fisica;
            $endereco->tipo_endereco                        = 'R';
            if (!empty($dados_contato->dados_titular->cep_correspondencia)) {
                $endereco->correspondencia                      = 'N';
            } else {
                $endereco->correspondencia                      = 'S';
            }
            $endereco->save();
            if (!$dados_contato->dados_titular->mesmo_endereco) {
                $endereco                                       = new \App\EnderecosPessoaFisica();
                $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_correspondencia;
                $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_correspondencia;
                $endereco->numero                               = $dados_contato->dados_titular->numero_correspondencia;
                $endereco->complemento                          = $dados_contato->dados_titular->complemento_correspondencia;
                $endereco->cep                                  = $dados_contato->dados_titular->cep_correspondencia;
                $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                    $dados_contato->dados_titular->cep_correspondencia,
                    $dados_contato->dados_titular->municipio_correspondencia,
                    $dados_contato->dados_titular->bairro_correspondencia,
                    $dados_contato->dados_titular->tiplogradouro_correspondencia,
                    $dados_contato->dados_titular->logradouro_correspondencia
                );
                $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
                $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
                $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_correspondencia;
                $endereco->codigo_pessoa_fisica                 = $pessoa->codigo_pessoa_fisica;
                $endereco->tipo_endereco                        = 'C';
                $endereco->correspondencia                      = 'S';
                $endereco->save();
            }
        } else {
            $endereco                                       = new \App\EnderecosPessoaFisica();
            $endereco->codigo_tipo_logradouro_infomed       = $dependente->tiplogradouro_residencia;
            $endereco->uf_infomed                           = $dependente->uf_residencia;
            $endereco->numero                               = $dependente->numero_residencia;
            $endereco->complemento                          = $dependente->complemento_residencia;
            $endereco->cep                                  = $dependente->cep_residencia;
            $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                $dependente->cep_residencia,
                $dependente->municipio_residencia,
                $dependente->bairro_residencia,
                $dependente->tiplogradouro_residencia,
                $dependente->logradouro_residencia
            );
            $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
            $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
            $endereco->codigo_municipio_infomed             = $dependente->municipio_residencia;
            $endereco->codigo_pessoa_fisica                 = $pessoa->codigo_pessoa_fisica;
            $endereco->tipo_endereco                        = 'R';
            if (!empty($dependente->cep_correspondencia)) {
                $endereco->correspondencia                      = 'N';
            } else {
                $endereco->correspondencia                      = 'S';
            }
            $endereco->save();
            if (!$dependente->mesmo_endereco) {
                $endereco                                       = new \App\EnderecosPessoaFisica();
                $endereco->codigo_tipo_logradouro_infomed       = $dependente->tiplogradouro_correspondencia;
                $endereco->uf_infomed                           = $dependente->uf_correspondencia;
                $endereco->numero                               = $dependente->numero_correspondencia;
                $endereco->complemento                          = $dependente->complemento_correspondencia;
                $endereco->cep                                  = $dependente->cep_correspondencia;
                $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                    $dependente->cep_correspondencia,
                    $dependente->municipio_correspondencia,
                    $dependente->bairro_correspondencia,
                    $dependente->tiplogradouro_correspondencia,
                    $dependente->logradouro_correspondencia
                );
                $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
                $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
                $endereco->codigo_municipio_infomed             = $dependente->municipio_correspondencia;
                $endereco->codigo_pessoa_fisica                 = $pessoa->codigo_pessoa_fisica;
                $endereco->tipo_endereco                        = 'C';
                $endereco->correspondencia                      = 'S';
                $endereco->save();
            }
        }
        // DADOS DA TABELA PESSOAS_FISICAS_DECLARACOES_DE_SAUDE
        foreach ($dependente->declaracao as $index => $declaracao) {
            $pessoa->declaracoes()->save(\App\DeclaracoesDeSaude::find($index), ['resposta' => $declaracao->valor, 'complemento' => @$declaracao->complemento]);
        }
        // DADOS DA TABELA ANEXOS
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "S";
        $anexo->endereco                = str_replace("public", "uploads", asset($dependente->anexo_identificacao));
        $anexo->codigo_pessoa_fisica    = $pessoa->codigo_pessoa_fisica;
        $anexo->save();
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "RES";
        $anexo->endereco                = str_replace("public", "uploads", asset($dependente->anexo_residencia));
        $anexo->codigo_pessoa_fisica    = $pessoa->codigo_pessoa_fisica;
        $anexo->save();
    }

    private function CriaContratante($contato, $dados_contato, $contrato, $titular) {
        // DADOS DA TABELA PRINCIPAL PESSOAS_FISICAS
        $contratante                            = new \App\PessoasFisicas();
        $contratante->nome                      = $dados_contato->dados_contratante->nome_contratante;
        $contratante->nome_mae                  = $dados_contato->dados_contratante->mae_contratante;
        $contratante->cns                       = $dados_contato->dados_contratante->cns_contratante;
        $contratante->cpf                       = $dados_contato->dados_contratante->cpf_contratante;
        $contratante->rg                        = $dados_contato->dados_contratante->rg_contratante;
        $contratante->codigo_usuario_create     = $contato->codigo_usuario;
        $contratante->data_create               = date('Y-m-d H:i:s');
        $contratante->codigo_usuario_update     = $contato->codigo_usuario;
        $contratante->data_update               = date('Y-m-d H:i:s');
        $contratante->data_nascimento           = $dados_contato->dados_contratante->nascimento_contratante;
        $contratante->sexo                      = $dados_contato->dados_contratante->sexo_contratante;
        $contratante->email                     = $dados_contato->dados_contratante->email_contratante;
        $contratante->orgao_expedidor           = $dados_contato->dados_contratante->orgao_contratante;
        $contratante->uf_expedicao              = $dados_contato->dados_contratante->ufexpedicao_contratante;
        $contratante->data_expedicao            = $dados_contato->dados_contratante->datexpedicao_contratante;
        $contratante->nome_pai                  = $dados_contato->dados_contratante->pai_contratante;
        $contratante->estado_civil              = $dados_contato->dados_contratante->estcivil_contratante;
        $contratante->dnv                       = $dados_contato->dados_contratante->dnv_contratante;
        $contratante->naturalidade              = $dados_contato->dados_contratante->naturalidade_contratante;
        $contratante->nacionalidade             = $dados_contato->dados_contratante->nacionalidade_contratante;
        $contratante->save();
        // DADOS DA TABELA CONTRATOS_PESSOAS_FISICAS
        $contratante->contratos()->save($contrato, ['contratante' => "S", 'codigo_dependencia' => null]);
        // DADOS DA TABELA TELEFONES_PESSOA_FISICA
        $telefone                       = new \App\TelefonesPessoaFisica();
        $_telefone_corrigido            = $this->CorrigeTelefone($dados_contato->dados_contratante->telcelular_contratante);
        $telefone->numero               = $_telefone_corrigido->numero;
        $telefone->ddd                  = $_telefone_corrigido->ddd;
        $telefone->codigo_pessoa_fisica = $contratante->codigo_pessoa_fisica;
        $telefone->tipo                 = 3;
        $telefone->save();
        if (!empty($dados_contato->dados_contratante->telfixo_contratante)) {
            $telefone                       = new \App\TelefonesPessoaFisica();
            $_telefone_corrigido            = $this->CorrigeTelefone($dados_contato->dados_contratante->telfixo_contratante);
            $telefone->numero               = $_telefone_corrigido->numero;
            $telefone->ddd                  = $_telefone_corrigido->ddd;
            $telefone->codigo_pessoa_fisica = $contratante->codigo_pessoa_fisica;
            $telefone->tipo                 = 1;
            $telefone->save();
        }
        // DADOS DA TABELA ENDERECOS_PESSOA_FISICA
        if ($dados_contato->dados_contratante->mesmo_endereco) {
            $endereco                                       = new \App\EnderecosPessoaFisica();
            $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_residencia;
            $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_residencia;
            $endereco->numero                               = $dados_contato->dados_titular->numero_residencia;
            $endereco->complemento                          = $dados_contato->dados_titular->complemento_residencia;
            $endereco->cep                                  = $dados_contato->dados_titular->cep_residencia;
            $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                $dados_contato->dados_titular->cep_residencia,
                $dados_contato->dados_titular->municipio_residencia,
                $dados_contato->dados_titular->bairro_residencia,
                $dados_contato->dados_titular->tiplogradouro_residencia,
                $dados_contato->dados_titular->logradouro_residencia
            );
            $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
            $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
            $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_residencia;
            $endereco->codigo_pessoa_fisica                 = $contratante->codigo_pessoa_fisica;
            $endereco->tipo_endereco                        = 'R';
            if (!empty($dados_contato->dados_titular->cep_correspondencia)) {
                $endereco->correspondencia                      = 'N';
            } else {
                $endereco->correspondencia                      = 'S';
            }
            $endereco->save();
            if (!$dados_contato->dados_titular->mesmo_endereco) {
                $endereco                                       = new \App\EnderecosPessoaFisica();
                $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_titular->tiplogradouro_correspondencia;
                $endereco->uf_infomed                           = $dados_contato->dados_titular->uf_correspondencia;
                $endereco->numero                               = $dados_contato->dados_titular->numero_correspondencia;
                $endereco->complemento                          = $dados_contato->dados_titular->complemento_correspondencia;
                $endereco->cep                                  = $dados_contato->dados_titular->cep_correspondencia;
                $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                    $dados_contato->dados_titular->cep_correspondencia,
                    $dados_contato->dados_titular->municipio_correspondencia,
                    $dados_contato->dados_titular->bairro_correspondencia,
                    $dados_contato->dados_titular->tiplogradouro_correspondencia,
                    $dados_contato->dados_titular->logradouro_correspondencia
                );
                $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
                $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
                $endereco->codigo_municipio_infomed             = $dados_contato->dados_titular->municipio_correspondencia;
                $endereco->codigo_pessoa_fisica                 = $contratante->codigo_pessoa_fisica;
                $endereco->tipo_endereco                        = 'C';
                $endereco->correspondencia                      = 'S';
                $endereco->save();
            }
        } else {
            $endereco                                       = new \App\EnderecosPessoaFisica();
            $endereco->codigo_tipo_logradouro_infomed       = $dados_contato->dados_contratante->tiplogradouro_correspondencia;
            $endereco->uf_infomed                           = $dados_contato->dados_contratante->uf_correspondencia;
            $endereco->numero                               = $dados_contato->dados_contratante->numero_correspondencia;
            $endereco->complemento                          = $dados_contato->dados_contratante->complemento_correspondencia;
            $endereco->cep                                  = $dados_contato->dados_contratante->cep_correspondencia;
            $return_endereco = $this->CriadorDeEnderecosNoInfomed(
                $dados_contato->dados_contratante->cep_correspondencia,
                $dados_contato->dados_contratante->municipio_correspondencia,
                $dados_contato->dados_contratante->bairro_correspondencia,
                $dados_contato->dados_contratante->tiplogradouro_correspondencia,
                $dados_contato->dados_contratante->logradouro_correspondencia
            );
            $endereco->codigo_logradouro_infomed            = $return_endereco->codigo_logradouro_infomed;
            $endereco->codigo_bairro_infomed                = $return_endereco->codigo_bairro_infomed;
            $endereco->codigo_municipio_infomed             = $dados_contato->dados_contratante->municipio_correspondencia;
            $endereco->codigo_pessoa_fisica                 = $dados_contato->dados_contratante->codigo_pessoa_fisica;
            $endereco->tipo_endereco                        = 'C';
            $endereco->correspondencia                      = 'S';
            $endereco->save();
        }
        // DADOS DA TABELA ANEXOS
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "S";
        $anexo->endereco                = str_replace("public", "uploads", asset($dados_contato->dados_contratante->anexo_identificacao));
        $anexo->codigo_pessoa_fisica    = $contratante->codigo_pessoa_fisica;
        $anexo->save();
        $anexo                          = new \App\Anexos();
        $anexo->tipo_anexo              = "RES";
        $anexo->endereco                = str_replace("public", "uploads", asset($dados_contato->dados_contratante->anexo_residencia));
        $anexo->codigo_pessoa_fisica    = $contratante->codigo_pessoa_fisica;
        $anexo->save();
        return $contratante;
    }

    private function CriadorDeEnderecosNoInfomed($cep, $municipio, $bairro, $tiplogradouro, $logradouro) {
        $obj                = new \stdClass;
        $this->webservice   = new \App\Webservice;
        $_retorno           = $this->webservice->RetornaValorWebservice("Geral", "getDadosEndereco", array($cep));
        if ($_retorno) {
            $obj->codigo_logradouro_infomed     = $_retorno[0]['LBR_CODIGO_LOGRAD'];
            $obj->codigo_bairro_infomed         = $_retorno[0]['LBR_BRR_CODIGO_BAIRRO'];
        } else {
    		$_codigo_bairro_infomed = $this->webservice->RetornaValorWebservice("Geral", "RetornaBairro", array($municipio, $bairro));
    		if (empty($_codigo_bairro_infomed)) {
    			$_codigo_bairro_infomed = $this->webservice->RetornaValorWebservice("Geral", "CriaBairro", array(mb_strtoupper($bairro), $municipio));
    			if ($_codigo_bairro_infomed[0] == "0" or empty($_codigo_bairro_infomed[1])) {
    				dd("Não foi possível criar o bairro do endereço.");
    			} else {
    				$_codigo_bairro_infomed = $_codigo_bairro_infomed[1];
    			}
    		} else {
    			$_codigo_bairro_infomed = $_codigo_bairro_infomed[0]['BRR_CODIGO_BAIRRO'];
    		}

    		if (empty($_codigo_bairro_infomed)) { dd("Não foi possível criar o bairro do endereço!"); }

    		$_codigo_nome_lograd_infomed = $this->webservice->RetornaValorWebservice("Geral", "RetornaNomeLograd", array($municipio, $logradouro));
    		if (empty($_codigo_nome_lograd_infomed)) {
    			$_codigo_nome_lograd_infomed = $this->webservice->RetornaValorWebservice("Geral", "CriaNomeLograd", array($municipio, mb_strtoupper($logradouro)));
    			if ($_codigo_nome_lograd_infomed[0] == "0" or empty($_codigo_nome_lograd_infomed[1])) {
    				dd("Não foi possível criar o nome logradouro do endereço.");
    			} else {
    				$_codigo_nome_lograd_infomed = $_codigo_nome_lograd_infomed[1];
    			}
    		} else {
    			$_codigo_nome_lograd_infomed = $_codigo_nome_lograd_infomed[0]['NML_CODIGO_NOME'];
    		}

    		if (empty($_codigo_nome_lograd_infomed)) { dd("Não foi possivel criar o nome do logradouro."); }

    		$_codigo_logradouro = $this->webservice->RetornaValorWebservice("Geral", "CriaLogradouro", array($_codigo_nome_lograd_infomed, $tiplogradouro, $_codigo_bairro_infomed, $cep));

    		if ($_codigo_logradouro[0] == "0") {
    			dd("Não foi possível criar o logradouro.");
    		}

            $obj->codigo_logradouro_infomed     = $_codigo_logradouro[1];
            $obj->codigo_bairro_infomed         = $_codigo_bairro_infomed;
        }
        return $obj;
    }

}
