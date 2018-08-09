<nav class="navbar navbar-default {{\Session::get('mobile') ? 'navbar-fixed-top' : ''}}">
    <div class="login_nav login_nav_top">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 col-xs-3 text-left">
                    <a href="http://unimedfama.com.br/user/login" target="_blank"><span class="fa fa-fw fa-home"></span> Portal</a>
                </div>
                <div class="col-sm-2 col-xs-4 text-center hidden-xs">
                    <a href="tel:9536231873"><span class="fa fa-fw fa-phone"></span>(95) 3623 - 1873</a>
                </div>
                @php $cidades = \App\Cidades::all() @endphp
                @if (Auth::check())
                    <div class="col-sm-6 col-xs-9 text-right">
                        <select style='background-color: transparent; border: 1px solid #00995d; padding: 3px 10px; margin-right: 5px' class='hidden-xs valueCidade'>
                            @foreach ($cidades as $cidade)
                                <option value="{{$cidade->codigo_cidade}}" {{($cidade->codigo_cidade == session('codigo_cidade') ? 'selected="selected"' : '')}}>{{$cidade->nome_cidade}} / {{$cidade->uf_cidade}}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('minha-conta') }}" class="btn btn-green btn-xs"><span class="fa fa-fw fa-user"></span> {{\App\Usuarios::find(Auth::id())->nome_real}}</a>
                        <a href="{{ route('sair') }}" class="btn btn-green btn-xs"><span class="fa fa-fw fa-close"></span> Sair</a>
                    </div>
                    <div class="col-xs-12 text-center hidden-md">
                        <select style='background-color: transparent; border: 1px solid #00995d; padding: 3px 10px; margin-right: 5px; margin-top: 15px' class='hidden-sm hidden-md hidden-lg valueCidade'>
                            @foreach ($cidades as $cidade)
                                <option value="{{$cidade->codigo_cidade}}" {{($cidade->codigo_cidade == session('codigo_cidade') ? 'selected="selected"' : '')}}>{{$cidade->nome_cidade}} / {{$cidade->uf_cidade}}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="col-sm-offset-0 col-sm-4 col-xs-8 col-xs-offset-0 text-right">
                        <select style='background-color: transparent; border: 1px solid #00995d; padding: 3px 10px; margin-right: 5px' class='hidden-xs valueCidade'>
                            @foreach ($cidades as $cidade)
                                <option value="{{$cidade->codigo_cidade}}" {{($cidade->codigo_cidade == session('codigo_cidade') ? 'selected="selected"' : '')}}>{{$cidade->nome_cidade}} / {{$cidade->uf_cidade}}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('minha-conta') }}" class="btn btn-green btn-xs"><span class="fa fa-fw fa-user"></span> Minha Conta</a>
                    </div>
                    <div class="col-xs-12 text-center hidden-md">
                        <select style='background-color: transparent; border: 1px solid #00995d; padding: 3px 10px; margin-right: 5px; margin-top: 15px' class='hidden-sm hidden-md hidden-lg valueCidade'>
                            @foreach ($cidades as $cidade)
                                <option value="{{$cidade->codigo_cidade}}" {{($cidade->codigo_cidade == session('codigo_cidade') ? 'selected="selected"' : '')}}>{{$cidade->nome_cidade}} / {{$cidade->uf_cidade}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
                aria-expanded="false">
    	        <span class="sr-only">Toggle navigation</span>
    	        <span class="icon-bar"></span>
    	        <span class="icon-bar"></span>
    	        <span class="icon-bar"></span>
    	      </button>
            <a class="navbar-brand" href="{{!\Session::get('mobile') ? route('index') : '#' }}">
    	      	<img width="160px" height="60px" src="/img/logo_unimed_mobile.png" alt="Logo Unimed"/>
    	    </a>
            <img src="/img/logo_client.svg" alt="Logo Lifemed" style='width: 80px; margin-top: 10px; margin-left: 5px;'/>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                @if(\Session::get('mobile'))
                    <li><a href="{{route('minha-conta')}}">Minha conta</a></li>
                @endif
                <li><a href="{{ route('planos') }}">Planos de Saúde</a></li>
                <li><a href="{{ route('carencias') }}">Carências</a></li>
                <li><a href="{{ route('coberturas') }}">Coberturas</a></li>
                <li><a href="{{ route('rede') }}">Rede de Atendimento</a></li>
                <li><a href="{{ route('duvidas') }}">Dúvidas</a></li>

                <!--<li><a href="{{ route('duvidas') }}">Mecanismos de Regulação</a></li>-->
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
