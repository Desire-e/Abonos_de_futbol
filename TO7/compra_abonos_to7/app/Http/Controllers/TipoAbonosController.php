<?php

// Namespace de este archivo
namespace App\Http\Controllers;


// Modelos
use App\Models\Abono;
use App\Models\TipoAbono;


// Request (peticiones POST)
use Illuminate\Http\Request;
// Gestionar autentificación
use Illuminate\Support\Facades\Auth;


// Consultas explicitas a las BD * para AJAX
use Illuminate\Support\Facades\DB;
// Validar peticiones tipo API (en JSON) * para AJAX / API
use Illuminate\Support\Facades\Validator;


class TipoAbonosController extends Controller {


    /*******************************
     * TABLA TIPO ABONOS REGISTRADOS
     * Datatables server-side
     * *****************************/

    /**
     * GET - Primera carga de vista listado (tabla) 
     * */    
    public function listadoTipoAbonos(){
        if(Auth::check() == false) { return redirect()->route('abonos.prohibido'); }

        return view('tipoAbonos.listadoTipoAbonos');
    }

    
    /**
     * AJAX POST - ELIMINAR REGISTROS DE TABLA SERVER-SIDE
     * 
     * Elimina registro tipo_abonos si existe
     * Si no existe, manda mensaje
     * */
    public function deleteListadoTipoAbonos(Request $request){
        if(Auth::check() == false) return redirect()->route('abonos.prohibido');

        $tipoAbono = TipoAbono::find($request->idTipoAbono);
        
        if($tipoAbono === null){ 
            return response()->json(['errors'=>'El tipo de abono a eliminar no existe.']);
        }
        else {
            $tipoAbono->delete();
            return response()->json(['success'=>true]);
        }
    }


    /**
     * AJAX POST - CARGA DINÁMICA DE TABLA SERVER-SIDE
     * 
     * Devuelve registros de tipo_abonos tras petición AJAX, con 
     * ordenación y paginación incluída
     *   
     * - DataTables (server-side mode) envía parámetros al servidor
     * - Laravel hace paginación, ordenación, búsqueda en BD
     * */
    public function getListadoTipoAbonos(Request $request){

        if (!Auth::check()) return redirect()->route('abonos.prohibido');

        // número de registros totales
        $recordsTotal = TipoAbono::count();
        // query builder
        $query = TipoAbono::query();


        /*** 1) COMPROBAR BUSCABLES Y ORDENABLES ***/
        $colBuscables  = [];
        $colOrdenables = [];

        // Datatables manda info de cada columna: columns[0][data, name, searchable, ordenable, ...]  
        for ($i = 0; $i < count($request->columns); $i++) {

            // Parseo de string a bool, ya que se recibe: columns[0][searchable] = 'true'
            $searchable = json_decode($request->columns[$i]['searchable']);
            $orderable = json_decode($request->columns[$i]['orderable']);
            
            if ($searchable) { array_push($colBuscables, $request->columns[$i]['name']); }
            if ($orderable) { array_push($colOrdenables, $request->columns[$i]['name']); }
        }
        
        
        /*** 2) BÚSQUEDA - construcción de WHERE ***/
        // Datatables manda: 
        // search[value]="..."  (termino de busqueda)
        // columns[0]['searchable']=true    (si la columna es buscable)
        $busqueda = $request->search['value'];

        if (!empty($busqueda)) {
            // Si es usuario introduce %, _ ó \, se escapa (sql no lo tendrá en 
            // cuenta con el significado especial que tiene)
            $like = '%' . addcslashes($busqueda, '%_\\') . '%';

            $query->where(
                function ($innerJoinQuery)      // Condiciones de where
                use ($colBuscables, $like) {    // Variables externas a usar
                    foreach ($colBuscables as $col) {
                        $innerJoinQuery->orWhere($col, 'LIKE', $like);
                    }        
                }
            );
        }

        // número de registros filtrados
        $recordsFiltered = (clone $query)->count();


        /*** 3) ORDENACIÓN - construcción de ORDER BY ***/
        // Datatables manda:
        // order[0][column]=1
        //   order[0] -- primera regla de ordenacion
        //   [column]=1 -- index de columna usada como criterio de orden 
        // order[0][dir]=asc -- dirección de orden (asc/desc)
        
        // Obtiene indice de columna usada como regla de ordenación
        $indexCol = $request->order[0]['column'];
        
        // Obtiene nombre de la columna
        $nameCol = $request->columns[$indexCol]['name'];

        // Comprueba si es ordenable
        if (!in_array($nameCol, $colOrdenables)) $nameCol = 'descripcion';
        

        // Obtiene dirección de orden
        $direccion = $request->order[0]['dir'];

        $query->orderBy($nameCol, $direccion);


        /*** 4) PAGINACIÓN - Construcción de LIMIT ***/
        // Datatables manda:
        // start=0     desde que fila empieza (offset) 
        // length=10   cuantas filas traer (limit)

        // numero de filas a traer
        $length = (int) $request->length;
        // empezando desde el index de la fila...
        $start  = (int) $request->start;
        
        // Si no se indica sin paginación (length != -1)
        if ($length !== -1) $query->offset($start)->limit($length);


        /*** 5) EJECUTAR CONSULTA ***/

        $tipoAbonos = $query->get();

        
        /** 6) MANDAR RESPUESTA - datos obtenidos de consulta ***/

        $datos = [];

        foreach ($tipoAbonos as $ta) {
            $datos[] = [
                'id' => $ta->id,
                'descripcion' => $ta->descripcion,
                'precio' => $ta->precio,
                'codigo' => $ta->codigo,
                'icono' => base64_encode($ta->icono),
            ];
        }
        // $datos = [
        //      ['id'=>'...', 'descripcion'=>'...',  ...], 
        //      [ ...]
        // ];

        return response()->json([
            // Datatables manda: draw=1 -- Contador de peticiones AJAX
            'draw'            => $request->draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $datos,
        ]);
    }



