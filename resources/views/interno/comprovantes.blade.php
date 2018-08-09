<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Anexos do Contrato</h1>
            <h2>Anexe os comprovantes dos dados do seu cadastro. Ao enviar seus documentos, certifiue-se da legibilidade e dos formatos de cada um. Só serão aceitos os arquivos em ZIP, RAR, PDF, DOC, DOCX, JPG, JPEG ou PNG. Protocolo de contratação: {{$dados->contratos[0]->protocolo}}</h2>
        </hgroup>
        <form method='POST' enctype='multipart/form-data' action='{{route("salva-comprovantes")}}' id='formulario_contrato_principal'>
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
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

        <div class="alert alert-warning alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Você tem até o dia {{$prazo}} para preencher todos os dados
        </div>

        @php $dados_titular = $dados->contratos[$codigo_contrato]->dados_titular; @endphp
        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Se o pagador do plano não for o titular, você deve alterá-lo no menu Alterar pagador do plano. Caso não faça agora, consideraremos que o pagador do plano é o titular e você não poderá alterar posteriormente.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="alert well alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" style="top: -10px; right: 0px">&times;</a>
                    <h3 style="float: left; padding: 5px 10px;"><i class="fa fa-info-circle" aria-hidden="true"></i></h3>
                    <p>Os documentos necessários são as cópias do Registro Geral (RG), CPF e comprovante de residência. No caso de uma pessoa menor de idade devem ser apresentados a certidão de nascimento e o CPF da criança ou do jovem; e o RG, CPF e comprovante de residência da pessoa responsável por ele.</p>
                    <p>Se a pessoa quiser incluir outros dependentes no contrato, deverá apresentar cópias da certidão de casamento, carteira de identidade e CPF (se o dependente for o cônjuge); ou fotocópia do registro de nascimento (se o dependente for um menor de idade); além dos documentos já citados necessários para o titular do plano.</p>
                </div>
            </div>
        </div>

        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                            <span class="nomBeneficiario">{{$dados_titular->nome_titular}}</span>
                            <span class="tipBeneficiario">Titular</span>
                        </a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Documentos de identificação <i class="duvida fa fa-question-circle" rel="tooltip" title="Qualquer tipo de documento de identificação. Obrigatoriamente um documento de identificação com foto, tais como Identifidade, carteira nacional de habilitação, carteira de trabalho ou carteira profissional. Para dependentes, deve-se anexar também documentos que comprovem o tipo de parentesco com o titular do plano." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_titular[anexo_identificacao]' required/>
                            </div>
                        </div>
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Comprovante de Residência <i class="duvida fa fa-question-circle" rel="tooltip" title="O comprovante deverá conter endereço completo (rua, nº, bairro, cidade e CEP quando houver). O documento precisa ter sido emitido nos últimos 90 dias da data de envio." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_titular[anexo_residencia]' required/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php $contador = 1; @endphp
            @forelse ($dados->contratos[$codigo_contrato]->dados_dependentes as $dependente)
            @php $contador++; @endphp
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$contador}}">
                            <span class="nomBeneficiario">{{$dependente->nome_dependente}}</span>
                            <span class="tipBeneficiario">Dependente</span>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{$contador}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Documentos de identificação <i class="duvida fa fa-question-circle" rel="tooltip" title="Qualquer tipo de documento de identificação. Obrigatoriamente um documento de identificação com foto, tais como Identifidade, carteira nacional de habilitação, carteira de trabalho ou carteira profissional. Para dependentes, deve-se anexar também documentos que comprovem o tipo de parentesco com o titular do plano." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_dependentes[{{$loop->index}}][anexo_identificacao]' required/>
                            </div>
                        </div>
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Comprovante de Residência <i class="duvida fa fa-question-circle" rel="tooltip" title="O comprovante deverá conter endereço completo (rua, nº, bairro, cidade e CEP quando houver). O documento precisa ter sido emitido nos últimos 90 dias da data de envio." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_dependentes[{{$loop->index}}][anexo_residencia]' required/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <!-- Não há dependentes neste contrato -->
            @endforelse

            @if (@$dados->contratos[$codigo_contrato]->dados_contratante)
            @php $dados_contratante = $dados->contratos[$codigo_contrato]->dados_contratante; @endphp
            @php $contador++; @endphp
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$contador}}">
                            <span class="nomBeneficiario">{{$dados_contratante->nome_contratante}}</span>
                            <span class="tipBeneficiario">Contratante</span>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{$contador}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Documentos de identificação <i class="duvida fa fa-question-circle" rel="tooltip" title="Qualquer tipo de documento de identificação. Obrigatoriamente um documento de identificação com foto, tais como Identifidade, carteira nacional de habilitação, carteira de trabalho ou carteira profissional. Para dependentes, deve-se anexar também documentos que comprovem o tipo de parentesco com o titular do plano." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_contratante[anexo_identificacao]' required/>
                            </div>
                        </div>
                        <div class="row mg-t10 mg-b10">
                            <div class="col-md-5">
                                <i class="fa fa-file-image-o icone-imagens" aria-hidden="true"></i>
                                Comprovante de Residência <i class="duvida fa fa-question-circle" rel="tooltip" title="O comprovante deverá conter endereço completo (rua, nº, bairro, cidade e CEP quando houver). O documento precisa ter sido emitido nos últimos 90 dias da data de envio." aria-hidden="true"></i>
                            </div>
                            <div class="col-md-7">
                                <input type='file' name='dados_contratante[anexo_residencia]' required/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="form-group">
            <button type="button" class="btn btn-default" onclick="window.history.go(-1); return false;">Voltar</button>
            <button type="button" class="btn btn-success" id="continuar_formulario_contratos">Continuar</button>
            <button type="submit" class="hide"></button>
        </div>
    </form>
    </main>
    @include('templates.footer')
</body>
</html>
