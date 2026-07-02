<?php
/*** REGLA PERSONALIZADA PARA EL validate() DEL CONTROLLER ***/

namespace App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;



class CuentaBancariaRule implements ValidationRule { 
       
    public function validate(string $attribute, mixed $value, Closure $fail): void {

        $value = strtoupper($value);
        if(!preg_match('/^ES\d{2}([ -]?\d{4}){5}$/', $value)) { 
            $fail('Formato incorrecto. Debe ser ES00-0000-0000-0000-0000-0000.');
            return;
        } 


        $iban = str_replace([' ', '-'], '', $value);  // quitar guiones y espacios
        // str_replace(subcadBuscar, subcadReemp, cadena,contador)
        // Busca todas las ocurrencias de una subcadena dentro de otra cadena y las reemplaza por otra subcadena 
        // contador (opcional) -- nº veces que se hizo el reemplazo. 
        $ibanReordenado = substr($iban, 4) . substr($iban, 0, 4); // mueve los 4 primeros al final
        $ibanNumerico = strtr($ibanReordenado, [
            'A'=>'10','B'=>'11','C'=>'12','D'=>'13','E'=>'14','F'=>'15','G'=>'16',
            'H'=>'17','I'=>'18','J'=>'19','K'=>'20','L'=>'21','M'=>'22','N'=>'23',
            'O'=>'24','P'=>'25','Q'=>'26','R'=>'27','S'=>'28','T'=>'29','U'=>'30',
            'V'=>'31','W'=>'32','X'=>'33','Y'=>'34','Z'=>'35'
        ]);

        if(bcmod($ibanNumerico, '97') != 1) { // bcmod() -- calcula el resto (módulo) de una división entre números grandes
            $fail('El IBAN no es válido.');
            return;
        }
    }

}