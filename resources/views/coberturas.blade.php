<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Coberturas | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
        <hgroup>
            <h1>Coberturas</h1>
            <h2>Plano individual e familiar</h2>
        </hgroup>
        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="long_card">
                        <p>
                            Para consultar as coberturas e procedimentos garantidos, acesse diretamente o site da ANS (Agência Nacional de Saúde Suplementar) <a href='http://www.ans.gov.br/planos-de-saude-e-operadoras/espaco-do-consumidor/' target='_blank'>clicando aqui</a>.
                        </p>
                        1. No menu esquerdo, clique em Verificar Cobertura do Plano;<br/>
                        2. Selecione a característica do plano desejado;<br/>
                        3. Digite o nome do procedimento que você deseja verificar;<br/>
                        4. Selecione o procedimento em um dos resultados que a tabela irá exibir. Clique no botão continuar abaixo da tabela de resultados.<br/>
                    </div>
                </div>
            </div>
        </section>
    </main>
	@include('templates.footer')
</body>
</html>
