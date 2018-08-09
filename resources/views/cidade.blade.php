<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<style>
#bg {
    background-image: url('{{url("/")}}/img/background-cidade.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    position: fixed;
    height: 100%;
    width: 100%;
}
#outside {
    background-color: rgba(0, 153, 93, 0.2);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    max-width: 100%;
    height: 100%;
}
.topo {
    width: 100%;
    height: 97px;
    background-color: #FFFFFF;
    border-bottom: 1px solid #C3C8C8;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 50;
}
.topo .centralizar {
    width: 960px;
    padding: 0 20px;
    margin: 0 auto;
}
.topo .logo {
    width: 199px;
    height: 62px;
    float: left;
    padding: 0px;
    margin: 18px 18px 0 0;
    background: url(/img/bg-logo.gif) no-repeat right 0;
}
.topo h2 {
    font: 16px/90% 'Unimed Slab', "Trebuchet MS", Helvetica, sans-serif;
    color: #00995d;
    margin: 31px 50px 0 0;
    float: left;
}
.topo h2 strong {
    font-size: 20px;
}

.outside div {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
}

.box-login {
    width: 470px;
    position: relative;
    z-index: 2;
    margin: 130px auto 0;
    max-width: 100%;
}

.topo-box-login {
    background-color: #BED72A;
    padding: 19px 20px;
}

.box-login fieldset {
    background-color: #FFFFFF;
    padding: 18px 10px 20px 20px;
}

.topo-box-login h3 {
    font: 24px/100% 'Unimed Slab', "Trebuchet MS", Helvetica, sans-serif;
    color: #00995d;
    margin-bottom: 7px;
}

.topo-box-login h3 span {
    font-weight: bold;
}

.topo-box-login .border-title {
    display: block;
    width: 350px;
    height: 8px;
    background-color: #00995d;
    margin-bottom: 10px;
}

.topo-box-login p {
    font-family: 'Unimed Slab';
    line-height: 20px;
    font-size: 16px;
    color: #00995d;
}

.bt-cidade {
    font-family: "Trebuchet MS";
    border: 1px solid #00995d !important;
    background: #00995d !important;
    color: #FFF !important;
    cursor: pointer;
    font-weight: normal;
    overflow: visible;
    padding: 10px;
    text-shadow: none;
    width: auto;
    display: block;
    margin-bottom: 5px;
    display: table;
}

</style>
<body>

    <div class="topo">
        <div class="centralizar">
            <h1 class="logo">
                <img width="160px" height="60px" src="{{url('/')}}/img/logo_unimed_mobile.png" alt="Página Inicial">
            </h1>
            <h2>WEBVENDAS <br/><strong>ONLINE</strong></h2>
	    <img src="{{url('/')}}/img/logo_client.svg" style="margin-top: 10px" width="80px">
        </div>
    </div>

    <div id="bg">
        <div id="outside">
            <div class="conteudo clearfix">
                <div class="box-login">
                    <div class="topo-box-login">
                        <h3>BEM-VINDO AO <span>WEBVENDAS ONLINE</span></h3> <span class="border-title"></span>
                        <p>Contratação de plano de saúde completamente online.</p>
                    </div>
                    <fieldset>
                        <p>Para continuar, selecione a cidade do plano que você deseja adquirir*.</p>
                        @foreach ($cidades as $cidade)
                            <a href='{{route("selecionar-cidade", ["codigo_cidade" => $cidade->codigo_cidade])}}' class='bt-cidade'><i class="fa fa-angle-right" aria-hidden="true"></i> {{$cidade->nome_cidade}} / {{$cidade->uf_cidade}}</a>
                        @endforeach
                        <div class="atualizado-em mg-t10">
                               * Só é possível a compra de plano se você tiver residência na mesma UF da cidade onde o plano está sendo vendido.
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
