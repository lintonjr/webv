<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImpressosProposta extends Model
{
    protected $table        = "impressos_proposta";
    protected $primaryKey   = "codigo_impresso_proposta";
    public $timestamps      = false;
}
