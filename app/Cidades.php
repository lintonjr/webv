<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidades extends Model
{
    protected $table        = "cidades";
    protected $primaryKey   = "codigo_cidade";
    public $timestamps      = false;
    protected $fillable     = ['*'];
}
