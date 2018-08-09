<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContatosOnline extends Model
{
    protected $table        = "contatos_online";
    protected $primaryKey   = "codigo_contato";
    public $timestamps      = false;
    protected $fillable     = [
                                'codigo_contato',
                                'email',
                                'ddd',
                                'celular',
                                'data_acesso',
                                'ip_acesso',
                                'codigo_plano',
                                'codigo_cidade',
                                'nome_titular',
                                'nascimento_titular',
                                'dados',
                                'codigo_usuario'
                            ];
}
