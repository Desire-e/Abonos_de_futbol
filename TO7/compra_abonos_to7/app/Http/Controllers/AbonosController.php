<?php

// Namespace de este archivo
namespace App\Http\Controllers;


// Modelos
use App\Models\Abono;
use App\Models\TipoAbono;


// Reglas personalizadas para los validate([])
use App\Rules\DniRule;
use App\Rules\NacimientoRule;
use App\Rules\CuentaBancariaRule;


// Request (peticiones POST)
use Illuminate\Http\Request;
// Gestionar autentificación
use Illuminate\Support\Facades\Auth;
// Gestionar fechas
use Carbon\Carbon;
// Gestionar cookies
use Illuminate\Support\Facades\Cookie;
// *Ampliado - dompdf para descarga de tickets en PDF
use Barryvdh\DomPDF\Facade\Pdf;


class AbonosController extends Controller {

    /**
     * GET - solo dar vistas
     * Landing page 
     * */
    public function index() {
        if(Auth::check() == true) { return redirect()->route('abonos.listado'); }

        return view('abonos.index');
    }



    /** 
     * GET
     * Formulario de compra de abonos
     * */
    public function compra() {
        /* Comprueba logeado */
        if(Auth::check() == true) { return redirect()->route('abonos.listado'); }
        
        /* Trae todos los registros de tipo_abonos -- REEMPLAZADO POR COMPONENTE */
        // $tiposAbono = TipoAbono::all();
        
        /* Pasa los tipo_abonos al formulario de compra para el select de la vista */ 
        // return view('abonos.compra', compact('tiposAbono'));
        // Pasar modelo eloquent a la vista:
        // - compact('tiposAbono') equivale a ['tiposAbono' => $tiposAbono]
        // - En la vista existe solo la variable $tiposAbono
        // - $tiposAbono es array de registros en tipo_abonos, se recorre
        //   cada uno y se accede a sus propiedades con {{ $tipo->id  ... }}
        
        /* Mostrar vista */
        return view('abonos.compra');

    }


