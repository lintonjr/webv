<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\ContatosOnline;
use App\Mail\Email;
use App\Mail\InformaAdmins;
use \App\Usuarios;
use \App\Planos;
use \App\Geral;

class ComprarFinalController extends Controller
{
    public function index() {

        if (empty(session('codigo_contato'))) {
            $mensagem = "Ocorreu um erro ao fazer sua requisição. Por favor, tente novamente mais tarde.";
        } else {
            $contato            = ContatosOnline::find(session('codigo_contato'));
            $plano              = Planos::find($contato->codigo_plano);
            $email              = new Email();
            $senha              = Geral::CriaSenhaAleatoria(10);
            $usuario_anterior   = Usuarios::where('login', $contato->email)->first();
            $dados              = json_decode($contato->dados);

            $this->EnviaSms($contato, $dados->contratos['0']->protocolo, $plano);

            if ($usuario_anterior) {
                $usuario                    = Usuarios::find($usuario_anterior->codigo_usuario);
                $contato->codigo_usuario    = $usuario->codigo_usuario;
                $contato->save();
                session(['codigo_contato' => $contato->codigo_contato]);
                return redirect('contratos');
            } else {
                $usuario                    = new Usuarios();
                $usuario->login             = $contato->email;
                $usuario->email             = $contato->email;
                $usuario->senha             = hash('sha512', $senha);
                $usuario->tipo              = 3;
                $usuario->codigo_cidade     = session('codigo_cidade');
                $usuario->status            = 'A';
                $usuario->nome_real         = $contato->nome_titular;
                $usuario->codigo_infomed    = env('CODIGO_VENDEDOR');
                $usuario->save();
            }
            $contato->codigo_usuario    = $usuario->codigo_usuario;
            $contato->save();

            $email->nome       = $contato->nome_titular;
            $email->nome_plano = $plano->nome_plano;
            $email->email      = $contato->email;
            $email->senha      = $senha;
            $email->protocolo  = $dados->contratos['0']->protocolo;
            $email->subject    = "Processo de Contratação de Plano de Saúde";

            \Mail::to($contato->email)->send($email);

            if(count(\Mail::failures()) > 0) {
                $mensagem = "Para continuar a contratação do seu plano, precisamos enviar um email com suas credenciais de acesso. Porém, não foi possível enviar um email para o seu email de cadastro (".$contato->email."). Por favor, verifique se o seu email foi preenchido corretamente e tente novamente.";
            } else {
                $mensagem = "Para continuar a contratação do seu plano, enviamos um email contendo as credenciais de acesso. Por favor, acesse seu email de cadastro (".$contato->email.") e faça no login clicando no botão chamado 'Minha Conta', que fica no canto superior direito deste portal.";
                $email_informativo = new InformaAdmins();
                $email_informativo->nome = $contato->nome_titular;
                $email_informativo->email = $contato->email;
                $email_informativo->ddd = $contato->ddd;
                $email_informativo->telefone = $contato->celular;
                $email_informativo->data = date_format(date_create_from_format('Y-m-d H:i:s', $contato->data_acesso), 'd/m/Y H:i:s');
                $email_informativo->valor = number_format($contato->valor_plano, 2, ',', '.');
                $email_informativo->subject = "Nova contratação de plano online - WebVendas Online";
                $email_informativo->protocolo = $dados->contratos['0']->protocolo;
                \Mail::to(explode(',', env('EMAIL_INFORMA_ADM')))->bcc("wander@unimedfama.com.br")->send($email_informativo);
            }
        }

        return view('comprar-final', compact('mensagem'));
    }

    public function EnviaSms($contato, $protocolo, $plano) {
        $data_string = [
            "sendSmsRequest" => [
                "to" => "55".$contato->ddd.$contato->celular,
                "msg" => "Bem-vindo ao WebVendas Unimed. Voce selecionou o plano ".$plano->nome_plano." por RS ".number_format($contato->valor_plano, 2, ',', '.').". Este e seu protocolo: " . $protocolo . "."
            ]
        ];

        $ch = curl_init("https://api-rest.zenvia360.com.br/services/send-sms");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_string));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic dW5pbWVkYW06YThnWGRSTFR3ZA==',
                'Accept: application/json'
            )
        );
        $response = @json_decode(curl_exec($ch));
        curl_close($ch);
    }
}
