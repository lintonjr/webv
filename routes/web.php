<?php
use App\Webservice;

/* AREA EXTERNA */
Route::get('/',                                 'HomeController@index')                 ->name('index');
Route::get('/planos',                           'PlanosController@index')               ->name('planos');
Route::get('/carencias',                        'CarenciasController@index')            ->name('carencias');
Route::get('/coberturas',                       'CoberturasController@index')           ->name('coberturas');
Route::get('/rede',                             'RedeController@index')                 ->name('rede');
Route::get('/duvidas',                          'DuvidasController@index')              ->name('duvidas');
Route::get('/comprar-plano/{codigo_plano}',     'ComprarPlanoController@get')           ->name('comprar-plano-get');
Route::post('/comprar-plano',                   'ComprarPlanoController@post')          ->name('comprar-plano-post');
Route::post('/confirmacao-compra',              'ConfirmacaoCompraController@index')    ->name('confirmacao-compra');
Route::get('/comprar-final',                    'ComprarFinalController@index')         ->name('comprar-final');

Route::get('/mobile',               'MobileController@index')->name('mobile');
Route::get('/mobile/selecionar-cidade','MobileController@selecionarCidade')->name('mobile-selecionar-cidade');

/* AREA INTERNA */
Route::get('/minha-conta',          'MinhaContaController@index')       ->name('minha-conta');
Route::get('/esqueci-senha',        'MinhaContaController@esqueci')     ->name('esqueci-senha');
Route::post('/login',               'LoginController@index')            ->name('login');
Route::get('/sair',                 'LoginController@sair')             ->name('sair');
/* USUÁRIO LOGADO */
Route::get('/contratos',            'ContratosController@index')                ->name('contratos')             ->middleware('verificausuario');
Route::post('/salva-contrato',      'ContratosController@salva')                ->name('salva-contrato')        ->middleware('verificausuario');
Route::get('/usuarios',             'UsuariosController@index')                 ->name('usuarios')              ->middleware('verificausuario');
Route::post('/salva-usuario',       'UsuariosController@salva')                 ->name('salva-usuario')         ->middleware('verificausuario');
Route::get('/alterar-pagador',      'PagadorController@index')                  ->name('alterar-pagador')       ->middleware('verificausuario');
Route::post('/salva-pagador',       'PagadorController@salva')                  ->name('salva-pagador')         ->middleware('verificausuario');
Route::get('/refaz',                'ContratosController@refaz')                ->name('refaz')                 ->middleware('verificausuario');
Route::get('/refaz-do',             'ContratosController@refazdo')              ->name('refaz-do')              ->middleware('verificausuario');
Route::get('/novo-plano',           'NovoPlanoController@index')                ->name('novo-plano')            ->middleware('verificausuario');
Route::get('/novo-plano-do',        'NovoPlanoController@novoplanodo')          ->name('novo-plano-do')         ->middleware('verificausuario');
Route::post('/salva-comprovantes',  'ContratosController@comprovantes')         ->name('salva-comprovantes')    ->middleware('verificausuario');
Route::post('/salva-declaracao',    'ContratosController@declaracoes')          ->name('salva-declaracao')      ->middleware('verificausuario');
Route::post('/finalizacao-salva',   'ContratosController@finaliza')             ->name('finalizacao-salva')     ->middleware('verificausuario');
Route::get('/cartao-provisorio',    'CartaoProvisorioController@impresso')      ->name('cartao-provisorio')     ->middleware('verificausuario');
Route::post('/salva-contato',       'ContatosController@salva')                 ->name('salva-contato')         ->middleware('verificausuario');
Route::get('/modelo-proposta',      'ImpressoPropostaController@impresso')      ->name('modelo-proposta');
Route::get('/modelo-declaracao',    'ImpressoDeclaracaoController@impresso')    ->name('modelo-declaracao');
Route::get('/modelo-carta',         'ImpressoCarta@impresso')                   ->name('modelo-carta');
Route::get('/modelo-contrato',      'ContratosController@impresso')             ->name('modelo-contrato');
Route::get('/modelo-cpt',           'ImpressoCptController@impresso')           ->name('modelo-cpt');

