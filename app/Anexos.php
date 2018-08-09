<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anexos extends Model
{
    protected $table        = "anexos";
    protected $primaryKey   = "codigo_anexo";
    public $timestamps      = false;
    protected $fillable     = ['*'];
}
