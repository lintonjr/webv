<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Email extends Mailable
{

    public $nome;
    public $nome_plano;
    public $email;
    public $senha;
    public $subject;
    public $protocolo;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.novo-usuario',
            [
                'nome'              => $this->nome,
                'nome_plano'        => $this->nome_plano,
                'email'             => $this->email,
                'senha'             => $this->senha,
                'protocolo'         => $this->protocolo
            ]
        )->from("flexaviso@gmail.com", "WebVendas")->subject($this->subject);
    }
}
