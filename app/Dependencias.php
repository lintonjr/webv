<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dependencias extends Model
{
    protected $table        = "dependencias";
    protected $primaryKey   = "codigo_dependencia";
    public $timestamps      = false;
}
