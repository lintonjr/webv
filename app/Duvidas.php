<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duvidas extends Model
{
    protected $table        = "duvidas";
    protected $primaryKey   = "codigo_duvida";
    public $timestamps      = false;
}
