<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;
use App\Models\Abono;
use App\Models\TipoAbono;

// Reglas personalizadas para los validate([])
use App\Rules\DniRule;
use App\Rules\NacimientoRule;
use App\Rules\CuentaBancariaRule;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// Ampliado - uso de storage para servir imagenes almacenadas
use Illuminate\Support\Facades\Storage;



/* CODIGOS DE ESTADO:
200	Ok

400 Bad Request	    *Demasiado genérico
404	Not found   	Cliente (pidió algo inexistente)
401	No autorizado	Cliente (no autenticado)
422 Campos invalidos

500	Error interno	Servidor
*/

class AbonosController extends Controller {


    // - Sirve imágenes de medallas e iconos para el panel admin (listado)
    // - Almacenadas en disco publico local storage/app/public
    // - Se exponen con un symlink por defecto: public/storage - storage/app/public
    public function imageResources(){

        // http://localhost/TO6/compra_abonos_to6/ -- APP_URL .env

        try {
            $urlsMedals = [];
            $urlsIcons = [];

            // 1. Obtiene array de strings de rutas relativas de los archivos
            $filesMedals = Storage::disk('public')->files('imagenes/medals');  // [ "imagenes/medals/foto1.jpg", ... ]
            $filesIcons = Storage::disk('public')->files('imagenes/icons');

            // 2. Genera array de strings de rutas absolutas hacia las imagenes, para dar al cliente
            foreach ($filesMedals as $f) {
                // solo nombre sin extensión (claves del array)
                $fileName = pathinfo($f, PATHINFO_FILENAME);

                // genera: "foto1" => "http://localhost/TO6/compra_abonos_to6/" + "storage/" + "imagenes/medals/foto1.png"
                // $urlsMedals[$fileName] = Storage::disk('public')->url($f);
                
                /***** Por qué no uso Storage para generar ruta absoluta:
                La API devuelve URLs: 
                http://localhost/TO6v2/compra_abonos_to6/storage/imagenes/medals/gold.png
                Pero en cliente solo funcionan las URLs: 
                http://localhost/TO6v2/compra_abonos_to6/public/storage/imagenes/medals/gold.png

                Porque el servidor está sirviendo desde la raíz del proyecto y no desde /public:
                - Laravel está pensado para que el servidor apunte a /compra_abonos_to6/public, 
                por lo que Laravel genera /storage/...
                - Apache apunta a /compra_abonos_to6/, espera /public/storage/... 
                */

                // genera: "foto1" => "http://localhost/TO6/compra_abonos_to6/public/storage/" + "imagenes/medals/foto1.png"
                /* asset() genera URLs de archivos públicos
                    url() genera URLS de cualquier recurso (rutas, endpoints...)*/
                $urlsMedals[$fileName] = asset('storage/' . $f);
        
                // dd($fileName, $urlsMedals); // Detiene la ejecución y muestra los valores
            }
            foreach ($filesIcons as $f) {
                $fileName = pathinfo($f, PATHINFO_FILENAME);
                // $urlsIcons[$fileName] = Storage::disk('public')->url($f);
                $urlsIcons[$fileName] = asset('storage/' . $f);
            }
        
            // 3. Envía respuesta con datos
            return response()->json([
                'status' => true,
                'data' => ['medals' => $urlsMedals, 'icons' => $urlsIcons],
                'message' => null,
                'errors' => null,
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => null,
                'errors' => $e->getMessage()
            ], 404); // no encontrado
        }
        
        /* Ejemplo respuesta API
        {
            "status": true,
            "data": [
                "medals" => [
                    "imagen1" => "http://localhost/TO6/compra_abonos_to6/public/storage/imagenes/medals/imagen1.png",
                ],
                "icons" => [
                    "icon1" => "http://localhost/TO6/compra_abonos_to6/public/storage/imagenes/icons/icon1.png",
                ]
            ], 
            ...
        }
        */
    }

