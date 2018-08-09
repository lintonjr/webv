<!DOCTYPE html>
<html lang="pt-br">
@include('templates.head', ['titulo' => 'Minha Conta | WebVendas'])
<body>
    @include('templates.navbar')
    <main class="container">
        <hgroup>
            <h1>Prazo Excedido</h1>
        </hgroup>

        @if ($outros_contatos)
        <div class="alert alert-info alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Foi detectado que você possui outras contratações neste usuário. Alterne de contrato aqui.
            <ul>
            @foreach ($outros_contatos as $outro_contato)
                <li class='outros_contatos {{($outro_contato->codigo_contato == $contato->codigo_contato ? 'mesmo-contato' : '')}}'><a href='{{route("altera-contato", ["codigo_contato" => $outro_contato->codigo_contato])}}'>@php echo '#' . ($loop->index + 1) . ' - Titular: ' . $outro_contato->nome_titular; @endphp</a></li>
            @endforeach
            </ul>
        </div>
        @endif

        @if(session('mensagem'))
            <div class="row">
                <div class="col-md-12">
                    {!! session('mensagem') !!}
                </div>
            </div>
        @endisset

        @include('templates.menuadicional')

        <div class="row">
            <div class="col-md-12">
                <p class="alert alert-danger">Prazo para envio das informações excedido. Neste caso, dirija-se a um de nossos escritórios de atendimento para contratação presencial.</p>
            </div>
        </div>

        <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Matriz Manaus:</b><br/>Rua Rio Amapá, 374 - Nossa Sra. das Graças - Manaus-AM - CEP: 69053-150 / Fone: (92) 3303-8000 e 3303-8003</div>
        <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Belém:</b><br/>Trav. Humaitá, 2778 - Marco - Belém-PA - CEP: 66095-220 / Fone: (91) 3344-0802</div>
        <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><b>Filial Macapá:</b><br/>Rua Mendonça Furtado, 2278 - Sala B - Santa Rita - Macapá-AP - CEP: 68900-060 / Fone: (96) 3223-3646</div>
        <div class="col-md-3" style='border: 1px solid #eee; height: 135px;'><B>Filial Boa Vista:</b><br/>Rua Coronel Mota, 1668 - Centro - CEP: 69301-120 / Fone: (95) 3198-2618</div>

    </main>
    @include('templates.footer')
</body>
</html>
