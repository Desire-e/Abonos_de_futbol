<?php
/** CONTROLADOR DE UN COMPONENTE */

namespace App\View\Components;

use App\Models\TipoAbono;
// para sacar los tipos desde la BD
use Illuminate\View\Component;
// clase base de todos los componentes Blade


class SelectTipoAbono extends Component {
    public $listado;
    public $selectTipo;
    

    /** Constructor: se ejecuta cada que usas el componente **/
    public function __construct($selectTipo) {
        $this->selectTipo =  $selectTipo;
        $this->listado = TipoAbono::All();
    }
    // Recibe un valor desde la view que lo usa:
    // <x-SelectTipoAbono select-tipo="$abono->tipo" /> 
    // Equivale a hacer new SelectTipoAbono("$abono->tipo")

    
    /* Renderizado: muestra la vista del componente cuando otra view lo usa */
    public function render() { 
        return view('components.select-tipo-abono'); 
    }
    // Archivo: resources/views/components/select-tipo-abono.blade.php
    // - Con view() Laravel asume automáticamente /resources/views
    // - Los puntos (.) se convierten en carpetas (/)
}
