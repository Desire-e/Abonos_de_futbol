<?php
/*** REGLA PERSONALIZADA PARA EL validate() DEL CONTROLLER ***/

namespace App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon; // para fechas



class NacimientoRule implements ValidationRule { 

    /* Laravel llama a este método automáticamente al validar.

       $attribute -- nombre del campo (tipo)
       $value -- valor enviado (el UUID del tipo)
       $fail -- función para marcar error
    */
       
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        
        /* Pasar a objeto fecha Carbon */
        // Carbon lanza una excepción (InvalidFormatException) si la fecha no puede parsearse.
        try{
            $fecha = Carbon::createFromFormat('Y-m-d', $value);
        }
        catch (\Exception $e) {
            $fail('La :attribute no es válida');
            return;
        }

        
        /* Calcular edad */
        $edad = $fecha->age; // Carbon tiene propiedad age para años completos

        if ($edad < 4 || $edad > 85) {
            $fail('La edad debe ser mayor a 4 años y menor a 85 años.');
        }

    }

}