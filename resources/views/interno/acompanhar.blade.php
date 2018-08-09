<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Acompanhe sua proposta</h1>
            <h2>Saiba o status e maiores informações da sua proposta. Protocolo de contratação: {{$dados->contratos[0]->protocolo}}</h2>
        </hgroup>

        @if ($outros_contatos)
        <div class="alert alert-info alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Foi detectado que você possui outras contratações neste usuário. Alterne de contrato aqui.
            <ul>
            @foreach ($outros_contatos as $outro_contato)
                <li class='outros_contatos {{($outro_contato->codigo_contato == $contato->codigo_contato ? 'mesmo-contato' : '')}}'><a href='{{route("altera-contato", ["codigo_contato" => $outro_contato->codigo_contato])}}'>@php echo '#' . ($loop->index + 1) . ' - Titular: ' . $outro_contato->nome_titular; @endphp</a></li>
            @endforeach
            </ul>
        </div>
        @endif

        @if ($contrato->status != '4')
            <div class="alert alert-warning alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Fique atento às alterações nesta tela. Esta tela serve para acompanhamento do status de sua proposta, assim como impressão dos arquivos necessários para procedência de sua contratação, como o boleto e NFS-e.
            </div>
        @endif
        @if (count($mensagens_nao_lidas))
            <div class="alert alert-warning alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Você possui mensagens não lidas. <a href='#contatos-acompanhamento'>Clique aqui</a> para visualizá-las.
            </div>
        @endif

        @php $dados_titular = $dados->contratos[$codigo_contrato]->dados_titular; @endphp
        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        @if (@$dados->contratos[0]->tipo_de_dlp and $dados->contratos[0]->tipo_de_dlp == "agravo")
        <div class="row">
            <div class="col-md-12">
                <div class="well expand-box" style="background-color: #fcf8e3; border-color: #faebcc">
                    <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Contratação Presencial</h3>
                    <p>Você optou pela contratação do agravo. Portanto, procure-nos em uma de nossas unidades de atendimento para continuidade do processo de contratação de plano de saúde. Nos apresente seu protocolo: {{$dados->contratos[0]->protocolo}}.</p>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Matriz Manaus:</b><br/>Rua Rio Amapá, 374 - Nossa Sra. das Graças - Manaus-AM - CEP: 69053-150 / Fone: (92) 3303-8000 e 3303-8003</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Belém:</b><br/>Trav. Humaitá, 2778 - Marco - Belém-PA - CEP: 66095-220 / Fone: (91) 3344-0802</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Macapá:</b><br/>Rua Mendonça Furtado, 2278 - Sala B - Santa Rita - Macapá-AP - CEP: 68900-060 / Fone: (96) 3223-3646</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><B>Filial Boa Vista:</b><br/>Rua Coronel Mota, 1668 - Centro - CEP: 69301-120 / Fone: (95) 3198-2618</div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12 ">
                <div class="well">
                    <h3><i class="fa fa-cogs" aria-hidden="true"></i> Status da Contratação:</h3>

                    @php
                        switch($contrato->status) {
                            case 1:
                                echo "";
                                break;
                            case 3:
                                echo "<strong>Contrato em auditoria</strong> - Nesta etapa, seus dados estão sendo analizados por nossos audotires. Aguarde.";
                                break;
                            case 4:
                                echo "<strong>Plano disponível para uso</strong> - Você já pode utilizar seu plano através do cartão provisório imediatamente.";
                                break;
                            case 5:
                            case 6:
                            case 7:
                            case 8:
                                echo "<strong>Auditoria realizada</strong> - A auditoria já foi realizada e seus dados foram comprovados. Estamos emitindo documentos fiscais e muito em breve você terá acesso ao seu plano.";
                                break;
                            default:
                                echo "Erro ao encontrar o status do seu pedido.";
                        }
                    @endphp

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="well">
                    <h3><i class="fa fa-barcode" aria-hidden="true"></i> Status do Boleto:</h3>

                    @php
                        switch($contrato->status_boleto) {
                            case 'A':
                                echo "<strong>Boleto em emissão</strong> - Seu boleto ainda não foi emitido. Em breve você terá acesso aqui mesmo no portal.";
                                break;
                            case 'G':
                                echo "<a href='".$contrato->boleto_html."' target='_blank'><strong>Boleto em aberto</strong> - Clique aqui para ter acesso ao seu boleto.</a>";
                                break;
                            case 'P':
                                echo "<strong>Boleto pago</strong> - Nosso sistema já recebeu a confirmação de pagamento do seu boleto.";
                                break;
                            case 'C':
                                echo "<strong>Boleto cancelado</strong> - Entre em contato conosco para maiores informações.";
                                break;
                            default:
                                echo "Erro ao encontrar o status do seu boleto.";
                        }
                    @endphp

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="well">
                    <h3><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Documentos da Proposta</h3>
                    <ul class='listagem-documentos'>
                        <!--<li><a href="/documentos/carta_de_orientacao_ao_beneficiario.pdf" target="_blank">Carta de Orientação ao Beneficiário</a></li>-->
                        <li><a href="{{route('modelo-carta')}}" target="_blank">Carta de Orientação ao Beneficiário</a></li>
                        <li><a href="/documentos/manual_de_orientacao_para_contratacao_de_planos_de_saude.pdf" target="_blank">Manual de Orientação para Contratação de Planos de Saúde</a></li>
                        <li><a href="{{route('modelo-contrato')}}" target="_blank">Contrato de Plano Individual ou Familiar</a></li>
                        <li><a href="{{route('modelo-proposta')}}" target="_blank">Proposta de Admissão</a></li>
                        <li><a href="{{route('modelo-declaracao')}}" target="_blank">Declaração de Saúde</a></li>
                        @if (@$dados->contratos[0]->tipo_de_dlp and $dados->contratos[0]->tipo_de_dlp == "cpt")
                            <li><a href="{{route('modelo-cpt')}}" target="_blank">Termo de Aceitação da Cobertura Parcial Temporária - CPT</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="well">
                    <h3><i class="fa fa-file-text-o" aria-hidden="true"></i> NFS-e</h3>
                    @if (!$contrato->numero_nfse)
                        <p>NFS-e ainda não está disponível.</p>
                    @else
                        <a href="{{route('imprimir-nfse')}}" target="_blank">Clique aqui para imprimir a NFS-e</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="well">
                    <h3><i class="fa fa-id-card-o" aria-hidden="true"></i> Cartão Provisório</h3>
                    @if ($contrato->status != 4)
                        <p>Cartão provisório ainda não está disponível.</p>
                    @else
                        <a href="{{route('cartao-provisorio')}}" target="_blank">Clique aqui para imprimir o cartão provisório</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="well expand-box">
                    <h3><i class="fa fa-comments-o" aria-hidden="true"></i> Formulário de contato com a Unimed</h3>
                    <div class="col-md-6 no-padding">
                        <form action="{{route('salva-contato')}}" enctype="multipart/form-data" method="POST">
                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                            <div class="col-md-12 no-padding">
                                <div class="form-group">
                                    <label>Assunto <span class="obrigatorio">*</span></label>
                                    <input required type='text' class='form-control' name='assunto'/>
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <div class="form-group">
                                    <label>Anexo</label>
                                    <input type='file' name='anexo'/>
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <div class="form-group">
                                    <label>Mensagem <span class="obrigatorio">*</span></label>
                                    <textarea required class='form-control' name='mensagem' rows='10'></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12 no-padding">
                                <button type="submit" class="btn btn-success">Enviar contato</button>
                            </div>
                        </form>
                    </div>
                    @if (count($contatos))
                    <div class="col-md-12 no-padding">
                        <div class="contatos-acompanhamento" id="contatos-acompanhamento">
                            <h4><i class="fa fa-indent"></i> Mensagens anteriores</h4><hr/>
                            @forelse ($contatos as $contato)
                                <div class="contatos-single expand-box">
                                    <div class='col-md-2 no-padding'>
                                        <label>Origem:</label>
                                        <span>{{ucwords($contato->origem)}}</span>
                                    </div>
                                    <div class='col-md-3 no-padding'>
                                        <label>Data:</label>
                                        <span>{{date_format(date_create_from_format('Y-m-d H:i:s', $contato->data), 'd/m/Y \à\s H:i:s')}}</span>
                                    </div>
                                    <div class='col-md-7 no-padding'>
                                        <label>Anexo:</label>
                                        <span>{!!(($contato->endereco_anexo) ? "<a href='$contato->endereco_anexo' target='_blank'>Baixar</a>" : "")!!}</span>
                                    </div>
                                    <div class='col-md-12 no-padding'>
                                        <label>Assunto:</label>
                                        <span>{{$contato->assunto}}</span>
                                    </div>
                                    <div class='col-md-12 no-padding'>
                                        <label>Mensagem:</label>
                                        <span>{{$contato->mensagem}}</span>
                                    </div>
                                </div>
                            @empty

                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center">
                @php
                    $agora = time();
                    $data_final = strtotime($dados->contratos[0]->final_data);
                    $datediff = $agora - $data_final;

                    $dias_desde_a_finalizacao = floor($datediff / (60 * 60 * 24));
                @endphp
                @if ($dias_desde_a_finalizacao <= 7)
                    <button class="btn btn-danger" id="solicitar-cancelamento">Solicitar cancelamento da proposta</button>
                @endif
                <p class='atualizado-em mg-t10'>* Você fez a solicitação de contratação há {{$dias_desde_a_finalizacao}} dias. Você possui direito de arrependimento da contratação por até 7 dias.</p>
            </div>
            <div class="col-md-12" id="motivo-de-cancelamento" style="display: none">
                <div class="well expand-box">
                    <h3><i class="fa fa-times-circle" aria-hidden="true"></i> Motivo de Cancelamento</h3>
                    <p>Por favor, informe o motivo de cancelamento da contratação:</p>
                    <form action='{{route("salva-cancelamento")}}' method='POST'>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div><input type='radio' name='motivo_cancelamento' value='1'/> Desejo alterar a proposta (alteração de produto, inclusão/exclusão de pessoas, alteração de dados)</div>
                        <div><input type='radio' name='motivo_cancelamento' value='2'/> Encontrei oferta melhor na concorrência</div>
                        <div><input type='radio' name='motivo_cancelamento' value='3'/> Ingressarei em um plano empresarial</div>
                        <div><input type='radio' name='motivo_cancelamento' value='4'/> Desejo ficar sem plano de saúde</div>
                        <div><input type='submit' class='btn btn-success mg-t15' value='Solicitar Cancelamento'/></div>
                    </form>
                </div>
            </div>
        </div>
     </div>
     <br/>
    </main>
    @include('templates.footer')
</body>
</html>
