<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Dados do Contrato</h1>
            <h2>Faça o preenchimento correto dos dados do contrato. Protocolo de contratação: {{$dados->contratos[0]->protocolo}}</h2>
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

        @php $dados_titular = $dados->contratos[$codigo_contrato]->dados_titular;  @endphp
        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        <form method="POST" action="{{route('salva-contrato')}}" id="formulario_contrato_principal">
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="codigo_contrato" value="{{$codigo_contrato}}"/>
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
                            <h4>Dados Pessoais</h4>
                            <hr/>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cpf_titular">CPF <span class="obrigatorio">*</span></label>
                                        <input id="cpf_titular" maxlength="11" class="form-control isNumber busca_cns" data-cns="cns_titular" type="text" name="dados_titular[cpf_titular]" value="{{$dados_titular->cpf_titular or old('dados_titular.cpf_titular')}}" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nome_titular">Nome Completo <span class="obrigatorio">*</span></label>
                                        <input maxlength="60" class="form-control" type="text" name="dados_titular[nome_titular]" value="{{$dados_titular->nome_titular or old('dados_titular.nome_titular')}}" required/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email_titular">Email <span class="obrigatorio">*</span></label>
                                        <input maxlength="150" required class="form-control" type="text" name="dados_titular[email_titular]" value="{{$dados_titular->email_titular or old('dados_titular.email_titular')}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mae_titular">Nome da Mãe <span class="obrigatorio">*</span></label>
                                        <input maxlength="60" class="form-control" type="text" name="dados_titular[mae_titular]" value="{{$dados_titular->mae_titular or old('dados_titular.mae_titular')}}" required/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sexo_titular">Sexo <span class="obrigatorio">*</span></label>
                                        <select id="sexo_titular" name="dados_titular[sexo_titular]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach ($sexo as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->sexo_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.sexo_titular') == $index AND !empty(old('dados_titular.sexo_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="estcivil_titular">Estado Civil <span class="obrigatorio">*</span></label>
                                        <select id="estcivil_titular" name="dados_titular[estcivil_titular]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach ($estcivil as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->estcivil_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.estcivil_titular') == $index AND !empty(old('dados_titular.estcivil_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nascimento_titular">Data de Nascimento <span class="obrigatorio">*</span></label>
                                        <input class="form-control isDate" type="text" name="dados_titular[nascimento_titular]" @php echo @$dados_titular->nascimento_titular != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dados_titular->nascimento_titular), 'd/m/Y') . '"' : '' @endphp readonly required/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telfixo_titular">Telefone Fixo com DDD</label>
                                        <input class="form-control isNumber isTelefone" type="text" name="dados_titular[telfixo_titular]" value="{{$dados_titular->telfixo_titular or old('dados_titular.telfixo_titular')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telcelular_titular">Telefone Celular com DDD <span class="obrigatorio">*</span></label>
                                        <input class="form-control isNumber isTelefone" type="text" name="dados_titular[telcelular_titular]" value="{{$dados_titular->telcelular_titular or old('dados_titular.telcelular_titular')}}" required/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rg_titular">RG</label>
                                        <input maxlength="20" class="form-control isNumber" type="text" name="dados_titular[rg_titular]" value="{{$dados_titular->rg_titular or old('dados_titular.rg_titular')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="orgao_titular">Orgão Expedidor</label>
                                        <select id="orgao_titular" name="dados_titular[orgao_titular]" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach ($orgexpedidores as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->orgao_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.orgao_titular') == $index AND !empty(old('dados_titular.orgao_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ufexpedicao_titular">UF Expedição</label>
                                        <select id="ufexpedicao_titular" name="dados_titular[ufexpedicao_titular]" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach ($uf as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->ufexpedicao_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.ufexpedicao_titular') == $index AND !empty(old('dados_titular.ufexpedicao_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="datexpedicao_titular">Data Expedição</label>
                                        <input class="form-control isDate" type="text" name="dados_titular[datexpedicao_titular]" @php echo (old('dados_titular.datexpedicao_titular') ? "value='".old('dados_titular.datexpedicao_titular')."'" : ""); @endphp @php echo @$dados_titular->datexpedicao_titular != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dados_titular->datexpedicao_titular), 'd/m/Y') . '"' : '' @endphp/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ufnaturalidade_titular">UF de Nascimento <span class="obrigatorio">*</span></label>
                                        <select id="ufnaturalidade_titular" name="dados_titular[ufnaturalidade_titular]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach ($uf as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->ufnaturalidade_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.ufnaturalidade_titular') == $index AND !empty(old('dados_titular.ufnaturalidade_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="naturalidade_titular">Município de Nascimento <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Nascimento para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                        <select id="naturalidade_titular" name="dados_titular[naturalidade_titular]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @isset($dados_titular->naturalidade_titular)
                                                <option value="{{$dados_titular->naturalidade_titular}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dados_titular->naturalidade_titular, $dados_titular->ufnaturalidade_titular)}}</option>
                                            @endisset
                                            @if (old('dados_titular.naturalidade_titular'))
                                                <option value="{{old('dados_titular.naturalidade_titular')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_titular.naturalidade_titular'), old('dados_titular.ufnaturalidade_titular'))}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nacionalidade_titular">Nacionalidade <span class="obrigatorio">*</span></label>
                                        <select id="nacionalidade_titular" name="dados_titular[nacionalidade_titular]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach ($paises as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->nacionalidade_titular ? 'selected="selected"' : ""}} {{ (old('dados_titular.nacionalidade_titular') == $index AND !empty(old('dados_titular.nacionalidade_titular'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="pai_titular">Nome do Pai</label>
                                        <input maxlength="60" class="form-control" type="text" name="dados_titular[pai_titular]" value="{{$dados_titular->pai_titular or old('dados_titular.pai_titular')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dnv_titular">DNV <i class="duvida fa fa-question-circle" rel="tooltip" title="Declaração de Nascido vivo - A declaração de nascido vivo tem o objetivo de garantir o acesso da criança recém-nascida a políticas públicas, bem como é documento necessário para a realização do registro de nascimento no cartório de Registro Civil. " aria-hidden="true"></i></label>
                                        <input maxlength="15" class="form-control isNumber" type="text" name="dados_titular[dnv_titular]" value="{{$dados_titular->dnv_titular or old('dados_titular.dns_titular')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cns_titular">CNS <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Cartão Nacional de Saúde - Por determinação do Ministério da Saúde, todas as pessoas, inclusive os usuários de planos de saúde, devem possuir o número do Cartão Nacional de Saúde (CNS). É um documento numerado que possibilita a identificação de qualquer pessoa no Brasil, ao utilizar os serviços de saúde. " aria-hidden="true"></i> <a href='http://cartaosus.com.br/consulta-cartao-sus/' target='_blank'>Consultar CNS</a></label>
                                        <input id='cns_titular' maxlength="17" class="form-control isNumber" type="text" name="dados_titular[cns_titular]" value="{{$dados_titular->cns_titular or old('dados_titular.cns_titular')}}" required/>
                                    </div>
                                </div>
                            </div>

                            <h4>Endereço de Residência</h4>
                            <hr/>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cep_residencia">CEP <span class="obrigatorio">*</span></label>
                                        <input maxlength="8" required class="form-control isNumber" type="text" id="cep_residencia" name="dados_titular[cep_residencia]" value="{{$dados_titular->cep_residencia or old('dados_titular.cep_residencia')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="uf_residencia">UF <span class="obrigatorio">*</span></label>
                                        <select required id="uf_residencia" name="dados_titular[uf_residencia]" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach ($uf as $index => $value)
                                            @php if ($index != $uf_permitida) { continue; } @endphp
                                            <option value="{{$index}}" {{ $index == @$dados_titular->uf_residencia ? 'selected="selected"' : ""}} {{ (old('dados_titular.uf_residencia') == $index AND !empty(old('dados_titular.uf_residencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="municipio_residencia">Município <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Residência para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                        <select required id="municipio_residencia" name="dados_titular[municipio_residencia]" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @isset($dados_titular->municipio_residencia)
                                                <option value="{{$dados_titular->municipio_residencia}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dados_titular->municipio_residencia, $dados_titular->uf_residencia)}}</option>
                                            @endisset
                                            @if (old('dados_titular.municipio_residencia'))
                                                <option value="{{old('dados_titular.municipio_residencia')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_titular.municipio_residencia'), old('dados_titular.uf_residencia'))}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bairro_residencia">Bairro <span class="obrigatorio">*</span></label>
                                        <input required class="form-control" type="text" name="dados_titular[bairro_residencia]" value="{{$dados_titular->bairro_residencia or old('dados_titular.bairro_residencia')}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tiplogradouro_residencia">Tipo de Logradouro <span class="obrigatorio">*</span></label>
                                        <select required id="tiplogradouro_residencia" name="dados_titular[tiplogradouro_residencia]" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach ($tiplogradouro as $index => $value)
                                            <option value="{{$index}}" {{ $index == @$dados_titular->tiplogradouro_residencia ? 'selected="selected"' : ""}} {{ (old('dados_titular.tiplogradouro_residencia') == $index AND !empty(old('dados_titular.tiplogradouro_residencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="logradouro_residencia">Logradouro <span class="obrigatorio">*</span></label>
                                        <input required class="form-control" type="text" name="dados_titular[logradouro_residencia]" value="{{$dados_titular->logradouro_residencia or old('dados_titular.logradouro_residencia')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="numero_residencia">Número <span class="obrigatorio">*</span></label>
                                        <input maxlength="10" required class="form-control" type="text" name="dados_titular[numero_residencia]" value="{{$dados_titular->numero_residencia or old('dados_titular.numero_residencia')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="complemento_residencia">Complemento</label>
                                        <input maxlength="100" class="form-control" type="text" name="dados_titular[complemento_residencia]" value="{{$dados_titular->complemento_residencia or old('dados_titular.complemento_residencia')}}"/>
                                    </div>
                                </div>
                            </div>

                            <h4>Endereço de Correspondência</h4>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Endereço de Correspondência é igual ao endereço de residência</label>
                                        <div class="inline-radio">Sim <input type="radio" name="dados_titular[mesmo_endereco]" value="1" @php echo ((@$dados_titular->mesmo_endereco !== '0') ? 'checked="checked"' : '') @endphp @php echo ((@$dados_titular->mesmo_endereco === '1') ? 'checked="checked"' : '') @endphp @php echo (old('dados_titular.mesmo_endereco') === "1" ? 'checked="checked"' : "") @endphp/></div>
                                        <div class="inline-radio">Não <input type="radio" name="dados_titular[mesmo_endereco]" value="0" @php echo ((@$dados_titular->mesmo_endereco === '0') ? 'checked="checked"' : '') @endphp @php echo (old('dados_titular.mesmo_endereco') === "0" ? 'checked="checked"' : "") @endphp/></div>
                                    </div>
                                </div>
                            </div>
                            <div id="titular_correspondencia" @php echo ((@$dados_titular->mesmo_endereco === "0") ? '' : 'class="hide"') @endphp @php echo (old('dados_titular.mesmo_endereco') == "1" ? 'class="hide"' : '') @endphp>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="cep_correspondencia">CEP <span class="obrigatorio">*</span></label>
                                            <input maxlength="8" class="form-control isNumber" type="text" id="cep_correspondencia" name="dados_titular[cep_correspondencia]" value="{{$dados_titular->cep_correspondencia or old('dados_titular.cep_correspondencia')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="uf_correspondencia">UF <span class="obrigatorio">*</span></label>
                                            <select id="uf_correspondencia" name="dados_titular[uf_correspondencia]" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach ($uf as $index => $value)
                                                @php if ($index != $uf_permitida) { continue; } @endphp
                                                <option value="{{$index}}" {{ $index == @$dados_titular->uf_correspondencia ? 'selected="selected"' : ""}} {{ (old('dados_titular.uf_correspondencia') == $index AND !empty(old('dados_titular.uf_correspondencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="municipio_correspondencia">Município <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Correspondência para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                            <select id="municipio_correspondencia" name="dados_titular[municipio_correspondencia]" class="form-control">
                                                <option value="">Selecione</option>
                                                @isset($dados_titular->municipio_correspondencia)
                                                    <option value="{{$dados_titular->municipio_correspondencia}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dados_titular->municipio_correspondencia, $dados_titular->uf_correspondencia)}}</option>
                                                @endisset
                                                @if (old('dados_titular.municipio_correspondencia'))
                                                    <option value="{{old('dados_titular.municipio_correspondencia')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_titular.municipio_correspondencia'), old('dados_titular.uf_correspondencia'))}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bairro_correspondencia">Bairro <span class="obrigatorio">*</span></label>
                                            <input class="form-control" type="text" name="dados_titular[bairro_correspondencia]" value="{{$dados_titular->bairro_correspondencia or old('dados_titular.bairro_correspondencia')}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tiplogradouro_correspondencia">Tipo de Logradouro <span class="obrigatorio">*</span></label>
                                            <select id="tiplogradouro_correspondencia" name="dados_titular[tiplogradouro_correspondencia]" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach ($tiplogradouro as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dados_titular->tiplogradouro_correspondencia ? 'selected="selected"' : ""}} {{ (old('dados_titular.tiplogradouro_correspondencia') == $index AND !empty(old('dados_titular.tiplogradouro_correspondencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="logradouro_correspondencia">Logradouro <span class="obrigatorio">*</span></label>
                                            <input class="form-control" type="text" name="dados_titular[logradouro_correspondencia]" value="{{$dados_titular->logradouro_correspondencia or old('dados_titular.logradouro_correspondencia')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="numero_correspondencia">Número <span class="obrigatorio">*</span></label>
                                            <input maxlength="10" class="form-control" type="text" name="dados_titular[numero_correspondencia]" value="{{$dados_titular->numero_correspondencia or old('dados_titular.numero_correspondencia')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="complemento_correspondencia">Complemento</label>
                                            <input maxlength="100" class="form-control" type="text" name="dados_titular[complemento_correspondencia]" value="{{$dados_titular->complemento_correspondencia or old('dados_titular.complemento_correspondencia')}}"/>
                                        </div>
                                    </div>
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
                                <h4>Tipo de Parentesco</h4>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_parentesco">Tipo de parentesco em relação ao titular <span class="obrigatorio">*</span></label>
                                            <select required id="tipo_parentesco" name="dados_dependentes[{{$loop->index}}][tipo_parentesco]" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach ($dependencias as $index => $value)
                                                <option value="{{$value->codigo_dependencia}}" {{ $value->codigo_dependencia == @$dependente->tipo_parentesco ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.tipo_parentesco') == $value->codigo_dependencia AND !empty(old('dados_dependentes.'.$loop->parent->index.'.tipo_parentesco'))) ? 'selected="selected"' : ""}}>{{$value->descricao}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <h4>Dados Pessoais</h4>
                                <hr/>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="cpf_dependente">CPF <span class="obrigatorio">*</span></label>
                                            <input maxlength="11" class="form-control isNumber busca_cns" data-cns="cns_dependente_{{$loop->index}}" type="text" name="dados_dependentes[{{$loop->index}}][cpf_dependente]" value="{{$dependente->cpf_dependente or old('dados_dependentes.'.$loop->index.'.cpf_dependente')}}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome_dependente">Nome Completo <span class="obrigatorio">*</span></label>
                                            <input maxlength="60" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][nome_dependente]" value="{{$dependente->nome_dependente or old('dados_dependentes.'.$loop->index.'.nome_dependente')}}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email_dependente">Email <span class="obrigatorio">*</span></label>
                                            <input maxlength="150" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][email_dependente]" value="{{$dependente->email_dependente or old('dados_dependentes.'.$loop->index.'.email_dependente')}}" required/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mae_dependente">Nome da Mãe <span class="obrigatorio">*</span></label>
                                            <input maxlength="60" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][mae_dependente]" value="{{$dependente->mae_dependente or old('dados_dependentes.'.$loop->index.'.mae_dependente')}}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sexo_dependente">Sexo <span class="obrigatorio">*</span></label>
                                            <select id="sexo_dependente" name="dados_dependentes[{{$loop->index}}][sexo_dependente]" class="form-control" required>
                                                <option value="">Selecione</option>
                                                @foreach ($sexo as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->sexo_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.sexo_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.sexo_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="estcivil_dependente">Estado Civil <span class="obrigatorio">*</span></label>
                                            <select id="estcivil_dependente" name="dados_dependentes[{{$loop->index}}][estcivil_dependente]" class="form-control" required>
                                                <option value="">Selecione</option>
                                                @foreach ($estcivil as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->estcivil_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.estcivil_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.estcivil_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nascimento_dependente">Data de Nascimento <span class="obrigatorio">*</span></label>
                                            <input class="form-control isDate" type="text" name="dados_dependentes[{{$loop->index}}][nascimento_dependente]" @php echo @$dependente->nascimento_dependente != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dependente->nascimento_dependente), 'd/m/Y') . '"' : '' @endphp readonly required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telfixo_dependente">Telefone Fixo com DDD</label>
                                            <input class="form-control isNumber isTelefone" type="text" name="dados_dependentes[{{$loop->index}}][telfixo_dependente]" value="{{$dependente->telfixo_dependente or old('dados_dependentes.'.$loop->index.'.telfixo_dependente')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telcelular_dependente">Telefone Celular com DDD <span class="obrigatorio">*</span></label>
                                            <input class="form-control isNumber isTelefone" type="text" name="dados_dependentes[{{$loop->index}}][telcelular_dependente]" value="{{$dependente->telcelular_dependente or old('dados_dependentes.'.$loop->index.'.telcelular_dependente')}}" required/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rg_dependente">RG</label>
                                            <input maxlength="20" class="form-control isNumber" type="text" name="dados_dependentes[{{$loop->index}}][rg_dependente]" value="{{$dependente->rg_dependente or old('dados_dependentes.'.$loop->index.'.rg_dependente')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="orgao_dependente">Orgão Expedidor</label>
                                            <select id="orgao_dependente" name="dados_dependentes[{{$loop->index}}][orgao_dependente]" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach ($orgexpedidores as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->orgao_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.orgao_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.orgao_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ufexpedicao_dependente">UF Expedição</label>
                                            <select id="ufexpedicao_dependente" name="dados_dependentes[{{$loop->index}}][ufexpedicao_dependente]" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach ($uf as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->ufexpedicao_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.ufexpedicao_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.ufexpedicao_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="datexpedicao_dependente">Data Expedição</label>
                                            <input class="form-control isDate" type="text" name="dados_dependentes[{{$loop->index}}][datexpedicao_dependente]" @php echo @$dependente->datexpedicao_dependente != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dependente->datexpedicao_dependente), 'd/m/Y') . '"' : '' @endphp @php echo (!empty(old('dados_dependentes.'.$loop->index.'.datexpedicao_dependente')) ? old('dados_dependentes.'.$loop->index.'.datexpedicao_dependente') : '') @endphp/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ufnaturalidade_dependente">UF de Nascimento <span class="obrigatorio">*</span></label>
                                            <select data-codigo="@php echo $loop->index @endphp" data-seletor="ufnaturalidade_dependente" name="dados_dependentes[{{$loop->index}}][ufnaturalidade_dependente]" class="form-control" required>
                                                <option value="">Selecione</option>
                                                @foreach ($uf as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->ufnaturalidade_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.ufnaturalidade_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.ufnaturalidade_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="naturalidade_dependente">Município de Nascimento <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Nascimento para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                            <select data-codigo="@php echo $loop->index @endphp" id="naturalidade_dependente" name="dados_dependentes[{{$loop->index}}][naturalidade_dependente]" class="form-control" required>
                                                <option value="">Selecione</option>
                                                @isset($dependente->naturalidade_dependente)
                                                    <option value="{{$dependente->naturalidade_dependente}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dependente->naturalidade_dependente, $dependente->ufnaturalidade_dependente)}}</option>
                                                @endisset
                                                @if (old('dados_dependentes.'.$loop->index.'.naturalidade_dependente'))
                                                    <option value="{{old('dados_dependentes.'.$loop->index.'.naturalidade_dependente')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_dependentes.'.$loop->index.'.naturalidade_dependente'), old('dados_dependentes.'.$loop->index.'.ufnaturalidade_dependente'))}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nacionalidade_dependente">Nacionalidade <span class="obrigatorio">*</span></label>
                                            <select id="nacionalidade_dependente" name="dados_dependentes[{{$loop->index}}][nacionalidade_dependente]" class="form-control" required>
                                                <option value="">Selecione</option>
                                                @foreach ($paises as $index => $value)
                                                <option value="{{$index}}" {{ $index == @$dependente->nacionalidade_dependente ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.nacionalidade_dependente') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.nacionalidade_dependente'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="pai_dependente">Nome do Pai</label>
                                            <input maxlength="60" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][pai_dependente]" value="{{$dependente->pai_dependente or old('dados_dependentes.'.$loop->index.'.pai_dependente')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="dnv_dependente">DNV <i class="duvida fa fa-question-circle" rel="tooltip" title="Declaração de Nascido vivo - A declaração de nascido vivo tem o objetivo de garantir o acesso da criança recém-nascida a políticas públicas, bem como é documento necessário para a realização do registro de nascimento no cartório de Registro Civil. Obrigatório para pessoas que nasceram a partir de 01/01/2010. " aria-hidden="true"></i></label>
                                            <input maxlength="15" class="form-control isNumber" type="text" name="dados_dependentes[{{$loop->index}}][dnv_dependente]" value="{{$dependente->dnv_dependente or old('dados_dependentes.'.$loop->index.'.dnv_dependente')}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cns_dependente">CNS <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Cartão Nacional de Saúde - Por determinação do Ministério da Saúde, todas as pessoas, inclusive os usuários de planos de saúde, devem possuir o número do Cartão Nacional de Saúde (CNS). É um documento numerado que possibilita a identificação de qualquer pessoa no Brasil, ao utilizar os serviços de saúde. " aria-hidden="true"></i></label>
                                            <input id="cns_dependente_{{$loop->index}}" maxlength="17" class="form-control isNumber" type="text" name="dados_dependentes[{{$loop->index}}][cns_dependente]" value="{{$dependente->cns_dependente or old('dados_dependentes.'.$loop->index.'.cns_dependente')}}" required/>
                                        </div>
                                    </div>
                                </div>

                                <h4>Endereço de Residência</h4>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Endereço residencial é o mesmo endereço do titular</label>
                                            <div class="inline-radio">Sim <input type="radio" data-codigo="@php echo $loop->index @endphp" data-seletor="dependente_mesmo_endereco" name="dados_dependentes[{{$loop->index}}][mesmo_endereco]" value="1" @php echo ((@$dependente->mesmo_endereco !== '0') ? 'checked="checked"' : '') @endphp @php echo ((@$dependente->mesmo_endereco === '1') ? 'checked="checked"' : '') @endphp @php echo (old('dados_dependentes.'.$loop->index.'.mesmo_endereco') === "1" ? 'checked="checked"' : "") @endphp/></div>
                                            <div class="inline-radio">Não <input type="radio" data-codigo="@php echo $loop->index @endphp" data-seletor="dependente_mesmo_endereco" name="dados_dependentes[{{$loop->index}}][mesmo_endereco]" value="0" @php echo ((@$dependente->mesmo_endereco === '0') ? 'checked="checked"' : '') @endphp @php echo (old('dados_dependentes.'.$loop->index.'.mesmo_endereco') === "0" ? 'checked="checked"' : "") @endphp/></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="endereco_residencia" data-codigo="@php echo $loop->index @endphp" @php echo ((@$dependente->mesmo_endereco === "0") ? 'class="endereco_residencia"' : 'class="hide endereco_residencia"') @endphp @php echo (old('dados_dependentes.'.$loop->index.'.mesmo_endereco') == "1" ? 'class="hide endereco_residencia"' : 'class="endereco_residencia"') @endphp>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cep_residencia">CEP <span class="obrigatorio">*</span></label>
                                                <input maxlength="8" data-codigo="@php echo $loop->index @endphp" class="form-control isNumber isCEP" type="text" id="cep_residencia" name="dados_dependentes[{{$loop->index}}][cep_residencia]" value="{{$dependente->cep_residencia or old('dados_dependentes.'.$loop->index.'.cep_residencia')}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="uf_residencia">UF <span class="obrigatorio">*</span></label>
                                                <select data-codigo="@php echo $loop->index @endphp" id="uf_residencia" name="dados_dependentes[{{$loop->index}}][uf_residencia]" class="form-control isUF">
                                                    <option value="">Selecione</option>
                                                    @foreach ($uf as $index => $value)
                                                    @php if ($index != $uf_permitida) { continue; } @endphp
                                                    <option value="{{$index}}" {{ $index == @$dependente->uf_residencia ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.uf_residencia') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.uf_residencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="municipio_residencia">Município <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Residência para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                                <select data-codigo="@php echo $loop->index @endphp" id="municipio_residencia" name="dados_dependentes[{{$loop->index}}][municipio_residencia]" class="form-control">
                                                    <option value="">Selecione</option>
                                                    @isset($dependente->municipio_residencia)
                                                        <option value="{{$dependente->municipio_residencia}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dependente->municipio_residencia, $dependente->uf_residencia)}}</option>
                                                    @endisset
                                                    @if (old('dados_dependentes.'.$loop->index.'.municipio_residencia'))
                                                        <option value="{{$dependente->municipio_residencia}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_dependentes.'.$loop->index.'.municipio_residencia'), old('dados_dependentes.'.$loop->index.'.uf_residencia'))}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="bairro_residencia">Bairro <span class="obrigatorio">*</span></label>
                                                <input data-codigo="@php echo $loop->index @endphp" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][bairro_residencia]" value="{{$dependente->bairro_residencia or old('dados_dependentes.'.$loop->index.'.bairro_residencia')}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tiplogradouro_residencia">Tipo de Logradouro <span class="obrigatorio">*</span></label>
                                                <select data-codigo="@php echo $loop->index @endphp" id="tiplogradouro_residencia" name="dados_dependentes[{{$loop->index}}][tiplogradouro_residencia]" class="form-control">
                                                    <option value="">Selecione</option>
                                                    @foreach ($tiplogradouro as $index => $value)
                                                    <option value="{{$index}}" {{ $index == @$dependente->tiplogradouro_residencia ? 'selected="selected"' : ""}} {{ (old('dados_dependentes.'.$loop->parent->index.'.tiplogradouro_residencia') == $index AND !empty(old('dados_dependentes.'.$loop->parent->index.'.tiplogradouro_residencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="logradouro_residencia">Logradouro <span class="obrigatorio">*</span></label>
                                                <input data-codigo="@php echo $loop->index @endphp" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][logradouro_residencia]" value="{{$dependente->logradouro_residencia or old('dados_dependentes.'.$loop->index.'.logradouro_residencia')}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="numero_residencia">Número <span class="obrigatorio">*</span></label>
                                                <input maxlength="10" data-codigo="@php echo $loop->index @endphp" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][numero_residencia]" value="{{$dependente->numero_residencia or old('dados_dependentes.'.$loop->index.'.numero_residencia')}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="complemento_residencia">Complemento</label>
                                                <input maxlength="100" class="form-control" type="text" name="dados_dependentes[{{$loop->index}}][complemento_residencia]" value="{{$dependente->complemento_residencia or old('dados_dependentes.'.$loop->index.'.complemento_residencia')}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                <!-- Não há dependentes neste contrato -->
                @endforelse
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
<script>
    $(".busca_cns").change(function(){

        var id = "#" + $(this).data('cns');

       $.ajax({
            'url' : '/ajax/cns/'+$(this).val(),
            'type' : 'get',
            
            success : function(result){
                
                if(result){
                    $(id).val(result);
                }  
                
            }
        });
    });
</script>

</html>
