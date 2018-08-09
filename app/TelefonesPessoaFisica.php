<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelefonesPessoaFisica extends Model
{
    protected $table            = "telefones_pessoa_fisica";
    protected $primaryKey       = "codigo_telefone";
    public $timestamps          = false;
    protected $fillable         = ['*'];
}
