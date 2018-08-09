<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])

<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Refazer Proposta</h1>
            <h2>Confirma que deseja refazer proposta?</h2>
        </hgroup>

        @include('templates.menuadicional')

        @php
            if ((@$dados->contratos[0]->status == "AUDITANDO")) {
                @endphp
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                Não é possível alterar os dados do pagador neste estágio da proposta
                            </div>
                        </div>
                    </div>
                @php
            } else {
                @endphp
                    <div class="row">
                        <div class="col-md-12">
                            <div class="long_card">
                                <p>Para fazer alterações como data de nascimento dos beneficiários e também quantidade de dependentes, é necessário refazer toda a proposta, pois o valor do plano será alterado. Ao refazer a proposta, todos os dados cadastrados serão perdidos. Você confirma que deseja refazer a proposta?</p>
                            </div>
                        </div>
                    </div>
                @php
            }
        @endphp

        @php
            if ((@$dados->contratos[0]->status != "AUDITANDO")) {
                @endphp
                <div class="form-group">
                    <button type="button" class="btn btn-default" onclick="window.history.go(-1); return false;">Voltar</button>
                    <a href="{{route('refaz-do')}}" class="btn btn-success">Confirma refazer proposta</a>
                </div>
                @php
            }
        @endphp

    </main>
    @include('templates.footer')
</body>
</html>
