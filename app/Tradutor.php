<?php

namespace App;

class Tradutor {

    static function AbrangenciaDoPlano($valor = "") {
        $valores = array(
            "N" => "Nacional",
            "M" => "Municipal",
            "E" => "Estadual"
        );
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function AcomodacaoDoPlano($valor = "") {
        $valores = array(
            "A" => "Apartamento/Individual",
            "E" => "Enfermaria/Coletivo"
        );
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function SexoDaPessoa($valor = "") {
        $valores = array(
            "M" => "Masculino",
            "F" => "Feminino"
        );
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function EstadoCivilDaPessoa($valor = "") {
        $valores = array(
            "R" => "Divorciado",
            "C" => "Casado",
            "D" => "Desquitado",
            "I" => "Indeterminado",
            "O" => "Outros",
            "S" => "Solteiro",
            "V" => "Viuvo",
            "U" => "União Estável",
            "A" => "Separado"
        );
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function OrgaoExpedidorDaRG($valor = "") {
        $webservice = new \App\Webservice;
        $retornows = $webservice->RetornaValorWebservice("Pessoa", "RetornaOrgaoExpedidor", array());
        $valores = array();
        foreach ($retornows as $retorno) {
            $valores[$retorno['ORGAO']] = $retorno['ORGAO'];
        }
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function Pais($valor = "") {
        $webservice = new \App\Webservice;
        $retornows = $webservice->RetornaValorWebservice("Pessoa", "RetornaPaises", array());
        $valores = array();
        foreach ($retornows as $retorno) {
            $valores[$retorno['PAI_CODIGO_PAIS']] = $retorno['PAI_NOME'];
        }
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function UF($valor = "") {
        $valores = array(
            "AC" => "AC",
            "AP" => "AP",
            "AM" => "AM",
            "BA" => "BA",
            "CE" => "CE",
            "DF" => "DF",
            "ES" => "ES",
            "GO" => "GO",
            "MA" => "MA",
            "MT" => "MT",
            "MS" => "MS",
            "MG" => "MG",
            "PA" => "PA",
            "PB" => "PB",
            "PR" => "PR",
            "PE" => "PE",
            "PI" => "PI",
            "RJ" => "RJ",
            "RN" => "RN",
            "RS" => "RS",
            "RO" => "RO",
            "RR" => "RR",
            "SC" => "SC",
            "SP" => "SP",
            "SE" => "SE",
            "TO" => "TO"
        );
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function MunicipiosDaUF($valor = "", $uf) {
        $webservice = new \App\Webservice;
        $retornows = $webservice->RetornaValorWebservice("Geral", "getMunicipiosDaUF", array($uf));
        $valores = array();
        foreach ($retornows as $retorno) {
            $valores[$retorno['NUP_CODIGO_MUNICIPIO']] = $retorno['NUP_NOME'];
        }
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

    static function TipoDeLogradouro($valor = "") {
        $webservice = new \App\Webservice;
        $retornows = $webservice->RetornaValorWebservice("Geral", "getTipoLogradouro", array());
        $valores = array();
        foreach ($retornows as $retorno) {
            $valores[$retorno['TPL_CODIGO_TIPO']] = $retorno['TPL_NOME'];
        }
        if (empty($valor)) { return $valores; }
        return $valores[$valor];
    }

}

?>