    /*******************************
     * FORMULARIO CREAR TIPO ABONOS
     * *****************************/    

    /**
     * AJAX GET - Primera carga de vista formulario 
     * */
    public function formularioTipoAbonos(){
        if(Auth::check() == false) { return redirect()->route('abonos.prohibido'); }

        return view('tipoAbonos.formularioTipoAbonos');
    }


    /**
     * AJAX POST - VALIDACIONES EN TIEMPO REAL DE CAMPOS 
     * */
    public function validarDescripcion(Request $request){

        // Validar dato
        $validator = Validator::make($request->all(), [
            'descripcion'=> 'required|string|max:50|unique:tipo_abonos,descripcion'
        ]);
        
        // Respuesta
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            return response()->json(['success'=>true]);
        }        

    }

    public function validarPrecio(Request $request){
        // Validar dato
        $validator = Validator::make($request->all(), [
            'precio'=> 'required|integer|min:0'
        ]);
        
        // Respuesta
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            return response()->json(['success'=>true]);
        }    
    }

    public function validarCodigo(Request $request){
        // Validar dato 
        $validator = Validator::make($request->all(), [
            'codigo'=> 'required|regex:/^[A-Z]$/|unique:tipo_abonos,codigo'
        ]);
        
        // Respuesta
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            return response()->json(['success'=>true]);
        }  
    }

    public function validarIcono(Request $request){
        // Validar dato 
        $validator = Validator::make($request->all(), [
            'icono'=> 'required|image|mimes:jpg,png|max:2048'
        ]);

        // Respuesta
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            return response()->json(['success'=>true]);
        }  
    }



    /**
     * AJAX POST - VALIDACIÓN + INSERT TRAS SUBMIT DE FORMULARIO 
     * */
    public function insertTipoAbono(Request $request){

        if(Auth::check() == false) { return redirect()->route('abonos.prohibido'); }

        /* 1º Validar inputs de formulario */
        $validator = Validator::make($request->all(), [
            'descripcion'=> ['required', 'string', 'max:50', 'unique:tipo_abonos,descripcion'],            
            'precio'=> ['required', 'integer', 'min:0' ],
            'codigo'=> [ 'required', 'regex:/^[A-Z]$/', 'unique:tipo_abonos,codigo' ],
            'icono'=> ['required', 'image', 'mimes:jpg,png', 'max:2048' ]
        ], 
        ['codigo.regex'=> 'El código debe contener una letra mayuscula' ]
        
        );

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }
        else {
            /* 2º Preparar campos para insertar registro en BD */

            // Si mandó un icono, pasar a binario:
            if ($request->hasFile('icono')) {
                // file()   obtiene objeto UploadedFile (para obtener nombre, guardar, o leer contenido)
                // get()    devuelve contenido de archivo bruto (binario) 
                $iconoBinary = $request->file('icono')->get();
            } 
            else { $iconoBinary = null; }
            
            $datosTipo = [
                'descripcion'=> $request->descripcion, 
                'precio'=> $request->precio, 
                // 'codigo'=> substr($request->descripcion, 0, 1),
                'codigo'=> $request->codigo,
                'icono'=> $iconoBinary 
            ];

            $tipoAbono = TipoAbono::create($datosTipo);


            return response()->json(['success'=>'Tipo de abono registrado correctamente']);
        }
    }


}