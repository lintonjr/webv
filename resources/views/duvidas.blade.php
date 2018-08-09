<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Dúvidas | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
		<hgroup>
            <h1>Dúvidas</h1>
            <h2>Perguntas mais frequentes referentes a contratação dos nossos planos de saúde.</h2>
        </hgroup>

        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="long_card">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            @foreach ($duvidas as $duvida)
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading{{$duvida->codigo_duvida}}">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$duvida->codigo_duvida}}" aria-expanded="true" aria-controls="collapse{{$duvida->codigo_duvida}}">
                                            {{$duvida->titulo}}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse{{$duvida->codigo_duvida}}" class="panel-collapse collapse @if($loop->first) in @endif" role="tabpanel" aria-labelledby="heading{{$duvida->codigo_duvida}}">
                                    <div class="panel-body">
                                        {!!$duvida->texto!!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div> <!-- panel-group -->
                    </div> <!-- long_card -->
                </div> <!-- col-md-12 -->
            </div> <!-- row -->
        </section>
    </main>
    @include('templates.footer')
</body>
</html>
