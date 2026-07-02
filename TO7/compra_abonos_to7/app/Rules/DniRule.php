<?php
/*** REGLA PERSONALIZADA PARA EL validate() DEL CONTROLLER ***/

namespace App\Rules;
// la función que Laravel da para marcar el error
use Closure;
// interfaz que Laravel exige para reglas personalizadas modernas. 
use Illuminate\Contracts\Validation\ValidationRule;


class DniRule implements ValidationRule { 

    /* Laravel llama a este método automáticamente al validar.

       $attribute -- nombre del campo (tipo)
       $value -- valor enviado (el UUID del tipo)
       $fail -- función para marcar error
    */
       
    public function validate(string $attribute, mixed $value, Closure $fail): void {

        $value = strtoupper($value);
        if(!preg_match('/^\d{8}[A-Z]$/', $value)) {
            // preg_match() devuelve 0 si cadena no coincide, false si el patrón es erroneo
            $fail('El :attribute no es válido.');
            return;
        }
        
        // Calcula letra correcta, la esperada
        $numero = intval(substr($value, 0, 8)); // intval(): Conversión del valor a int. Si no se hizo, da 0.
        $letra = $value[8];
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letraEsperada = $letras[$numero % 23];
        
        if($letra !== $letraEsperada) {
            $fail('La letra del :attribute no corresponde a los números.');
            return;
        }
        
    }

}