<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PessoasFisicas extends Model
{
    protected $table        = "pessoas_fisicas";
    protected $primaryKey   = "codigo_pessoa_fisica";
    public $timestamps      = false;
    protected $fillable     = ['*'];

    public function contratos() {
        return $this->belongsToMany("\App\Contratos", "contratos_pessoas_fisicas", "codigo_pessoa_fisica", "codigo_contrato")->withPivot("contratante", "codigo_dependencia");
    }

    public function declaracoes() {
        return $this->belongsToMany("\App\DeclaracoesDeSaude", "pessoas_fisicas_declaracoes_de_saude", "codigo_pessoa_fisica", "codigo_declaracao_de_saude")->withPivot("resposta", "complemento");
    }
}