    /** Da registros de tipo_abonos para el formulario de compra de abonos **/
    public function tipoAbonos() {
        try {
            // Trae todos los registros de tipo_abonos
            $tiposAbono = TipoAbono::all();

            // Envía respuesta con datos
            return response()->json([
                'status' => true,
                'data' => $tiposAbono,
                'message' => null,
                'errors' => null,
            ], 200);

        } 
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => null,
                'errors' => $e->getMessage()
            ], 404); // no encontrado
        }
    }




    /** Inserta registro de abono comprado **/
    public function insert(Request $request) {
        try{
            /* 1º Valida campos de nuevo abono comprado */
            $validacion = Validator::make($request->all(), [
                    'nombre'=> [
                        'required', 'string', 'regex:/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/u'
                    ],
                    'dni'=> [ 
                        'required', new DniRule  // regla personalizada
                    ],  
                    'nacimiento'=> [
                        'required', new NacimientoRule 
                    ],
                    'telefono'=> [ 
                        'required', 'regex:/^[679]\d{8}$/'
                    ],
                    'cuentaBancaria'=> [ 
                        'required', new CuentaBancariaRule 
                    ],
                    'abonoTipo'=> [
                        'required', 'exists:tipo_abonos,id' // valida que el valor existe en columna id de tabla tipo_abonos
                    ],           
                    'terminosCheck'=>'required', 
                    // Autogenerados, no necesitan validación
                    'id'=>'', 'fecha'=>'', 'abonado'=>'', 'edad'=>'', 'asiento'=>'', 'precio'=>'', 'abonoTipoId'=>''
                ], 
                // Otros mensajes de error personalizados
                [
                    'terminosCheck.required' => 'Debe aceptar los términos.',
                    'nombre.regex' => 'El nombre solo puede contener letras.',
                    'telefono.regex' => 'El teléfono debe ser numérico, comenzar por 6, 7 o 9 y contener 8 números .',
                ]
            );

            // Si validacion falla -- respuesta fallida
            if($validacion->fails()) {
                return response()->json([
                    'status' => false,
                    'data' => $request->all(),
                    'message' => null,
                    'errors' => $validacion->errors(),
                ], 422); // datos invalidos
            }
            // Si validacion OK -- crea campos autogenerados, crea registro en abonos, respuesta OK
            else {

                /* 2º Preparo los campos para el insert en BD (autogenerados + rellenados por usuario) */         
                // Obtengo el tipo_abonos seleccionado:
                $tipo = TipoAbono::where('id', $request->abonoTipo)->first(); // objeto TipoAbono
                // SELECT * FROM tipo_abonos WHERE is = '...' LIMIT 1;

                // Genero asiento + valido asiento:
                // Si existe ya ese asiento registrado, genero nuevo asiento
                // Si generé ya 5 veces -- respuesta fallida
                $codigoAsiento = null;
                $intentos = 0;
                do {
                    $codigo = $this->setAsiento($tipo->descripcion);

                    $existe = Abono::where('asiento', $codigo)->exists();
                    // SELECT * FROM abonos WHERE asiento = :$codigo
                    // ->exists() da true/false si encuentra o no registro
                    
                    if (!$existe) {
                        $codigoAsiento = $codigo;
                        break;
                    }

                    $intentos++;
                } while($intentos < 5);
                if ($codigoAsiento === null) {
                    // inyecto error manual para el campo autogenerado
                    $validacion->errors()->add('asiento', 'No hay asientos disponibles en este momento.');

                    return response()->json([
                        'status' => false,
                        'data' => $request->all(),
                        'message' => null,
                        'errors' => $validacion->errors(),
                    ], 400); // bad request
                }

                /* 3º Preparar campos para insertar registro en BD (validate() ok y $codigoAsiento !== null) */
                $datosAbono = [
                    'fecha'=> now(), 
                    'abonado'=> $this->setAbonado($request->nombre, $request->dni), 
                    'edad'=> $edad = $this->setEdad($request->nacimiento),
                    'telefono'=> $request->telefono, 
                    'cuenta_bancaria' => str_replace([' ', '-'], '', $request->cuentaBancaria), // quita espacios/guiones 
                    'tipo'=> $tipo->id,
                    'asiento'=> $codigoAsiento,
                    'precio'=> $this->setPrecio($edad, $tipo->precio, Abono::all()) 
                ];

                /* 4º Inserta en BD */
                $abono = Abono::create($datosAbono); // INSERT INTO abonos VALUES(...)


                /* 5º Respuesta OK */
                return response()->json([
                    'status' => true,
                    'data' => [ 'id'=> $abono->id ], // manda id en respuesta para mostrar ticket después
                    'message' => 'Compra realizada correctamente',
                    'errors' => null,
                ], 200);
            }
        }
        // Respuesta fallida ante excepciones
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }




    public function ticket($id) {
        try {
            // Obtengo abono por su id
            $abono = Abono::findOrFail($id); 

            // Reformateo los datos de la base de datos para mostrarlos en la vista del ticket
            $abonado = explode(" - ", $abono->abonado);
            $nombre = trim($abonado[0]);
            $dni = trim($abonado[1]);

            $dataT = [ 
                'fecha' => \Carbon\Carbon::parse($abono->fecha)->format('d/m/Y H:i'),
                // Convierte string de fecha, en un objeto de fecha Carbon de laravel, dando formato
                // Al enviar JSON se pasa como un string
                
                'nombre' => $nombre,
                'dni' => $dni,
                'telefono' => $abono->telefono,
                'tipo' => $abono->tipoAbono->descripcion,
                // ->tipoAbono -- Llama al método de Abono::tipoAbono() que marca la relación entre 
                // tabla abonos y tipo_abonos; devuelve un objeto eloquent TipoAbono, con el que 
                // se puede acceder a sus campos (->descripcion, ->precio ...)

                'asiento' => $abono->asiento,
                'precio' => number_format($abono->precio, 2, ',', '.'),
                'edad' => $abono->edad
            ];

            // Envía respuesta con datos
            return response()->json([
                'status' => true,
                'data' => $dataT,
                'message' => null,
                'errors' => null,
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => null,
                'errors' => $e->getMessage()
            ], 404);
        }
    }






    /** ------------- FUNCIONES AUXILIARES: campos autoset ------------- **/

    /* Campo 'abonado' con formato "Nombre Apellidos - DNI" */
    private function setAbonado($nombre, $dni){
        $abonado = '';
        $abonadoNombre = '';
        $abonadoApellidos = '';
        
        $abonadoNombreApellidos = explode(" ", $nombre);
        $abonadoNombre = $abonadoNombreApellidos[0];
        for ($i = 1; $i < count($abonadoNombreApellidos); $i++){
            $abonadoApellidos .= $abonadoNombreApellidos[$i].' ';
        }
        $abonadoApellidos = trim($abonadoApellidos);
        $abonadoDni = $dni;
        
        return $abonado = $abonadoNombre . ' ' . $abonadoApellidos . ' - ' . $abonadoDni;
    }


    /* Calcula la edad */
    private function setEdad($nacimiento) {
        $fechaNac = Carbon::createFromFormat('Y-m-d', $nacimiento);
        $edad = $fechaNac->age;
        return $edad;
    }


    /* Calcula 'precio' total, con rebajas si las hay */
    private function setPrecio($edad, $tipoPrecio) {
        $rebaja = 0;
        $importeTotal = 0;
        
        // Calcula rebaja segun edad
        if($edad < 12) $rebaja = 80;
        if ($edad > 65) $rebaja = (50*$tipoPrecio)/100;

        // Aplica rebaja al precio base del abono
        $importeTotal = $tipoPrecio - $rebaja;
        return $importeTotal;
    }


    /* Genera campo de código de 'asiento' */
    private function setAsiento($tipoDesc){

        // ----- Primera letra del tipo de abono:
        $letra = strtoupper(substr($tipoDesc, 0, 1));         


        // ----- Bloque de asientos (1-5 inclusives):
        // rand(min, max): Genera int aleatorio entre un int minimo y máximo inclusives.
        $bloque = 'B' . rand(1,5);
        

        // ----- Fila dentro del bloque (0-29 inclusives):
        // Los números de fila menores de 10 serán rellenados con 0s a la izquierda.
        $filaNum = rand(0,29);
        if ($filaNum < 10){
            $filaNumCadena = "0" . "$filaNum";
        } 
        else {
            $filaNumCadena = "$filaNum";
        }
        $fila = 'F' . $filaNumCadena;


        // ----- Asiento dentro de la fila (0-199 inclusives):
        $maxAsientosPorFila = 140 + ($filaNum*2);
        $asientoNum = rand(0, $maxAsientosPorFila);
        // Los números de asiento menores de 100 serán rellenados con 0s a la izquierda.
        if ($asientoNum < 10){       // por ejemplo 009
            $asientoNumCadena = "00" . "$asientoNum";
        } 
        else if ($asientoNum < 100) { // por ejemplo 099
            $asientoNumCadena = "0" . "$asientoNum";
        }
        else { // en cualquier otro caso, por ejemplo 100
            $asientoNumCadena = "$asientoNum";
        } 
        $asiento = 'A' . $asientoNumCadena;


        return $codigoAsiento = $letra . $bloque . '/' . $fila . "-" . $asiento;
    }

}
