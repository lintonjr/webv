<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Esqueci minha senha</h1>
            <h2>Enviaremos uma nova senha para seu email de cadastro.</h2>
        </hgroup>
        <section>
            @if(session('mensagem'))
                <div class="row">
                    <div class="col-md-12">
                        {!! session('mensagem') !!}
                    </div>
                </div>
            @endisset
            <div class="row">
                <div class="col-md-6">
                    <div class="long_card">
                        <form method="POST" action="{{route('nova-senha')}}">
                            <fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <label>Seu E-mail</label>
                                    <input class='form-control' type='text' name='email' value='{{old("email")}}' required />
                                <div class='form-group mg-t15 col-md-12'>
                                    <input class='btn btn-lg btn-primary btn-rounded' type='submit' value='Enviar' />
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main>
    @include('templates.footer')
</body>

</html>
