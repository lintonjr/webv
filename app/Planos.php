<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Planos extends Model
{
    protected $table            = "planos";
    protected $primaryKey       = "codigo_plano";
    public $timestamps          = false;

    public function getInicioVigenciaAttribute($value) {
        return date_format(date_create_from_format("Y-m-d", $value), "d/m/Y");
    }

    public function getFimVigenciaAttribute($value) {
        return date_format(date_create_from_format("Y-m-d", $value), "d/m/Y");
    }

    public function getRegistroAnsAttribute($value) {
        return Mascara::Mask($value, "###.###/##-#");
    }

    public function getAbrangenciaAttribute($value) {
        return Tradutor::AbrangenciaDoPlano($value);
    }

    public function getAcomodacaoAttribute($value) {
        return Tradutor::AcomodacaoDoPlano($value);
    }
}
