<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Finalização da Contratação</h1>
            <h2>Passos finais para a contratação do plano. Protocolo de contratação: {{$dados->contratos[0]->protocolo}}</h2>
        </hgroup>
        @if ($outros_contatos)
        <div class="alert alert-info alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Foi detectado que você possui outras contratações neste usuário. Alterne de contrato aqui.
            <ul>
            @foreach ($outros_contatos as $outro_contato)
                <li class='outros_contatos'><a href='{{route("altera-contato", ["codigo_contato" => $outro_contato->codigo_contato])}}'>@php echo '#' . ($loop->index + 1) . ' - Titular: ' . $outro_contato->nome_titular; @endphp</a></li>
            @endforeach
            </ul>
        </div>
        @endif

        @include('templates.menuadicional')


        <form action='{{route("finalizacao-salva")}}' method='POST'>
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-md-12">
                Após a finalização do seu contrato e auditoria dos seus dados, você terá acesso ao 1º boleto com vencimento em {{env('DIAS_VENCIMENTO_BOLETO')}} dia(s). O seu plano é em pré-pagamento, sendo assim, a partir do 2º boleto, seu vencimento será de acordo com a data escolhida nas opções abaixo.
            </div>
        </div>
        <div class="row dia-vencimento">
            <div class="col-md-4">
                <div>
                    <input type="radio" id="control_01" name="dia_vencimento" value="10" required checked>
                    <label for="control_01">
                        <h2>10</h2>
                    </label>
                </div>
                <div>
                    <input type="radio" id="control_02" name="dia_vencimento" value="20">
                    <label for="control_02">
                        <h2>20</h2>
                    </label>
                </div>
                <div>
                    <input type="radio" id="control_03" name="dia_vencimento" value="30">
                    <label for="control_03">
                        <h2>30</h2>
                    </label>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <h3>Documentos Importantes</h3>
                <p>Os documentos abaixo possuem todas as informações referentes a sua contratação. Baixe aqui esses documentos:</p>
            </div>
        </div>
        <div class="row documentos">
            <div class="col-md-12">
                <a href="/documentos/manual_de_orientacao_para_contratacao_de_planos_de_saude.pdf" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Manual de Orientação para Contratação de Planos de Saúde</a>
            </div>
            <div class="col-md-12">
                <a href="{{route('modelo-contrato')}}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Modelo do Contrato de Plano de Saúde</a>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <h3>Resumo da sua contratação</h3>
            </div>
            @php
                $resumo = json_decode($contato->dados);
                $resumo = $resumo->contratos[0];
            @endphp
            <div class="col-md-12 resumo">
                <div class="titulo col-md-3">Valor do plano</div>                <div class="valor col-md-9">R$ {{number_format($contato->valor_plano, 2, ',', '.')}}</div>
                <div class="titulo col-md-3">Plano</div>                         <div class="valor col-md-9">{{$plano->nome_plano}} - Registro: {{$plano->registro_ans}}</div>
                <div class="titulo col-md-3">Abrangência / Acomodação</div>      <div class="valor col-md-9">{{$plano->abrangencia}} / {{$plano->acomodacao}}</div>
                <div class="titulo col-md-3">Titular</div>                       <div class="valor col-md-9">{{$resumo->dados_titular->nome_titular}}</div>
                @if (!empty($resumo->dados_dependentes))
                    @foreach ($resumo->dados_dependentes as $dependente)
                        <div class="titulo col-md-3">Dependente</div>            <div class="valor col-md-9">{{$dependente->nome_dependente}}</div>
                    @endforeach
                @endif
                @if (!empty($resumo->dados_contratante))
                    <div class="titulo col-md-3">Contratante</div>               <div class="valor col-md-9">{{$resumo->dados_contratante->nome_contratante}}</div>
                @endif
            </div>
        </div>
        <hr/>
        <div class="row termos">
            <div class="col-md-12">
                <h3>Termos e Condições da Contratação</h3>
            </div>
            <div class="col-md-12">
                <div class="box-termos">
                    <!-- Box dos Termos -->
                    <strong>TERMOS E CONDIÇÕES DA CONTRATAÇÃO</strong>

                    <ul>
                        <li>Sua proposta está sob análise da operadora;</li>
                        <li>Se toda a documentação remetida estiver correta, em breve você terá acesso neste portal a todas as informações de confirmação acerca da sua contratação e, nesse momento, seu contrato estará vigente, para início da contagem dos prazos de carência;</li>
                        <li>O pagamento do boleto bancário é imprescindível para confirmar o seu interesse nessa contratação e, pode ser pago até a data de vencimento nele consignada. Caso não ocorra o pagamento, esta operadora entenderá que você desistiu da contratação em curso;</li>
                        <li>Se tiver havido utilização dos serviços nesse período, a operadora lhe encaminhará cobrança dos custos dos atendimentos/procedimentos realizados;</li>
                        <li>Você pode exercer o direito de arrependimento em até 07 (sete) dias contados do pagamento do boleto bancário, por intermédio de formulário disponibilizado no espaço cliente, salientando esta operadora que:</li>
                        <li>Se tiver havido utilização dos serviços nesse período, a operadora lhe encaminhará cobrança dos custos dos atendimentos/procedimentos realizados;</li>
                        <li>A cobertura do seu contrato é limitada ao Rol da ANS - Agência Nacional de Saúde Suplementar;</li>
                        <li>Seu contrato possui carências que devem ser cumpridas por você e pelos eventuais beneficiários dependentes;</li>
                        <li>Seu contrato possui área determinada para a prestação dos serviços e, uma rede credenciada, que deve ser observada;</li>
                    </ul>
                    <!-- Box dos Termos -->
                </div>
                <br/>
                <input required type='checkbox' name='aceite_termos' value='1'/> Declaro que estou ciente e de acordo com os Termos e Condições de Contratação
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success mg-t10">Continuar</button>
        </div>
    </form>
    </main>
    @include('templates.footer')
</body>
</html>
