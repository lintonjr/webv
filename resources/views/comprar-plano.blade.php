<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Contratação do Plano | WebVendas'])
    <link rel="stylesheet" href="/css/radio-mobile.css">
<body>
	@include('templates.navbar')
	<main class="container">
		<hgroup>
            <h1>Contratar Plano</h1>
            <h2>Prossiga informando os dados a seguir para contratar o plano.</h2>
        </hgroup>

        @php
            if (\Auth::id()) {
                /* Usuário já está logado, então ele está aqui por que quer contratar um novo plano */
                $contato    = \App\ContatosOnline::where("codigo_contato", session('codigo_contato'))->first();
                $email      = @$contato->email;
                $ddd        = @$contato->ddd;
                $telefone   = @$contato->celular;
                session(['codigo_contato' => '']);
            }
        @endphp

        <section>
            <form action="{{ route('confirmacao-compra') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="title">
                                <h3>Informações iniciais</h3>
                            </div>
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="email">Seu e-mail</label>
                                            <input required class="form-control" type="text" name="email" id="email" value="{{ $email or old('email') }}" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="ddd">DDD</label>
                                                    <input required class="form-control isNumber" type="text" name="ddd" id="ddd" value="{{ $ddd or old('ddd') }}" maxlength="2">
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="form-group">
                                                    <label for="telefone">Seu telefone</label>
                                                    <input required class="form-control isNumber" type="text" name="telefone" id="telefone" value="{{ $telefone or old('telefone') }}" maxlength="9">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- content -->
                        </div> <!-- card -->

                        <div class="card">
                            <div class="title">
                                <h3>Informações do plano</h3>
                            </div>
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label for="codigo_plano">Selecione o plano</label>
                                            @if(!\Session::get('mobile'))
                                            <select required name="codigo_plano" id="codigo_plano" class="form-control">
                                                <option>Clique aqui</option>
                                                @foreach ($planos as $plano)
                                                <option value="{{$plano->codigo_plano}}" {{($plano->codigo_plano == @$codigo_plano || old('codigo_plano') == $plano->codigo_plano) ? 'selected=\'selected\'' : ''}}>
                                                    {{$plano->nome_plano}} - {{mb_strtoupper($plano->descricao_plano)}} - {{$plano->registro_ans}}
                                                </option>
                                                @endforeach
                                            </select>
                                            @else
                                              <div class="box">
                                                @foreach ($planos as $plano)
                                                  <input {{$plano->codigo_plano == @$codigo_plano ? 'checked="checked"' : '' }} required="required" value="{{$plano->codigo_plano}}" type="radio" id="{{$plano->codigo_plano}}" name="codigo_plano">
                                                  <label for="{{$plano->codigo_plano}}">{{$plano->nome_plano}}</label>
                                                @endforeach
                                              </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('planos') }}" {{!\Session::get('mobile') ? 'target="_blank"' : ''  }} class="btn btn-green btn-xs">Informações dos Planos</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="card_titular" class="card">
                            <div class="title">
                                <h3>Informações do titular</h3>
                            </div>
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="nome_titular">Nome</label>
                                            <input required class="form-control" type="text" name="nome_titular" id="nome_titular" value="{{ old('nome_titular') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="nascimento_titular">Data de nascimento</label>
                                            <input required class="form-control isDate" type="text" name="nascimento_titular" id="nascimento_titular" value="{{ old('nascimento_titular') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br class="toRemove">
                        <div id="card_dependentes" class="hidden">

                        </div>

                        <div class="atualizado-em text-right mg-b30">* Para o plano selecionado, apenas as dependências companheiro, cônjuge, filho e menor tutelado são permitidas.</div>

                    </div> <!-- col-md-12 -->
                </div> <!-- row -->

                <div class="row buttons two">
                    <div class="col-sm-6 col-xs-12 btn1">
                        <a class="btn btn-primary btn-rounded btn-lg btn-outline" href="javascript:void" id="add_dependente" role="button">Adicionar dependentes</a>
                    </div>
                    <div class="col-sm-6 col-xs-12 btn2">
                        <button type="submit" class="btn btn-primary btn-rounded btn-lg">Prosseguir contratação</button>
                    </div>
                </div> <!-- row -->
            </form>
        </section>
    </main>

	@include('templates.footer')

</body>
</html>
