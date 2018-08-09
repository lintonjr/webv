<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable
{
    protected $table        = "usuarios";
    protected $primaryKey   = "codigo_usuario";
    public $timestamps      = false;

    protected $fillable     = [
                                'codigo_usuario',
                                'login',
                                'email',
                                'nome_real',
                                'senha',
                                'codigo_infomed',
                                'tipo',
                                'codigo_cidade',
                                'ip_acesso',
                                'status',
                                'data_ultimo_acesso',
                                'cpf_vendedor'
                            ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

}
