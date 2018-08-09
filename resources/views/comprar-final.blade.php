<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Contratando Plano | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
        <hgroup>
            <h1>Contratando Plano</h1>
            <h2>Processo de contratação de plano de saúde</h2>
        </hgroup>
        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="long_card">
                        <p>{{$mensagem}}</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
	@include('templates.footer')
</body>
</html>
