<?php

namespace App\Task;

use Closure;

class QuitarCaracteresString {
    

    public function handle($data,Closure $next){
        $EliminarCaracteres = function($item){
            if(!is_numeric(str_replace(',','.',$item))){
                $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹", "Ñ","'",'"');
                $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E", "N","","");
                $item = str_replace($no_permitidas, $permitidas, $item);
                return preg_replace('([^A-Za-z0-9 ])', ' ', $item);
            }
            return $item;
        };
        $data = is_array($data) ? array_map($EliminarCaracteres,$data) : $EliminarCaracteres($data);
        return $next($data);
    }
}