/* REQUISICOES AJAX */
Route::post('/ajax/municipios', function() {
    return \App\Tradutor::MunicipiosDaUF("", \Request::input('uf'));
});
Route::post('/ajax/buscacep', function(){
    $webservice = new \App\Webservice;
    $retorno = $webservice->RetornaValorWebservice("Geral", "getDadosEndereco", array(\Request::input('cep')));
    if (@$retorno[0]['NUP_EST_SIGLA'] != \App\Cidades::find(session("codigo_cidade"))->uf_cidade) {
        return "";
    }
    return $retorno;
});
Route::get('/altera-contato/{codigo_contato}', function($codigo_contato) {
    session(['codigo_contato' => $codigo_contato]);
    return redirect('contratos');
})->name('altera-contato')->middleware('verificausuario');
Route::get('/imprimir-nfse', function() {
    $contato = \App\ContatosOnline::find(session('codigo_contato'));
    $contrato = \App\Contratos::find($contato->codigo_contrato);
    $_url_nota = str_replace("{NUMERO_NFSE}", $contrato->numero_nfse, env('URLNFSEGERADA'));
    $_url_nota = str_replace("{NUMERO_IDENTIFICACAO}", $contrato->numero_identificacao, $_url_nota);
    return Redirect::to($_url_nota);
})->name('imprimir-nfse')->middleware('verificausuario');
Route::get('/status-contrato', function() {
    $contato = \App\ContatosOnline::find(session('codigo_contato'));
    $dados = json_decode($contato->dados)->contratos[0];
    if (!@$dados->status) {
        echo "Informando seus dados";
    } else if ($dados->status == "ENVIAR_COMPROVANTES") {
        echo "Enviando comprovantes";
    } else if ($dados->status == "DECLARACAO_DE_SAUDE") {
        echo "Declaração de saúde";
    } else if ($dados->status == "FINALIZACAO") {
        echo "Finalização da proposta";
    } else if ($dados->status == "AUDITANDO") {
        echo "Verificação da Unimed";
    }
})->middleware('verificausuario');
Route::get('/selecionar-cidade/{codigo_cidade}', function($codigo_cidade) {
    session(['codigo_cidade' => $codigo_cidade]);

    if(\Session::get('mobile')){
        return redirect()->route('planos');
    }

    return redirect('/');
})->name('selecionar-cidade');

Route::get('/ajax/cns/{cpf}',function($cpf){

    if(!array_key_exists($cpf,config('global.cpfs'))){
        return "";
    }

    return config('global.cpfs')[$cpf];
});

Route::get("/get-impresso/{codigo_contato}/{impresso}/{protocolo}/{data_acesso}", function($codigo_contato, $impresso, $protocolo, $data_acesso) {
    $codigo_contato     = base64_decode(urldecode($codigo_contato));
    $impresso           = base64_decode(urldecode($impresso));
    $protocolo          = base64_decode(urldecode($protocolo));
    $data_acesso        = base64_decode(urldecode($data_acesso));

    $contato = \App\ContatosOnline::where('codigo_contato', $codigo_contato)->where('data_acesso', $data_acesso)->first();
    if (!count($contato)) { return false; }
    $dados = json_decode($contato->dados);
    if ($protocolo != $dados->contratos[0]->protocolo) { return false; }

    session(['codigo_contato' => $contato->codigo_contato]);

    if ($impresso == "declaracao_de_saude") {
        return redirect('modelo-declaracao');
    } else if ($impresso == "proposta") {
        return redirect('modelo-proposta');
    } else if ($impresso == "carta") {
        return redirect('modelo-carta');
    } else if ($impresso == "contrato") {
        return redirect('modelo-contrato');
    } else if ($impresso == "cpt") {
        return redirect('modelo-cpt');
    }

    return false;
})->name('get-impresso');

Route::post("/nova-senha", function() {
   $dados       = \Request::all();
   $email       = $dados['email'];
   $nova_senha  = \App\Geral::CriaSenhaAleatoria(10);
   $usuario     = \App\Usuarios::where('login', $email)->first();
   if (!count($usuario)) {
       return \Redirect::to('esqueci-senha')->withInput()->with('mensagem', '<div class="alert alert-danger">Usuário não encontrado</div>');
   } else {
       $usuario->senha = hash('sha512', $nova_senha);
       $usuario->save();
       $mail = new \App\Mail\NovaSenha();
       $mail->senha = $nova_senha;
       $mail->subject = "Recuperação de Senha";
       \Mail::to($email)->send($mail);
       return \Redirect::to('esqueci-senha')->withInput()->with('mensagem', '<div class="alert alert-success">Enviamos uma nova senha para seu email. Por favor, verifique.</div>');
   }
})->name("nova-senha");
Route::post('salva-cancelamento', function() {
    $dados = \Request::all();
    if ($dados['motivo_cancelamento'] == 1) {
        $motivo = "Desejo alterar a proposta (alteração de produto, inclusão/exclusão de pessoas, alteração de dados)";
    } else if ($dados['motivo_cancelamento'] == 2) {
        $motivo = "Encontrei oferta melhor na concorrência";
    } else if ($dados['motivo_cancelamento'] == 3) {
        $motivo = "Ingressarei em um plano empresarial";
    } else if ($dados['motivo_cancelamento'] == 4) {
        $motivo = "Desejo ficar sem plano de saúde";
    }
    $contato                            = \App\ContatosOnline::find(session('codigo_contato'));
    $contrato                           = \App\Contratos::where('codigo_contrato', $contato->codigo_contrato)->first();
    $contato_contrato                   = new \App\ContratosContatos();
    $contato_contrato->assunto          = "Pedido de Cancelamento";
    $contato_contrato->mensagem         = "Cliente fez o pedido de cancelamento em " . date('d/m/Y \à\s H:i:s') . " por motivo de: " . $motivo;
    $contato_contrato->visualizado      = 0;
    $contato_contrato->origem           = "cliente";
    $contato_contrato->destino          = "unimed";
    $contato_contrato->data             = date('Y-m-d H:i:s');
    $contato_contrato->codigo_contrato  = $contrato->codigo_contrato;
    $contato_contrato->save();
    return \Redirect::to('contratos')->withInput()->with('mensagem', '<div class="alert alert-success">Pedido de cancelamento enviado com sucesso!</div>');
})->name('salva-cancelamento');
