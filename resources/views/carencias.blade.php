<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Carências | WebVendas'])
<body>
	@include('templates.navbar')
	<main class="container">
		<hgroup>
            <h1>Carências</h1>
            <h2>Plano individual e familiar. Conforme <strong>Artigo 12 da Lei 9.656/1998.</strong></h2>
        </hgroup>

        <section>
            <div class="row">
                <div class="col-md-12">
                    <div class="long_card">
                        <div class="title">
                            <h2>Os serviços previstos neste contrato serão efetivamente prestados após o pagamento da 1ª (primeira) mensalidade e a partir da data de sua vigência constante na Proposta de Admissão, obedecendo-se os seguintes prazos:</h2>
                        </div>
                        <div class="content">
                                <ul>
                                    <li>24 (vinte e quatro) horas para os casos de acidentes pessoais, que são aqueles de evento exclusivo, com data caracterizada, diretamente externo, súbito, imprevisível, involuntário, violento e causador de lesão física que, por si só e independente de toda e qualquer outra causa, torne necessário o tratamento médico;</li>

                                    <li>30 (trinta) dias para os casos de consultas médicas e exames ambulatoriais de rotina (laboratoriais e raio x simples);</li>

                                    <li>300 (trezentos) dias para partos;</li>

                                    <li>24 (vinte e quatro) meses para internações e tratamento de doenças e lesões pré-existentes, exceto nos casos em que o usuário tenha optado por agravo;</li>

                                    <li>180 (cento e oitenta) dias para os demais casos, especialmente para a remoção aérea e/ou terrestre, inter-hospitalar.</li>
                                </ul>
                        </div>
                    </div>
                </div> <!-- col-md-12 -->
            </div> <!-- row -->
        </section>
    </main>
    @include('templates.footer')
</body>
</html>
