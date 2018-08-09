<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContratosContatos extends Model
{
    protected $table        = "contratos_contatos";
    protected $primaryKey   = "codigo_contrato_contato";
    public $timestamps      = false;
    protected $fillable     = ['*'];
}
