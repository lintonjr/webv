<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Declaração de Saúde</h1>
            <h2>Preencha corretamente a declaração de saúde dos beneficiários do seu contrato. Protocolo de contratação: {{$dados->contratos[0]->protocolo}}</h2>
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

        @php $x = 1; @endphp

        <div class="alert alert-documento alert-dismissable" style="overflow: auto;">
            <div style='text-align: left; color: #000'>
                <p style="font-family:Calibri; text-align: center;">
                    <font color="#9ACD32" style="font-size: 18pt">
                        <b>CARTA DE ORIENTAÇÃO AO BENEFICIÁRIO </b>
                    </font>
                </p>
                <p>Prezado(a) Beneficiário(a), </p>
                <p>A <b>Agência Nacional de Saúde Suplementar (ANS)</b>, instituição que regula as atividades das operadoras de planos de assistência à saúde e que tem como missão defender o interesse público, vem, por meio desta Carta de Orientação, prestar informações para o preenchimento da DE DECLARAÇÃO DE SAÚDE.</p>

                <div style="border: 1px solid #9ACD32; padding: 20px">
                    <p><b>1 - O QUE É A DECLARAÇÃO DE SAÚDE? </b> </p>

                    <p>É o formulário que acompanha o contrato do Plano de Saúde, onde o beneficiário ou seu representante legal deverá informar as doenças ou lesões preexistentes de que saiba ser portador ou sofredor no momento da contratação do plano. Para o seu preenchimento, o beneficiário tem o direito de ser orientado, gratuitamente, por um médico credenciado/referenciado pela operadora. Se optar por um profissional de sua livre escolha, assumirá o custo desta opção, portanto, se você (beneficiário) toma medicamentos regularmente, consulta médicos por problema de saúde do qual conhece o diagnóstico, fez qualquer exame que identificou alguma doença ou lesão, esteve internado ou submeteu-se a alguma cirurgia, DEVE DECLARAR ESTA DOENÇA OU LESÃO.</p>
                </div>
                <p>&nbsp;</p>
                <div style="border: 1px solid #9ACD32; padding: 20px">
                    <p><b>2 - AO DECLARAR DOENÇA E/OU LESÃO DE QUE O BENEFICIÁRIO SAIBA SER PORTADOR NO MOMENTO DA CONTRATAÇÃO:</b> </p>
                    <ul>
                        <li>2.1 - A operadora NÃO poderá impedi-lo de contratar o plano desaúde. Caso isto ocorra, encaminhe a denúncia à ANS. </li>
                        <li>2.2 - A operadora deverá oferecer: cobertura total ou COBERTURA PARCIAL TEMPORÁRIA (CPT), podendo ainda oferecer o Agravo, que é um acréscimo no valor da mensalidade, pago ao plano privado de assistência à saúde, para que se possa utilizar toda a cobertura contratada, após os prazos de carências contratuais. </li>
                        <li>2.3 - No caso de CPT, haverá restrição de cobertura para cirurgias, leitos de alta tecnologia (UTI, unidade coronariana ou neonatal) e procedimentos de alta complexidade - PAC (tomografia, ressonância, tratamento radiológico, etc.) EXCLUSIVAMENTE relacionados à doença ou lesão declarada, até 24 meses contados desde assinatura do contrato. Após esse período, a cobertura passará a ser integral de acordo com o plano contratado. </li>
                        <li>2.4 - NÃO haverá restrições de cobertura para consultas médicas, internações não cirúrgicas, exames e procedimentos que não sejam de alta complexidade, mesmo que relacionados à doença ou lesão preexistente declarada, desde que cumpridos os prazos de carências estabelecidas no contrato. <p></p></li>
                        <li>2.5 - Não caberá alegação posterior de omissão de informação da Declaração de Saúde por parte da operadora para esta doença ou lesão.</li>
                    </ul>
                </div>
                <p>&nbsp;</p>
                <div style="border: 1px solid #9ACD32; padding: 20px">
                    <p><b>3 - AO NÃO DECLARAR DOENÇA E/OU LESÃO DE QUE O BENEFICIÁRIOSAIBA SER PORTADOR NO MOMENTO DA CONTRATAÇÃO:</b> </p>
                    <ul>
                        <li>3.1 A operadora poderá suspeitar de omissão de informação e neste caso, deverá comunicar imediatamente ao beneficiário, podendo oferecer CPT ou solicitar abertura de processo administrativo junto à ANS, denunciando a omissão da informação. </li>
                        <li>3.2 Comprovada a omissão de informação pelo beneficiário, a operadora poderá RESCINDIR o contrato por FRAUDE e responsabilizá-lo pelos procedimentos referentes a doença ou lesão não declarada.</li>
                        <li>3.3 Até o julgamento final do processo pela ANS, NÃO poderá ocorrer suspensão do atendimento nem rescisão do contrato. Caso isto ocorra, encaminhe a denúncia à ANS.</li>
                    </ul>
                </div>
                <p>&nbsp;</p>
                <div style="border: 1px solid #9ACD32; padding: 20px">
                    <p><b>ATENÇÃO!</b> </p>
                    <p>Se a operadora oferecer redução ou isenção de carência, isto não significa que dará cobertura assistencial para doença ou lesão que o beneficiário saiba ter no momento da assinatura contratual. Cobertura Parcial Temporária - CPT - NÃO é carência! Portanto, o beneficiário não deve deixar de informar se possui alguma doença ou lesão ao preencher o Formulário de Declaração de Saúde!</p>
                </div>

                <p>&nbsp;</p>
                <p>OBSERVAÇỖES: Para consultar a lista completa de procedimentos de alta complexidade - PAC acesse o Rol de Procedimentos e Eventos em Saúde da ANS no endereço eletrônico: www.ans.gov.br - Perfil Beneficiário.<br> Em caso de dúvidas, entre em contato com a ANS pelo telefone 0800-701-9656 ou consulte a página da ANS - www.ans.gov.br - Perfil Beneficiário.</p>
            </div>

            <a href="#" class="close" data-dismiss="alert" aria-label="close"><span>Li e estou de acordo com os termos</span></a>

        </div>

        <form method='POST' enctype='multipart/form-data' action='{{route("salva-declaracao")}}' id='formulario_contrato_principal'>
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
        <div class="panel-group hidden" id="accordion">
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
                        @foreach ($declaracoes_de_saude as $declaracao_de_saude)
                            <div class="row">
                                <div class="col-md-10">
                                    {{$declaracao_de_saude->pergunta}}
                                </div>
                                <div class="col-md-2 mg-t10">
                                    <!-- RADIO SIM/NAO -->
                                    <div class="toggle-radio" id="toggle-radio{{$x}}">
                                        <input type="radio" name="dados_titular[declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][valor]" class="yes" id="yes{{$x}}" value="S"/>
                                        <input type="radio" name="dados_titular[declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][valor]" class="no" id="no{{$x}}" value="N"/>
                                        <div class="switch">
                                            <label for="yes{{$x}}" id="toggle-radio{{$x}}-yes">Sim</label>
                                            <span class="toggle-radio{{$x}}"><i class="fa fa-question" aria-hidden="true"></i></span>
                                            <label for="no{{$x}}" id="toggle-radio{{$x}}-no">Não</label>
                                        </div>
                                    </div>
                                    <!-- RADIO SIM/NAO -->
                                </div>
                                @if ($declaracao_de_saude->complemento_obrigatorio == "1")
                                    <div class="col-md-12 mg-t10 hidden" id="toggle-radio{{$x}}-complemento">
                                        <input maxlength="45" class='form-control' type='text' name='dados_titular[declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][complemento]' placeholder='Especifique...'/>
                                    </div>
                                @endif
                            </div>
                            <hr/>
                            @php $x++ @endphp
                        @endforeach
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
                        @foreach ($declaracoes_de_saude as $declaracao_de_saude)
                            <div class="row">
                                <div class="col-md-10">
                                    {{$declaracao_de_saude->pergunta}}
                                </div>
                                <div class="col-md-2 mg-t10">
                                    <!-- RADIO SIM/NAO -->
                                    <div class="toggle-radio" id="toggle-radio{{$x}}">
                                        <input type="radio" name="dados_dependentes[{{$loop->parent->index}}][declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][valor]" class="yes" id="yes{{$x}}" value="S"/>
                                        <input type="radio" name="dados_dependentes[{{$loop->parent->index}}][declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][valor]" class="no" id="no{{$x}}" value="N"/>
                                        <div class="switch">
                                            <label for="yes{{$x}}" id="toggle-radio{{$x}}-yes">Sim</label>
                                            <span class="toggle-radio{{$x}}"><i class="fa fa-question" aria-hidden="true"></i></span>
                                            <label for="no{{$x}}" id="toggle-radio{{$x}}-no">Não</label>
                                        </div>
                                    </div>
                                    <!-- RADIO SIM/NAO -->
                                </div>
                                @if ($declaracao_de_saude->complemento_obrigatorio == "1")
                                    <div class="col-md-12 mg-t10 hidden" id="toggle-radio{{$x}}-complemento">
                                        <input maxlength="45" class='form-control' type='text' name='dados_dependentes[{{$loop->parent->index}}][declaracao][{{$declaracao_de_saude->codigo_declaracao_de_saude}}][complemento]' placeholder='Especifique...'/>
                                    </div>
                                @endif
                            </div>
                            <hr/>
                            @php $x++ @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <!-- Não há dependentes neste contrato -->
            @endforelse
            <br/>
            <div class="well">
                <input type='radio' name='medico_declaracao' value='NAO' checked='checked'/> Dispenso a presença de um médico para preencher minha declaração de saúde.<br/>
                <input type='radio' name='medico_declaracao' value='SIM'/> Desejo que minha declaração de saúde seja avaliada junto a um medico. <i class="duvida fa fa-question-circle" data-toggle="tooltip" rel="tooltip" title="Você será solicitado a confirmar os dados da declaração de saúde na presença de um médico especialista. Nós entraremos em contato com você disponibilizando três horários diferentes para essa entrevista." aria-hidden="true"></i> <span style='color: #b7b3b3; font-style: italic; margin-left: 10px; font-size: 13px;'>Opcional</span>
            </div>

            <div class="form-group" style='margin-top: 10px'>
                <button type="button" class="btn btn-success" id="declaracao_de_saude">Continuar</button>
            </div>
        </div>

        <div class="alert alert-documento alert-dismissable hide" id='declaracao-de-saude-final'>
            <div style='text-align: left; color: #000; padding: 10px; text-align: justify; overflow: auto'>

                <p>Declaro que as informações da declaração de saúde são a expressão da verdade, podendo a Unimed considerá-las para análise, aceitação e manutenção das coberturas. Declaro, ainda, que estou ciente de que a omissão de informações sobre a existência de doenças ou lesões preexistentes das quais saiba ser portador(a) no momento do preenchimento deste formulário de declaração de saúde, desde que tal omissão seja comprovada junto à ANS, pode acarretar a suspensão ou o cancelamento do contrato. Neste caso, serei responsável pelo pagamento das despesas realizadas com o tratamento da doença ou lesão omitida, a partir da data em que tiver recebido comunicado ou notificação da Unimed alegando a presença de doença ou lesão preexistente não declarada.</p>

                <p id='declaracao_com_medico' class='hide'><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Você solicitou que sua declaração de saúde seja avaliada junto a um médico especialista, portanto nós entraremos em contato com você o mais breve possível para disponibilizar três horários diferentes disponíveis para esta entrevista, conforme regulamentação da ANS.</p>

                <div class='hide' id='dirijase' style='overflow: auto'>
                    <p>Para fins de adesão ao plano de saúde em conformidade com o que estabelece a Resolução Normativa nº 162 da ANS, <b>declaro está ciente do cumprimento da CPT – Cobertura Parcial Temporária</b>, e estou ciente de que isso acarretará, durante o prazo de 24 (vinte e quatro) meses após minha adesão de que estarão suspensas as coberturas para <b>procedimentos de alta complexidade (conforme rol ANS), Leitos de Alta Tecnologia e Eventos Cirúrgicos</b> relacionados às doenças acima descritas. Para que não haja dúvidas com relação aos procedimentos que não terei direito durante este prazo após minha adesão, que estão relacionados às doenças declaradas por mim, que posso consultar o Rol da ANS – Agência Nacional de Saúde Suplementar vigente à época de minha contratação, bem como suas atualizações em www.ans.gov.br ou dirigir-me à operadora em caso de dúvidas.</p>

                    <input type='radio' name='tipo_de_dlp' value='cpt' style='display: none'/>
                    <br/>

                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Matriz Manaus:</b><br/>Rua Rio Amapá, 374 - Nossa Sra. das Graças - Manaus-AM - CEP: 69053-150 / Fone: (92) 3303-8000 e 3303-8003</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Belém:</b><br/>Trav. Humaitá, 2778 - Marco - Belém-PA - CEP: 66095-220 / Fone: (91) 3344-0802</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Macapá:</b><br/>Rua Mendonça Furtado, 2278 - Sala B - Santa Rita - Macapá-AP - CEP: 68900-060 / Fone: (96) 3223-3646</div>
                    <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><B>Filial Boa Vista:</b><br/>Rua Coronel Mota, 1668 - Centro - CEP: 69301-120 / Fone: (95) 3198-2618</div>
                </div>

                <div class="form-group" style='margin-top: 20px'>
                    <button type="button" class="btn btn-success" id="continuar_declaracao_de_saude">Li e estou de acordo com os termos e condições</button>
                    <button type="submit" class="hide"></button>
                </div>
            </div>
        </div>

    </form>

    </main>
    @include('templates.footer')
</body>
</html>
