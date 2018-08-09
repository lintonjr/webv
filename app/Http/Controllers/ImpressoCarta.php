<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImpressoCarta extends Controller
{
    public function impresso() {
        $contato = \App\ContatosOnline::find(session('codigo_contato'));
        $contrato = \App\Contratos::find($contato->codigo_contrato);
        $dados_contato = json_decode($contato->dados);

        $_estilo = "
        <meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />
        <style>
            * {
                font-size: 20px;
                text-align: justify;
            }
        </style>";
        $_top = "<img src='".url('/')."/img/top.png' width='1000px'/>";
        $_bottom = "<img src='".url('/')."/img/bottom.png' width='1000px'/>";

        $_meio_p1 = "
            <p style='text-align: center; margin-top: 0;'><b>CARTA DE ORIENTAÇÃO AO BENEFICIÁRIO </b></p>
            <p>Prezado(a) Beneficiário(a), </p>
            <p>A <b>Agência Nacional de Saúde Suplementar (ANS)</b>, instituição
            que regula as atividades das operadoras de planos privados de assistência à
            saúde e tem como missão defender o interesse público, vem, por meio desta,
            prestar informações para o preenchimento do FORMULÁRIO DE DECLARAÇÃO DE SAÚDE.</p>

            <p>1 - O QUÊ É DE DECLARAÇÃO DE SAÚDE?</p>

            <p>É o formulário que acompanha o Contrato do Plano de Saúde,
            onde o beneficiário ou seu representante legal deverá informar as doenças ou
            lesões preexistentes de que saiba ser portador ou sofredor no momento da
            contratação do plano. Para o seu preenchimento, o beneficiário tem o direito de
            ser orientado, gratuitamente, por um médico credenciado/ referenciado pela
            operadora. Se optar por um profissional de sua livre escolha, assumirá o custo
            desta opção, portanto, se você (beneficiário) toma medicamentos regularmente,
            consulta médicos por problema de saúde do qual conhece o diagnóstico, fez qualquer
            exame que identificou alguma doença ou lesão, esteve internado ou submeteu-se a
            alguma cirurgia, DEVE DECLARAR ESTA DOENÇA OU LESÃO.</p>

            <p>2 - AO DECLARAR DOENÇA E/OU LESÃO DE QUE O BENEFICIÁRIO SAIBA
            SER PORTADOR NO MOMENTO DA CONTRATAÇÃO:</p>
            <p>2.1 - A operadora NÃO poderá impedi-lo de contratar o plano de
            saúde. Caso isto ocorra, encaminhe a denúncia à ANS.</p>
            <p>2.2 - A operadora deverá oferecer: cobertura total ou COBERTURA
            PARCIAL TEMPORÁRIA (CPT), podendo ainda oferecer o Agravo, que é um acréscimo
            no valor da mensalidade, pago ao plano privado de assistência à saúde, para que
            se possa utilizar toda a cobertura contratada, após os prazos de carências
            contratuais. </p>
            <p>2.3 - No caso de CPT, haverá restrição de cobertura para
            cirurgias, leitos de alta tecnologia (UTI, unidade coronariana ou neonatal) e
            procedimentos de alta complexidade - PAC (tomografia, ressonância,
            tratamento radiológico, etc.*) EXCLUSIVAMENTE relacionados à doença ou lesão declarada, até 24
            meses contados desde a assinatura do contrato. Após o período máximo de 24 meses da assinatura
            contratual, a cobertura passará a ser integral de acordo com o plano contratado.</p>
            <p>2.4 - NÃO haverá restrições de cobertura para consultas médicas, internações não cirúrgicas, exames e
            procedimentos que não sejam de alta complexidade, mesmo que relacionados à doença ou lesão
            preexistente declarada, desde que cumpridos os prazos de carências estabelecidas no contrato. </p>
            <p style='margin-bottom: 0px;'>2.5 Não caberá alegação posterior de omissão de informação da
            Declaração de Saúde por parte da operadora para esta doença ou lesão.</p>
        ";

        $_meio_p2 = "
            <p>3 - AO NÃO DECLARAR DOENÇA E/OU LESÃO DE QUE O BENEFICIÁRIO SAIBA SER PORTADOR NO MOMENTO DA CONTRATAÇÃO:</p>
            <p>3.1 A operadora poderá suspeitar de omissão de informação e,
            neste caso, deverá comunicar imediatamente ao beneficiário, podendo oferecer
            CPT, ou solicitar abertura de processo administrativo junto à ANS, denunciando
            a omissão da informação. </p>
            <p>3.2 Comprovada a omissão de informação pelo beneficiário, a
            operadora poderá RESCINDIR o contrato por FRAUDE e responsabilizá-lo pelos
            procedimentos referentes a doença ou lesão não declarada.</p>
            <p>3.3 Até o julgamento final do processo pela ANS, NÃO poderá
            ocorrer suspensão do atendimento nem rescisão do contrato. Caso isto ocorra,
            encaminhe a denúncia à ANS.</p>
            </div>
            <p>ATENÇÃO! Se a operadora oferecer redução ou isenção de carência, isto
            não significa que dará cobertura assistencial para doença ou lesão que o
            beneficiário saiba ter no momento da assinatura contratual. Cobertura Parcial
            Temporária - CPT - NÃO é carência! O beneficiário, portanto, não deve deixar de
            informar se possui alguma doença ou lesão ao preencher o Formulário de
            Declaração de Saúde!</p>
            <p>OBSERVAÇÕES: Para consultar a lista completa de procedimentos de alta
            complexidade - PAC, acesse o Rol de Procedimentos e Eventos em Saúde da ANS no
            endereço eletrônico: www.ans.gov.br - Perfil Beneficiário.Em caso de dúvidas,
            entre em contato com a ANS pelo telefone 0800-701-9656 ou consulte a página da
            ANS - www.ans.gov.br - Perfil Beneficiário.</p>
            <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><p text-align: left'>Assinado eletronicamente por " . $dados_contato->contratos[0]->declaracao_nome . " com o usuário " . $dados_contato->contratos[0]->declaracao_login . " em " . date_format(date_create_from_format("Y-m-d H:i:s", $dados_contato->contratos[0]->declaracao_data), 'd/m/Y \à\s H:i:s') . " através do IP " . $dados_contato->contratos[0]->declaracao_ip . "</p>
        ";

        $_impresso = $_estilo . $_top . $_meio_p1 . $_meio_p2 . $_top . $_bottom;

        $nome_impresso = date("YmdHis") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
		file_put_contents(storage_path() . '/app/temp/' . $nome_impresso . ".html", $_impresso);

		$comando = base_path() . '/app/Http/Controllers/wkhtmltox/bin/wkhtmltopdf --footer-center [page]/[topage] ' . storage_path() . '/app/temp/' . $nome_impresso . '.html ' . storage_path() . '/app/temp/' . $nome_impresso . '.pdf';
		exec($comando);

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename=declaracao_de_saude.pdf');
        @readfile(storage_path() . '/app/temp/' . $nome_impresso . '.pdf');
    }
}
