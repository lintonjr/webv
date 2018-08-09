<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeclaracoesDeSaude extends Model
{
    protected $table        = "declaracoes_de_saude";
    protected $primaryKey   = "codigo_declaracao_de_saude";
    public $timestamps      = false;
}
