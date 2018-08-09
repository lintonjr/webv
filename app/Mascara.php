<?php

namespace App;

class Mascara {
    static function Mask($val, $mask) {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if(isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }

    static function FloatVal($string) {
        return (float)str_replace(",", ".", $string);
    }
}
