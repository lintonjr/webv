<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnderecosPessoaFisica extends Model
{
    protected $table        = "enderecos_pessoa_fisica";
    protected $primaryKey   = "codigo_endereco";
    public $timestamps      = false;
    protected $fillable     = ['*'];
}
