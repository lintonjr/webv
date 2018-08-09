<footer id="rodape">
    <div class="container">
        <div class="row pd-b30 hidden-sm hidden-xs">
            <div class="col-md-12">
                <ul class="nav navbar-nav">
                    <li><a href="{{ route('planos') }}">Planos de Saúde</a></li>
                    <li><a href="{{ route('carencias') }}">Carências</a></li>
                    <li><a href="{{ route('coberturas') }}">Coberturas</a></li>
                    <li><a href="{{ route('rede') }}">Rede de Atendimento</a></li>
                    <li><a href="{{ route('duvidas') }}">Dúvidas</a></li>
                </ul>
            </div>
        </div>
        <!-- .row -->
        <div class="row pd-b60 data_footer">
            <div class="col-sm-4 col-xs-12 data_footer_client">
                Unimed Fama © 2017<br> Registro ANS 313971
            </div>
            <div class="col-sm-4 text-center col-xs-12 text-center">
                <a class='btn btn-green' href='{{url("/")}}/documentos/manual_webvendas_v1.0.pdf' target='_blank' style='padding: 5px 30px'><i class="fa fa-question-circle" aria-hidden="true"></i> &nbsp;Instruções</a>
            </div>
        </div>
        <!-- .row -->
    </div>
    <!-- .container -->

    <div class="login_nav login_nav_bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 col-xs-4 text-left">
                    <a href="http://unimedfama.com.br/user/login" target="_blank"><span class="fa fa-fw fa-home"></span> Portal</a>
                </div>
                <div class="col-sm-4 col-xs-4 text-center hidden-xs">
                    <a href="tel:9536231873"><span class="fa fa-fw fa-phone"></span>(95) 3623 - 1873</a>
                </div>
                <div class="col-sm-offset-0 col-sm-4 col-xs-4 col-xs-offset-4 text-right">
                    <a href="{{ route('minha-conta') }}" class="btn btn-green btn-xs"><span class="fa fa-fw fa-user"></span> Minha Conta</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/script.js?v=3"></script>
<script src="/js/jquery.mask.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.pt-BR.min.js"></script>
<script src="/js/progress-bar.js"></script>

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-96831485-4', 'auto');
    ga('send', 'pageview');
</script>
