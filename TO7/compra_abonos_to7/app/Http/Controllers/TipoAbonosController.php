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
        if(Auth::check() == false) { return redirect()->route('abonos.prohibido'); }

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


        /** 1) BÚSQUEDA POR DESCRIPCION - Construcción del WHERE **/
        // Datatables manda: 
        // search[value]="..." -- termino de busqueda
        // columns[0][data, name, searchable, ordenable, ...] -- info de cada columna
        // columns[0]['searchable']=true -- si es buscable una columna
        
        $where = '';

        // Si Datatables envió una busqueda (input de barra buscadora)
        if(!empty($request->search['value'])) {

            $stringAdded = false; // indica que inicialmente no se añadió una consulta WHERE hacia una columna
            $where.= 'WHERE ';

            
            // Revisa las columnas de DataTables que sean buscables
            for ($i = 0; $i < count($request->columns); $i++) {

                // json_decode() porque: 
                // - Datatables manda bool      columns[0][searchable] = true
                // - Laravel obtiene string     columns[0][searchable] = 'true'
                // - Hay que convertir strings a bool     json_decode("false") === false
                $searchable = json_decode($request->columns[$i]['searchable']);
                
                if ($searchable) {
                    // si se añadió consulta WHERE para una column
                    // primero añade OR para añadir siguiente consulta a otra column
                    if ($stringAdded) { $where.= ' OR '; }

                    // añade 'WHERE nombreCol LIKE '%'termino'%''
                    $where.= $request->columns[$i]['name'] .' LIKE \'%'. $request->search['value'] .'%\'';
                    
                    // Indica que ya hay una consulta a una columna añadida, antes de la siguiente
                    $stringAdded = true;

                    /* 
                    Para la siguiente iteración de columnas, hará:
                    'WHERE nombreCol LIKE '%'termino'%'' . 
                    ' OR ' . 
                    'WHERE nombreCol2 LIKE '%'termino2'%''
                    */
                }
                
            }
        }



        /** 2) ORDENACIÓN POR DESCRIPCIÓN - Construcción del ORDER BY **/
        // Datatables manda:
        // order[0][column]=1
        //   order[0] -- primera regla de ordenacion
        //   [column]=1 -- index de columna usada como criterio de orden 
        // order[0][dir]=asc -- dirección de orden (asc/desc)

        // Obtiene indice de columna usada como regla de ordenación
        $indexCol = $request->order[0]['column'];
        // Obtiene nombre de la columna
        $nameCol = $request->columns[$indexCol]['name'];
        
        // Obtiene dirección de orden
        $direccion = $request->order[0]['dir'];

        $orderBy = 'ORDER BY ' . $nameCol . ' ' . $direccion;



        /** 3) PAGINACIÓN - Construcción del LIMIT **/
        // Datatables manda:
        // start=0     desde que fila empieza (offset) 
        // length=10   cuantas filas traer (limit)

        $paginacion = '';
        
        // Si no se indica sin paginación (length != -1)
        if ($request->length != -1) {
            $paginacion .= 
            // numero de filas a traer
            'LIMIT ' . $request->length . 
            // empezando desde el index de la fila...
            ' OFFSET ' . $request->start;
        }



        /** 4) EJECUTAR CONSULTA **/
        $tipoAbonos = DB::select('SELECT * FROM tipo_abonos '. $where .' '. $orderBy .' '. $paginacion);



        /** 5) IMPRESCINDIBLE PARA DATATABLES CON SERVER-SIDE **/

        // Nº de registros filtrados
        $recordsFiltered = count($tipoAbonos);
        // Nº de registros totales en la BD
        $recordsTotal = DB::select('SELECT COUNT(id) as recordsNum FROM tipo_abonos')[0]->recordsNum;
        

        
        /** 6) Mandar respuesta (datos trabajados) **/
        
        $datos = [];

        foreach($tipoAbonos as $tipo) {
            $fila = [];
            
            $fila['id']=$tipo->id;
            $fila['descripcion']=$tipo->descripcion;
            $fila['precio']=$tipo->precio;
            $fila['codigo']=$tipo->codigo;
            // pasa binario a serializado para mandar en el JSON
            $fila['icono']=base64_encode($tipo->icono);

            $datos[]=$fila;
            // $datos = [
            //      ['id'=>'...', 'descripcion'=>'...',  ...], 
            //      [ ...]
            // ];
        }
        
        return response()->json([
            // Datatables manda:
            // draw=1 -- Contador de peticiones AJAX
            'draw' => $request->draw, 

            'recordsTotal' => $recordsTotal, 
            'recordsFiltered' => $recordsFiltered, 
            
            'data' => $datos
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