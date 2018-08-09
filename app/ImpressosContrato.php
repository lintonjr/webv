<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImpressosContrato extends Model
{
    protected $table        = "impressos_contrato";
    protected $primaryKey   = "codigo_impresso_contrato";
    public $timestamps      = false;
}
