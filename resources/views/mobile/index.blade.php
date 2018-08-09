<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="keywords" content="Unimed Fama, Webvendas Unimed Fama, plano de saúde, online">
    <meta name="theme-color" content="#dfdfdf">
    <meta name="robots" content="follow, index">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/mobile.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/estilo.css">
    <link rel="stylesheet" href="/css/radio-mobile.css">

  </head>
  <body class=" background-mobile" >

    <div class="topo" style="">
        <div class="centralizar">
            <h1 class="logo">
                <img width="160px" height="60px" src="/img/logo_unimed_mobile.png" alt="Página Inicial">
            </h1>
                <h2>WEBVENDAS <br><strong>MOBILE</strong></h2>
        </div>

    </div>

    <div class="container" style="margin-top:{{!$errors->any() ? '100px' : '110px'}}">

      @if ($errors->any())
          <div style="" class="alert alert-danger">
            <p>Ops! Selecione uma cidade.</p>
          </div>
      @endif


      <h1 class="text-center" style="font-size:24px" >BEM - VINDO</h1>
      <p class="text-center" >Para continuar, selecione a cidade do plano que você deseja adquirir*.</p>


        <form action="{{route('mobile-selecionar-cidade')}}">

            <div class="text-center">

                <div class="box">
                  @forelse ($cidades as $cidade)
                    <input value="{{$cidade->codigo_cidade}}" type="radio" id="{{$cidade->codigo_cidade}}" name="cidade">
                    <label for="{{$cidade->codigo_cidade}}">{{$cidade->nome_cidade}} - {{$cidade->uf_cidade}}</label>
                  @empty

                  @endforelse
                </div>

                <i><small>* Só é possível a compra de plano se você tiver residência na mesma UF da cidade onde o plano está sendo vendido.</small></i>

                <div style="margin-top:35px" class="buttons">
                    <button type="submit" class="btn btn-lg btn-rounded btn-primary">Prosseguir</button>
                </div>
            </div>

        </form>


    </div>


{{--




 --}}
  </body>
</html>
