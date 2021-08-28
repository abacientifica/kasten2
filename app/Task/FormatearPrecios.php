<?php

namespace App\Task;

use Closure;

class FormatearPrecios {
    

    public function handle($data,Closure $next){
        $EliminarCaracteres = function($item){
            $item = str_replace(',', '.', $item);
            if(is_numeric($item)){
                return $item;
            }
            return $item;
        };
        $data = is_array($data) ? array_map($EliminarCaracteres,$data) : $EliminarCaracteres($data);
        return $next($data);
    }
}