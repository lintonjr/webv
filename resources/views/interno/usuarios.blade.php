<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Dados do Usuário</h1>
            <h2>Tela para alteração de dados do usuário</h2>
        </hgroup>

        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        <form method="POST" action="{{route('salva-usuario')}}">
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type='text' class='form-control' name='nome_real' value='{{$usuario->nome_real}}'/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type='password' class='form-control' name='nova_senha'/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Repetir Nova Senha</label>
                        <input type='password' class='form-control' name='repetir_senha'/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-default" onclick="window.history.go(-1); return false;">Voltar</button>
                <button type="submit" class="btn btn-success">Continuar</button>
            </div>
        </form>
    </main>
@include('templates.footer')
</body>

</html>
