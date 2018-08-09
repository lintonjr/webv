<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Adicionar novo plano</h1>
            <h2>Confirmação de adição de um novo plano</h2>
        </hgroup>

        @include('templates.menuadicional')

        <div class="col-md-12">
            <div class="long_card">
                <p>Você pode realizar a contratação de mais de um plano no mesmo usuário. Isso possibilita que o usuário possua mais de um contrato em sua conta, com seus respectivos titulares e dependentes. O usuário deverá alternar entre os contratos, pois não é possível editar os dois contratos ao mesmo tempo. Para continuar, clique no botão de confirmação abaixo:</p>
            </div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-default" onclick="window.history.go(-1); return false;">Voltar</button>
            <a href="{{route('novo-plano-do')}}" class="btn btn-success">Confirma adicionar novo plano</a>
        </div>

    </main>
    @include('templates.footer')
</body>
</html>
