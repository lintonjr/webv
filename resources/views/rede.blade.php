<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Rede de Atendimento | WebVendas'])
<body>
	@include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Rede de Atendimento</h1>
            <h2>Conheça a rede de atendimento da Unimed em todo Brasil</a></h2>
        </hgroup>

        <section>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="long_card">
                        <div class="title">
                            <h2>Nacional</h2>
                        </div>
                        <div class="content">
                            <ul>
                                <li>Presente em 75% do território nacional;</li>
                                <li>101 mil médicos credenciados;</li>
                                <li>376 Unimed's;</li>
                                <li>3.702 hospitais;</li>
                                <li>4.759 clínicas;</li>
                                <li>3.467 laboratórios;</li>
                                <li>1.205 centros de diagnose;</li>
                                <li>16.431 recursos credenciados.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- col-md-4 -->

                <div class="col-md-6 col-sm-12">
                    <div class="long_card">
                        <div class="title">
                            <h2>Recursos Próprios</h2>
                        </div>
                        <div class="content">
                            <ul>
                                <li>69 hospitais;</li>
                                <li>73 prontos atendimentos;</li>
                                <li>3.223 leitos;</li>
                                <li>24 laboratórios;</li>
                                <li>14 centros de diagnósticos.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- col-md-4 -->

                <!--
				<div class="col-md-4 col-sm-12">
                    <div class="long_card">
                        <div class="title">
                            <h2>Em Roraima</h2>
                        </div>
                        <div class="content">
                            <ul>
                                <li>1 Município atendido</li>
                                <li>1 Hospital Credenciado</li>
                                <li>1 Hospital Próprio</li>
                                <li>1 Posto de Atendimento</li>
                                <li>24 Clínicas Conveniadas</li>
                                <li>2 Laboratórios Credenciados</li>
                                <li>1 Laboratórios Próprio</li>
                                <li>140 Médicos Cooperados</li>
                                <li>230 Colaboradores</li>
                            </ul>
                        </div>
                    </div>
                </div>
				-->
                <!-- col-md-4 -->
            </div>
            <!-- row -->
			<span class='atualizado-em'>* Atualizado em 10/01/2017</span>
        </section>
    </main>
    @include('templates.footer')
</body>

</html>
