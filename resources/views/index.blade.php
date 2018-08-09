<!DOCTYPE html>
<html lang="pt-br">
    @include('templates.head', ['titulo' => 'Início | WebVendas :: Comprar plano de saúde online'])
<body>
	@include('templates.navbar')

	<!-- Chat box inicio -->

	<div class="chatbox chatbox--tray chatbox--empty">
		<div class="chatbox__title">
			<h5 style="color:white" >Atendimento Online</h5>
			<button class="chatbox__title__tray">
				<span></span>
			</button>
			<button class="chatbox__title__close">
				<span>
					<svg viewBox="0 0 12 12" width="12px" height="12px">
						<line stroke="#FFFFFF" x1="11.75" y1="0.25" x2="0.25" y2="11.75"></line>
						<line stroke="#FFFFFF" x1="11.75" y1="11.75" x2="0.25" y2="0.25"></line>
					</svg>
				</span>
			</button>
		</div>
		<div class="chatbox__body">
			<div class="chatbox__body__message chatbox__body__message--left">
				<img src="/img/callcenter.png" alt="Picture">
				<p>Nenhum de nossos consultores está disponível no momento, entraremos em contato o mais breve possível.</p>
			</div>
		</div>
		<form class="chatbox__credentials">
			<div class="form-group">
				<label for="inputName">Nome:</label>
				<input type="text" class="form-control" id="inputName" required>
			</div>
			<div class="form-group">
				<label for="inputEmail">E-mail:</label>
				<input type="email" class="form-control" id="inputEmail" required>
			</div>
			<button type="submit" class="btn btn-success btn-block">Enviar</button>
		</form>
		<textarea class="chatbox__message" placeholder="Digite aqui sua mensagem"></textarea>
	</div>

	<!-- Chat box fim -->

	<div class="container-fluid">
		<div class="row">
			<div class="jumbotron bg-primary-green2">
				<div class="container text-center text-secondary-green1 unimedsans">
			  		<p class="unimedsans text-uppercase mg-bottom5">Adquira agora o seu</p>
			  		<h1 class="unimedslab">Plano de Saúde</h1>
			  		<p class="unimedslab">Estamos presentes em 75% do território nacional</p>
			  		<p><a class="btn btn-primary btn-rounded btn-lg text-uppercase scroll" href="#contratar_plano" role="button">Contratar plano</a></p>
				</div>
			</div>
		</div>
	</div>

	<section class="container">
		<div class="row">
			<section class="col-md-12">
				<h2>Rede de Atendimento</h2>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="box_redeAtendimento b1">
							<span class="data">101 mil</span>
							<span class="description">médicos credenciados</span>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="box_redeAtendimento b2">
							<span class="data">16.431</span>
							<span class="description">recursos credenciados</span>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="box_redeAtendimento b3">
							<span class="data">4.759</span>
							<span class="description">clínicas</span>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3">
						<div class="box_redeAtendimento b4">
							<span class="data">3.702</span>
							<span class="description">hospitais</span>
						</div>
					</div>
				</div> <!-- /.row -->
			</section> <!-- /.col-md-12 -->
		</div> <!-- /.row -->
        <div class='atualizado-em text-right mg-t10'>* Atualizado em 10/01/2017</div>
	</section>

	<section class="container contato">
		<div class="row">
			<section class="col-md-12 text-center">
				<h2>Contato</h2>
				<p>
					Em caso de  dúvidas, entre em contato com a <strong>Lifemed</strong>, nossa corretora de seguros.
				</p>
				<p>
					Você também pode acessar nossa página de dúvidas onde respondemos as perguntas mais frequentes. Para acessá-la, <a href="{{route('duvidas')}}" rel="nofollow">clique aqui</a>.
				</p>
				<a href="tel:9536231873">(95) 3623 - 1873</a>
			</section>
		</div>
	</section>

	<section id="contratar_plano" class="full bg-secondary-green1 text-light bg_textura_diagonal">
		<div class="container">
			<div class="row">
				<section class="col-md-offset-3 col-md-6">
					<h2>Contratar plano</h2>
					<p class="sub">
                        <p class="text-center mg-t30">A Agência Nacional de Saúde (ANS) regulamentou a contratação de plano de saúde por meio de plataformas digitais. Este portal está atendendo todas as diretrizes dispostas na <a style='color: #b1d34a' href='http://www.ans.gov.br/component/legislacao/?view=legislacao&task=TextoLei&format=raw&id=MzMyNw==' target='_blank'>Resolução Normativa nº 413</a>, publicada em novembro de 2016.</p>
						<br/><p>Informe <strong>todos</strong> os dados a seguir para iniciarmos a contratação ou simulação do seu plano*.</p>
					</p>
					<form action="{{ route('comprar-plano-post') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="email">Informe seu email</label>
									<input required class="form-control" type="text" id="email" name="email" maxlength="100">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="telefone_ddd">DDD</label>
									<input required class="form-control isNumber" type="text" id="telefone_ddd" name="telefone_ddd" maxlength="2">
								</div>
							</div>
							<div class="col-md-10">
								<div class="form-group">
									<label for="telefone_numero">Número celular</label>
									<input required class="form-control isNumber" type="text" id="telefone_numero" name="telefone_numero" maxlength="9">
								</div>
							</div>
						</div>

            <div class='atualizado-em text-right mg-t10'>* Somente para novos contratos</div>

						<div class="form-group text-center mg-t30">
                <input type='submit' class='btn btn-lg btn-primary btn-rounded text-uppercase' value='Prosseguir'/>
						</div>

					</form>
				</section>
			</div>
		</div>
	</section>

    @include('templates.footer')

</body>
<script>
	(function($) {
    $(document).ready(function() {
        var $chatbox = $('.chatbox'),
            $chatboxTitle = $('.chatbox__title'),
            $chatboxTitleClose = $('.chatbox__title__close'),
            $chatboxCredentials = $('.chatbox__credentials');
        $chatboxTitle.on('click', function() {
            $chatbox.toggleClass('chatbox--tray');
        });
        $chatboxTitleClose.on('click', function(e) {
            e.stopPropagation();
            $chatbox.addClass('chatbox--closed');
        });
        $chatbox.on('transitionend', function() {
            if ($chatbox.hasClass('chatbox--closed')) $chatbox.remove();
        });
        $chatboxCredentials.on('submit', function(e) {
            e.preventDefault();
            $chatbox.removeClass('chatbox--empty');
        });
    });
})(jQuery);	
</script>
</html>
