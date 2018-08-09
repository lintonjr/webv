<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Planos de Saúde | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
		<hgroup>
            <h1>Planos de Saúde</h1>
            <h2>Para saber mais detalhes sobre cada plano, clique no título do plano desejado.</h2>
        </hgroup>
        <section>
            <div class="row">
                <div class="col-md-12">
                    @foreach ($planos as $plano)
                    <div class="card">
                        <div class="title">
                            <h2>{{$plano->nome_plano}}</h2>
                            <p><span class="rotulo">Registro ANS:</span> {{$plano->registro_ans}}</p>
                        </div>
                        <div class="more_info">
                            <!--<span class='row'><strong>Vigência:</strong> {{$plano->inicio_vigencia}} até {{$plano->fim_vigencia}}</span>-->
							<span class='row'><strong>Segmentação:</strong> {{ucwords(mb_strtolower($plano->descricao_plano))}}</span>
                            <span class='row'><strong>Abrangência:</strong> {{$plano->abrangencia}}{{($plano->abrangencia == 'Municipal' ? "/" . $cidade->nome_cidade : '')}}</span>
                            <span class='row'><strong>Acomodação:</strong> {{$plano->acomodacao}}</span>
                            <span class='row'><strong>Carência:</strong>
                                Os serviços contratados serão prestados aos beneficiários regularmente inscritos, após o cumprimento de carências.<br/>
                                As carências explicitadas neste tema serão contadas a partir da data da vigência contratual, ou seja, a partir da assinatura da proposta de adesão, da assinatura do contrato ou do primeiro pagamento, o que ocorrer primeiro, sendo assim especificadas:<br/>
                                a) 24 (vinte e quatro) horas para urgência e emergência;<br/>
                                b) 30 (trinta) dias para os casos de consultas médicas e exames ambulatoriais de rotina (laboratoriais e raio x simples);<br/>
                                c) 300 (trezentos) dias para partos a termo;<br/>
                                d) 24 (vinte e quatro) meses para doenças e lesões preexistentes;<br/>
								e) 180 (cento e oitenta) dias para todos os demais procedimentos;<br/>
                            </span>
							<span class='row'><strong>Tipo de Contratação:</strong> Individual/Familiar.</span>
                        </div>
                        <div class="buttons">
                            <a href="javascript:void(0)" class="btn btn-lg btn-rounded btn-primary btn-outline btn_more_info">Saiba Mais</a>
                            <a href="{{ route('comprar-plano-get', ['codigo_plano' => $plano->codigo_plano]) }}" class="btn btn-lg btn-rounded btn-primary">Comprar</a>
                        </div>
                    </div>
                    @endforeach
                </div> <!-- col-md-12 -->
            </div> <!-- row -->
        </section>
    </main>
    @include('templates.footer')
</body>
</html>