    /**
     * POST - obtiene datos, los usa y da vista / redireccion...
     * Inserta registro de abono comprado
     * */
    public function insert(Request $request) { // $request objeto que almacena datos de POST, GET...
    
        if(Auth::check() == true) { return redirect()->route('abonos.listado'); }

        /* 1º Validar inputs de formulario */
        $request->validate([
            'nombre'=> [
                'required', 
                'string', 
                'regex:/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/u'
            ],
            'dni'=> [ 
                'required',
                new DniRule  // regla personalizada
            ],  
            'nacimiento'=> [
                'required', 
                new NacimientoRule 
            ],
            'telefono'=> [ 
                'required',
                'regex:/^[679]\d{8}$/'
            ],
            'cuentaBancaria'=> [ 
                'required', 
                new CuentaBancariaRule 
            ],
            'abonoTipo'=> [
                'required', 
                'exists:tipo_abonos,id' // valida que el valor existe en columna id de tabla tipo_abonos
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
        ]);



        /* 2º Preparo los campos para el insert en BD (autogenerados + rellenados por usuario) */         
        
        // Obtengo el tipo_abonos seleccionado:
        $tipo = TipoAbono::where('id', $request->abonoTipo)->first(); 
        // SELECT * FROM tipo_abonos WHERE is = '...' LIMIT 1;
        // da objeto TipoAbono con propiedades: $tipo->id, $tipo->descripcion, $tipo->precio

        // Genero asiento + valido asiento:
        // Si existe ya ese asiento registrado, genero nuevo asiento
        // Si generé ya 5 veces -- error
        $codigoAsiento = null;
        $intento = 0;
        do{
            $codigo = $this->setAsiento($tipo->codigo);

            $existe = Abono::where('asiento', $codigo)->exists();
            // SELECT * FROM abonos WHERE asiento = :$codigo
            // ->exists() da true/false si encuentra o no registro
            
            if (!$existe) {
                $codigoAsiento = $codigo;
                break;
            }

            $intentos++;
        } while($intento < 5);
        if ($codigoAsiento === null) {
            // Aquí hago manualmente lo que haría validate() cuando encuentra errores en los inputs del formulario
            return redirect()
                ->back()    // redir hacia atrás (el formulario compra())
                ->withErrors([      // guarda el mensaje de error en sesión
                    'asiento' => 'No hay asientos disponibles en este momento.'
                ]);
                //->withInput(); 
                // - guarda el valor introducido en el input en sesión 
                // - solo para datos introducidos por usuario, para acceder a ellos mediante {{old()}}
                // - pero en este caso no tiene sentido
        }


        /* 3º Preparar campos para insertar registro en BD (validate() ok y $codigoAsiento !== null) */
        $datosAbono = [
            // 'id' => '', // id omitido -- se genera automaticamente al hacer ::create pues en Abono hago use HasUuid
            'fecha'=> now(), 
            'abonado'=> $this->setAbonado($request->nombre, $request->dni), 
            'edad'=> $edad = $this->setEdad($request->nacimiento),
            'telefono'=> $request->telefono, 
            'cuenta_bancaria' => str_replace([' ', '-'], '', $request->cuentaBancaria), // quita espacios / guiones 
            'tipo'=> $tipo->id,
            'asiento'=> $codigoAsiento,
            'precio'=> $this->setPrecio($edad, $tipo->precio, Abono::all()) 
        ];


        /* 4º Inserta en BD */
        $abono = Abono::create($datosAbono); // INSERT INTO abonos VALUES(...)


        /* 5º Crea cookies de algunos inputs -- ya validados e insertados, compra exitosa */
        $cookies = [
            Cookie::make('nombre', $request->nombre, 60*24*60), // 60 días
            Cookie::make('dni', $request->dni, 60*24*60),
            Cookie::make('nacimiento', $request->nacimiento, 60*24*60),
            Cookie::make('telefono', $request->telefono, 60*24*60),
            Cookie::make('cuentaBancaria', $request->cuentaBancaria, 60*24*60)
        ];



        /* 6º Redir al ticket() + envia cookies + el id generado por modelo Abono tras hacer ::create() */
        // respuesta http: redirige a ticket(), enviando id
        $response = redirect()->route('abonos.ticket', $abono->id);

        // añade al header de la respuesta http la cookie
        foreach ($cookies as $cookie) {
            $response->cookie($cookie);
        }

        return $response;
    }



    /** Muestra vista del ticket recien comprado **/
    public function ticket($id) {
        if(Auth::check() == true) {
            return redirect()->route('abonos.listado');
        }


        /* 1º Obtengo abono por su id */
        $abono = Abono::find($id); // SELECT * FROM abonos WHERE id = :id;

        
        /* 2º Reformateo los datos de la base de datos para mostrarlos en la vista del ticket */
        $abonado = explode(" - ", $abono->abonado);
        $nombre = trim($abonado[0]);
        $dni = trim($abonado[1]);

        $datosTicket = [
            'id' => $id,
            'fecha' => \Carbon\Carbon::parse($abono->fecha)->format('d/m/Y H:i'),
            // Carbon -- librería de fechas que usa Laravel
            // parse() -- convierte fecha en string, en un objeto Carbon (para poder 
            // formatear, sumar días, comparar, ...)
            
            'nombre' => $nombre,
            'dni' => $dni,
            'telefono' => $abono->telefono,
            'tipoAbono' => $abono->tipoAbono->descripcion,
            // ->tipoAbono -- Llama al método de Abono::tipoAbono() que marca la relación entre 
            // tabla abonos y tipo_abonos; devuelve un objeto eloquent TipoAbono, con el que 
            // se puede acceder a sus campos (->descripcion, ->precio ...)

            'asiento' => $abono->asiento,
            'precio' => number_format($abono->precio, 2, ',', '.'),
            // number_format(numero, decimales, separador_decimal, separador_miles)

            'edad' => $abono->edad
        ];

        /* 3º Le paso los datos reformateados a la vista */ 
        return view('abonos.ticket', $datosTicket);
        // Pasar un array a la vista: Laravel descompone automáticamente el array y 
        // crea variables en la vista.
        // - En la vista estará directamente los nombres de los indices del array 
        //   asociativo: {{ $fecha }},  {{ $nombre }}, {{ $dni }} ...
        // - No existe $datosTicket en la vista
    }


    /** Muestra un listado de los abonos comprados **/
    public function listado() {
        if(Auth::check() == false) { return redirect()->route('abonos.prohibido'); }

        /* Obtener abonos */
        // $abonos = Abono::orderByDesc('asiento')->get();
        
        // with()
        // Laravel trae todos los abonos, y todos los tipoAbonos necesarios emparejados con su abono
        // Evita problema n+1 (cada $abono->tipoAbono genera una consulta a la BD)
        $abonos = Abono::with('tipoAbono')->orderByDesc('asiento')->get();        


        /* Recorre los abonos y crea nuevo array */
        $abonos = $abonos->map(function ($abono) {
            // Codifica de binario a icono (ascii):
            // añade "campo" para la vista ->icono_base64
            // este contiene imagen "leíble" por navegador
            if ($abono->tipoAbono->icono) {
                $abono->icono_base64 = base64_encode($abono->tipoAbono->icono);
            }  
            else { $abono->icono_base64 = null; }
            
            // Revisa descuento
            if ($abono->edad < 12) {
                $abono->descuento = "Menor de 12 años";
            }
            elseif($abono->edad > 65){
                $abono->descuento = "Jubilado";
            }
            else { $abono->descuento = "Sin abono especial"; }

            return $abono;
        });


        return view('abonos.listado', compact('abonos'));   
    }

    /** Muestra página de aviso (contenido protegido) **/
    public function prohibido() {        
        /* Aviso solo para no logeados */
        if(Auth::check() == true) {
            return redirect()->route('abonos.listado');
        }

        return view('abonos.prohibido');   
    }





    /**
     * GET
     * Extra. Descarga ticket en archivo pdf
     * Librería: Barryvdh\DomPDF\Facade\Pdf
     * composer require barryvdh/laravel-dompdf
     * */
    public function downloadTicket($id){
        try {
            // 1 - Busco ticket por id
            $abono = Abono::findOrFail($id);

            // Reformateo los datos de la BD para mostrarlos en la vista del ticket
            $abonado = explode(" - ", $abono->abonado);
            $nombre = trim($abonado[0]);
            $dni = trim($abonado[1]);
            $dataT = [ 
                'fecha' => \Carbon\Carbon::parse($abono->fecha)->format('d/m/Y H:i'),                
                'nombre' => $nombre,
                'dni' => $dni,
                'telefono' => $abono->telefono,
                'tipo' => $abono->tipoAbono->descripcion,
                'asiento' => $abono->asiento,
                'precio' => number_format($abono->precio, 2, ',', '.'),
                'edad' => $abono->edad
            ];

            // 2 - Convierte una vista Blade (views/abonos/ticketpdf.blade.php) en un documento PDF:
            // Pasandole los datos a cargar en la vista.
            // Devuelve un objeto PDF $pdf (no es un archivo todavía)
            $pdf = Pdf::loadView('abonos.ticketpdf', $dataT);


            // 3 - Manda como respuesta una descarga al mismo tiempo que se genera en binario el archivo PDF:

            // streamDownload(callable $callback, string $name, array $headers = []) -- forma de descargar un 
            // archivo sin guardar en almacenamiento, generándolo "al vuelo" y enviándolo directamente al 
            // navegador forzando descarga (manda header Content-Disposition: attachment)
            
            // use ($pdf) -- mete la variable externa $pdf dentro de la función (ya que las funciones en PHP 
            // no pueden ver variables externas automáticamente).
            return response()->streamDownload(function () use ($pdf) {
                
                echo $pdf->output();
                // $pdf->output() -- obtiene el PDF en binario
                // echo -- va enviando cada dato binario
                
            }, 'ticket.pdf'); // nombre del archivo que recibe el usuario al descargarlo


        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => 'Error generando PDF',
                'errors' => $e->getMessage()
            ], 500);
        }
    }




    /** ------------- FUNCIONES AUXILIARES: campos autoset ------------- **/

    /**
     * Campo "abonado"
     * Formato "Nombre Apellidos - DNI"
     * */
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


    /**
     * Campo "edad" calculado
     * */
    private function setEdad($nacimiento) {
        $fechaNac = Carbon::createFromFormat('Y-m-d', $nacimiento);
        $edad = $fechaNac->age;
        return $edad;
    }


    /**
     * Campo "precio" + rebajas calculado
     * */
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


    /**
     * Campo "asiento" código calculado
     * */
    private function setAsiento($letra){       

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





/**
 * TODO.
 * PASAR A UN DOCX !!!
 */

/*** VALIDACIONES:
forma 1 -- ['nameDelInput'=>'regla|regla ...',  ]
forma 2 -- ['nameDelInput'=>['regla', 'regla' ...],  ]

Opcional (mensajes de error sin regla personalizada): 
['nameDelInput'=>['regla', 'regla' ...],  ], [name.regla =>'mensaje error', ...]

Mensajes de error automaticos sin especificar:
Crear carpeta /lang/es **MIRAR CRUD V3 
*/


/*** COOKIES

Crear cookie:
- Se suelen crear en el controlador
- Se pueden crear con helper cookie() o con facade Cookie
- Automaticamente se encriptan (por seguridad, pero puede obtenerse sin si 
  las necesitas para JS).

1. Helper cookie()
Ejemplo:
return response("Cookie creada")->cookie('nombre', 'Juan', 60);

Params obligatorios:
response(...) -- helper que crea un objeto de respuesta HTTP (Illuminate\Http\Response).
El objeto Response tiene métodos adicionales que permiten modificar encabezados, 
cookies, descargas, etc.

->cookie($nombreCookie, $valor, $duracionMinutos) -- añade una cookie a esa respuesta.

Params opcionales:
ruta, dominio, seguridad (secure), HTTP only, SameSite


2. Facade cookie()
Ejemplo:
use Illuminate\Support\Facades\Cookie;
return response("Cookie creada")->cookie(Cookie::make('nombre', 'Juan', 60));
// otra forma sin return
$cookie = Cookie::make('nombre', 'Juan', 60); // dura 60 minutos


Qué lo diferencia:
- Crea un objeto cookie más configurable.
- Mismos parámetros que el helper, pero como objeto.
- Útil si quieres reusar la cookie, pasarla entre varias respuestas, o hacer algo más 
  complejo antes de enviarla.


Importante:
En Laravel, cualquier llamada a redirect() o response() devuelve un objeto de respuesta HTTP 
(Illuminate\Http\Response o Illuminate\Http\RedirectResponse).
Por lo que puedes:
response()->cookie()
redirect()->route->()->cookie()

-----------------------------------------------------------------------------------------
2. Leer una cookie 
Desde un controlador:
$request->cookie('nombre');


Desde una vista Blade:
{{ request()->cookie('nombre') }}
// request() devuelve la instancia actual de la petición HTTP que hizo el navegador

-----------------------------------------------------------------------------------------
3. Eliminar una cookie
use Illuminate\Support\Facades\Cookie;
return response("Cookie eliminada")->cookie(Cookie::forget('nombre'));

-----------------------------------------------------------------------------------------
4. Crear cookie permanente (larga duración)
Cookie::make('nombre', 'Juan', 60 * 24 * 30); // 30 días

*/

