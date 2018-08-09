<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformaAdmins extends Mailable
{
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
        return $this->view('email.informa-admins',
        [
            'nome'      => $this->nome,
            'email'     => $this->email,
            'ddd'       => $this->ddd,
            'telefone'  => $this->telefone,
            'data'      => $this->data,
            'valor'     => $this->valor,
            'protocolo' => $this->protocolo
        ]
    )->from("flexaviso@gmail.com", "WebVendas")->subject($this->subject);
}
}
