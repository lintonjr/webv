<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contratos extends Model
{
    protected $table        = "contratos";
    protected $primaryKey   = "codigo_contrato";
    public $timestamps      = false;
    protected $fillable     = ['*'];

    public function pessoasfisicas() {
        return $this->belongsToMany("\App\PessoasFisicas", "contratos_pessoas_fisicas", "codigo_contrato", "codigo_pessoa_fisica")->withPivot("contratante", "codigo_dependencia");
    }
}
