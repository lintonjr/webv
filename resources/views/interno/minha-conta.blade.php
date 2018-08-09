<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Continue sua Contratação</h1>
            <h2>Para acompanhar sua proposta ou seguir para os próximos passos, acesse aqui sua conta.</h2>
        </hgroup>
        <section>
            @isset($mensagem)
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-danger">
                        {!!$mensagem!!}
                    </div>
                </div>
            </div>
            @endisset
            <div class="row">
                <div class="col-md-6">
                    <div class="long_card">
                        <form method="POST" action="{{route('login')}}">
                            <fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <label>Email</label>
                                    <input class='form-control' type='text' name='email' value='{{old("email")}}' required />
                                <label>Senha</label>
                                    <input class='form-control' type='password' name='senha' required />
                                <div class='form-group mg-t15 col-md-12'>
                                    <input class='btn btn-lg btn-primary btn-rounded' type='submit' value='Login' />
                                    <a class='btn btn-lg btn-success btn-rounded' href='{{route("esqueci-senha")}}'>Esqueci minha senha</a>
                                </div>
                            </fieldset>
                        </form>
                    </div> <!-- long_card -->
                </div> <!-- col-md-12 -->
            </div> <!-- row -->
        </section>

    </main>
    @include('templates.footer')
</body>

</html>
