<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Contratação do Plano | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
		<hgroup>
            <h1>Contratando Plano</h1>
            <h2>Verifique se os dados estão corretos para prosseguir com a contratação.</h2>
        </hgroup>

        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="long_card">
                        <div class="title_full">
                            <h2>{{$nome_plano}}</h2>
                            <p><span class="rotulo">Registro ANS:</span> {{$codigo_ans}}</p>
                        </div>
                        <div class="content">
                            <span class="rotulo text-secondary-indigo">Titular</span>
                            <ul>
                                <li class="text-secondary-indigo">
                                    {{$nome_titular}}
                                    <ul>
                                        <li><span class="rotulo">E-mail:</span> {{$email_titular}}</li>
                                        <li><span class="rotulo">Telefone:</span> {{$ddd_titular}} {{$telefone_titular}}</li>
                                        <li><span class="rotulo">Faixa Etária:</span> {{$faixa_titular}}</li>
                                        <li><span class="rotulo">Valor:</span> R$ {{number_format($valor_titular, 2, ',', '.')}}</li>
                                    </ul>
                                </li>
                            </ul>
                            <span class="rotulo text-secondary-green1">Dependentes</span>
                            <ul class="short">
                                @forelse ($dependentes as $dependente)
                                    <li class="text-secondary-green1">{{$dependente['nome_dependente']}}
                                        <ul>
                                            <li>Faixa Etária: {{$dependente['faixa_dependente']}}</li>
                                            <li>Valor: R$ {{number_format($dependente['valor_dependente'], 2, ',', '.')}}</li>
                                        </ul>
                                    </li>
                                @empty
                                    <p>Sem dependentes.</p>
                                @endforelse
                            </ul>
                        <div class="comprar_plano2_box_valor text-right">
                            <div class="rotulo">Valor</div>
                            <div class="comprar_plano2_number_valor">R$ {{$valor_total}} / mês</div>
                        </div>
                        </div> <!-- content -->
                    </div> <!-- long_card -->
                </div> <!-- col-md-12 -->
            </div> <!-- row -->
            <div class="row buttons two">
                <div class="col-sm-6 col-xs-12 btn1">
                    <a class="btn btn-primary btn-rounded btn-lg btn-outline" href="{{route('comprar-plano-get', ['codigo_plano' => $codigo_plano])}}" role="button">Alterar dados</a>
                </div>
                <div class="col-sm-6 col-xs-12 btn2">
                    <a class="btn btn-primary btn-rounded btn-lg" href="{{route('comprar-final')}}">Quero contratar</a>
                </div>
            </div> <!-- row -->
        </section>
    </main>
	@include('templates.footer')
</body>
</html>
