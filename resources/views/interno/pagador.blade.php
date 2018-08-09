<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Dados do Pagador</h1>
            <h2>Caso o pagador do plano não seja o titular, você pode cadastrá-lo nesta sessão.</h2>
        </hgroup>

        @php /* dd($dados->contratos[$codigo_contrato]);  */ @endphp

        @php $dados_contratante = @$dados->contratos[$codigo_contrato]->dados_contratante; @endphp

        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        @php
            if ((@$dados->contratos[$codigo_contrato]->status == "AUDITANDO" or @$dados->contratos[$codigo_contrato]->status == "DECLARACAO_DE_SAUDE")) {
                @endphp
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                Não é possível alterar os dados do pagador neste estágio da proposta
                            </div>
                        </div>
                    </div>
                @php
            }
        @endphp

        <form action='{{route("salva-pagador")}}' method='POST'>
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="codigo_contrato" value="{{$codigo_contrato}}"/>
            <div class="panel-body">
                <h4>Dados Pessoais</h4>
                <hr/>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cpf_contratante">CPF <span class="obrigatorio">*</span></label>
                            <input class="form-control isNumber" type="text" name="dados_contratante[cpf_contratante]" value="{{$dados_contratante->cpf_contratante or old('dados_contratante.cpf_contratante')}}" required/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_contratante">Nome Completo <span class="obrigatorio">*</span></label>
                            <input class="form-control" type="text" name="dados_contratante[nome_contratante]" value="{{$dados_contratante->nome_contratante or old('dados_contratante.nome_contratante')}}" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email_contratante">Email <span class="obrigatorio">*</span></label>
                            <input required class="form-control" type="text" name="dados_contratante[email_contratante]" value="{{$dados_contratante->email_contratante or old('dados_contratante.email_contratante')}}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mae_contratante">Nome da Mãe <span class="obrigatorio">*</span></label>
                            <input class="form-control" type="text" name="dados_contratante[mae_contratante]" value="{{$dados_contratante->mae_contratante or old('dados_contratante.mae_contratante')}}" required/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sexo_contratante">Sexo <span class="obrigatorio">*</span></label>
                            <select id="sexo_contratante" name="dados_contratante[sexo_contratante]" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($sexo as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->sexo_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.sexo_contratante') == $index AND !empty(old('dados_contratante.sexo_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estcivil_contratante">Estado Civil <span class="obrigatorio">*</span></label>
                            <select id="estcivil_contratante" name="dados_contratante[estcivil_contratante]" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($estcivil as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->estcivil_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.estcivil_contratante') == $index AND !empty(old('dados_contratante.estcivil_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nascimento_contratante">Data de Nascimento <span class="obrigatorio">*</span></label>
                            <input required class="form-control isDate" type="text" name="dados_contratante[nascimento_contratante]" @php echo @$dados_contratante->nascimento_contratante != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dados_contratante->nascimento_contratante), 'd/m/Y') . '"' : '' @endphp/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telfixo_contratante">Telefone Fixo com DDD</label>
                            <input class="form-control isNumber isTelefone" type="text" name="dados_contratante[telfixo_contratante]" value="{{$dados_contratante->telfixo_contratante or old('dados_contratante.telfixo_contratante')}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telcelular_contratante">Telefone Celular com DDD <span class="obrigatorio">*</span></label>
                            <input class="form-control isNumber isTelefone" type="text" name="dados_contratante[telcelular_contratante]" value="{{$dados_contratante->telcelular_contratante or old('dados_contratante.telcelular_contratante')}}" required/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rg_contratante">RG</label>
                            <input class="form-control isNumber" type="text" name="dados_contratante[rg_contratante]" value="{{$dados_contratante->rg_contratante or old('dados_contratante.rg_contratante')}}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="orgao_contratante">Orgão Expedidor</label>
                            <select id="orgao_contratante" name="dados_contratante[orgao_contratante]" class="form-control">
                                <option value="">Selecione</option>
                                @foreach ($orgexpedidores as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->orgao_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.orgao_contratante') == $index AND !empty(old('dados_contratante.orgao_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ufexpedicao_contratante">UF Expedição</label>
                            <select id="ufexpedicao_contratante" name="dados_contratante[ufexpedicao_contratante]" class="form-control">
                                <option value="">Selecione</option>
                                @foreach ($uf as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->ufexpedicao_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.ufexpedicao_contratante') == $index AND !empty(old('dados_contratante.ufexpedicao_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="datexpedicao_contratante">Data Expedição</label>
                            <input class="form-control isDate" type="text" name="dados_contratante[datexpedicao_contratante]" @php echo (old('dados_contratante.datexpedicao_contratante') ? "value='".old('dados_contratante.datexpedicao_contratante')."'" : ""); @endphp @php echo @$dados_contratante->datexpedicao_contratante != "" ? 'value="' . @date_format(date_create_from_format('Y-m-d', $dados_contratante->datexpedicao_contratante), 'd/m/Y') . '"' : '' @endphp/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ufnaturalidade_contratante">UF de Nascimento <span class="obrigatorio">*</span></label>
                            <select id="ufnaturalidade_contratante" name="dados_contratante[ufnaturalidade_contratante]" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($uf as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->ufnaturalidade_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.ufnaturalidade_contratante') == $index AND !empty(old('dados_contratante.ufnaturalidade_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="naturalidade_contratante">Município de Nascimento <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Nascimento para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                            <select id="naturalidade_contratante" name="dados_contratante[naturalidade_contratante]" class="form-control" required>
                                <option value="">Selecione</option>
                                @isset($dados_contratante->naturalidade_contratante)
                                    <option value="{{$dados_contratante->naturalidade_contratante}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dados_contratante->naturalidade_contratante, $dados_contratante->ufnaturalidade_contratante)}}</option>
                                @endisset
                                @if (old('dados_contratante.naturalidade_contratante'))
                                    <option value="{{old('dados_contratante.naturalidade_contratante')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_contratante.naturalidade_contratante'), old('dados_contratante.ufnaturalidade_contratante'))}}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nacionalidade_contratante">Nacionalidade <span class="obrigatorio">*</span></label>
                            <select id="nacionalidade_contratante" name="dados_contratante[nacionalidade_contratante]" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($paises as $index => $value)
                                <option value="{{$index}}" {{ $index == @$dados_contratante->nacionalidade_contratante ? 'selected="selected"' : ""}} {{ (old('dados_contratante.nacionalidade_contratante') == $index AND !empty(old('dados_contratante.nacionalidade_contratante'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="pai_contratante">Nome do Pai</label>
                            <input class="form-control" type="text" name="dados_contratante[pai_contratante]" value="{{$dados_contratante->pai_contratante or old('dados_contratante.pai_contratante')}}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dnv_contratante">DNV <i class="duvida fa fa-question-circle" rel="tooltip" title="Declaração de Nascido vivo - A declaração de nascido vivo tem o objetivo de garantir o acesso da criança recém-nascida a políticas públicas, bem como é documento necessário para a realização do registro de nascimento no cartório de Registro Civil. Obrigatório para pessoas que nasceram a partir de 01/01/2010. " aria-hidden="true"></i></label>
                            <input class="form-control isNumber" type="text" name="dados_contratante[dnv_contratante]" value="{{$dados_contratante->dnv_contratante or old('dados_contratante.dns_contratante')}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cns_contratante">CNS <i class="duvida fa fa-question-circle" rel="tooltip" title="Cartão Nacional de Saúde - Por determinação do Ministério da Saúde, todas as pessoas, inclusive os usuários de planos de saúde, devem possuir o número do Cartão Nacional de Saúde (CNS). É um documento numerado que possibilita a identificação de qualquer pessoa no Brasil, ao utilizar os serviços de saúde. " aria-hidden="true"></i></label>
                            <input class="form-control isNumber" type="text" name="dados_contratante[cns_contratante]" value="{{$dados_contratante->cns_contratante or old('dados_contratante.cns_contratante')}}"/>
                        </div>
                    </div>
                </div>

                <h4>Endereço de Correspondência</h4>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Endereço de correspondência do pagador é igual ao endereço de correspondência do titular</label>
                            <div class="inline-radio">Sim <input type="radio" name="dados_contratante[mesmo_endereco]" value="1" @php echo ((@$dados_contratante->mesmo_endereco !== '0') ? 'checked="checked"' : '') @endphp @php echo ((@$dados_contratante->mesmo_endereco === '1') ? 'checked="checked"' : '') @endphp @php echo (old('dados_contratante.mesmo_endereco') === "1" ? 'checked="checked"' : "") @endphp/></div>
                            <div class="inline-radio">Não <input type="radio" name="dados_contratante[mesmo_endereco]" value="0" @php echo ((@$dados_contratante->mesmo_endereco === '0') ? 'checked="checked"' : '') @endphp @php echo (old('dados_contratante.mesmo_endereco') === "0" ? 'checked="checked"' : "") @endphp/></div>
                        </div>
                    </div>
                </div>
                <div id="contratante_correspondencia" @php echo ((@$dados_contratante->mesmo_endereco === "0") ? '' : 'class="hide"') @endphp @php echo (old('dados_contratante.mesmo_endereco') == "1" ? 'class="hide"' : '') @endphp>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cep_correspondencia">CEP <span class="obrigatorio">*</span></label>
                                <input class="form-control isNumber" type="text" id="cep_correspondencia" name="dados_contratante[cep_correspondencia]" value="{{$dados_contratante->cep_correspondencia or old('dados_contratante.cep_correspondencia')}}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="uf_correspondencia">UF <span class="obrigatorio">*</span></label>
                                <select id="uf_correspondencia" name="dados_contratante[uf_correspondencia]" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach ($uf as $index => $value)
                                    @php if ($index != $uf_permitida) { continue; } @endphp
                                    <option value="{{$index}}" {{ $index == @$dados_contratante->uf_correspondencia ? 'selected="selected"' : ""}} {{ (old('dados_contratante.uf_correspondencia') == $index AND !empty(old('dados_contratante.uf_correspondencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="municipio_correspondencia">Município <span class="obrigatorio">*</span> <i class="duvida fa fa-question-circle" rel="tooltip" title="Selecione primeiro a UF de Correspondência para os municípios desta UF serem listados." aria-hidden="true"></i></label>
                                <select id="municipio_correspondencia" name="dados_contratante[municipio_correspondencia]" class="form-control">
                                    <option value="">Selecione</option>
                                    @isset($dados_contratante->municipio_correspondencia)
                                        <option value="{{$dados_contratante->municipio_correspondencia}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF($dados_contratante->municipio_correspondencia, $dados_contratante->uf_correspondencia)}}</option>
                                    @endisset
                                    @if (old('dados_contratante.municipio_correspondencia'))
                                        <option value="{{old('dados_contratante.municipio_correspondencia')}}" selected="selected">{{\App\Tradutor::MunicipiosDaUF(old('dados_contratante.municipio_correspondencia'), old('dados_contratante.uf_correspondencia'))}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bairro_correspondencia">Bairro <span class="obrigatorio">*</span></label>
                                <input class="form-control" type="text" name="dados_contratante[bairro_correspondencia]" value="{{$dados_contratante->bairro_correspondencia or old('dados_contratante.bairro_correspondencia')}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tiplogradouro_correspondencia">Tipo de Logradouro <span class="obrigatorio">*</span></label>
                                <select id="tiplogradouro_correspondencia" name="dados_contratante[tiplogradouro_correspondencia]" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach ($tiplogradouro as $index => $value)
                                    <option value="{{$index}}" {{ $index == @$dados_contratante->tiplogradouro_correspondencia ? 'selected="selected"' : ""}} {{ (old('dados_contratante.tiplogradouro_correspondencia') == $index AND !empty(old('dados_contratante.tiplogradouro_correspondencia'))) ? 'selected="selected"' : ""}}>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="logradouro_correspondencia">Logradouro <span class="obrigatorio">*</span></label>
                                <input class="form-control" type="text" name="dados_contratante[logradouro_correspondencia]" value="{{$dados_contratante->logradouro_correspondencia or old('dados_contratante.logradouro_correspondencia')}}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numero_correspondencia">Número <span class="obrigatorio">*</span></label>
                                <input class="form-control" type="text" name="dados_contratante[numero_correspondencia]" value="{{$dados_contratante->numero_correspondencia or old('dados_contratante.numero_correspondencia')}}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="complemento_correspondencia">Complemento</label>
                                <input class="form-control" type="text" name="dados_contratante[complemento_correspondencia]" value="{{$dados_contratante->complemento_correspondencia or old('dados_contratante.complemento_correspondencia')}}"/>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    if (!(@$dados->contratos[$codigo_contrato]->status == "AUDITANDO" or @$dados->contratos[$codigo_contrato]->status == "DECLARACAO_DE_SAUDE")) {
                        @endphp
                            <div class="form-group">
                                <button type="button" class="btn btn-default" onclick="window.history.go(-1); return false;">Voltar</button>
                                <button type="submit" class="btn btn-success" id="continuar_formulario_contratos">Salvar</button>
                            </div>
                        @php
                    }
                @endphp

            </div>
        </form>

    </main>
    @include('templates.footer')
</body>

</html>
