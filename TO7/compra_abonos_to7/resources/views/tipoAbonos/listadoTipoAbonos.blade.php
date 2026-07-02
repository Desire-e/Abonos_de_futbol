@extends("layouts.default")


<!-- <head> -->
@section('title', 'Listado de abonos')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/listado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endsection

@section('scripts')

<script>
$(document).ready(
    
function () {
    /****************************
     * DATATABLE MODO SERVER-SIDE
     * serverSide: true
     * **************************/

    /*
    DataTables:
    - manda parámetros de control del estado de la tabla  
      (paginación, búsqueda, ordenación, etc.)
    - deja de trabajar con todos los datos en el navegador 
    - y pasa a pedirle a Laravel

    El servidor (Laravel):
    - reconstruye la consulta a BD para devolver los datos 
      ya trabajados para Datatable

    ¿Cuando se envía peticiones?
    Cuando haces scroll, buscas o cambias página.
    DataTables hace un AJAX automático.

    -----------------------------------------
    Qué params envía DataTables:

    1. Contador de peticiones AJAX
    draw=1      ??

    2. Paginacion:
    start=0     desde que fila empieza (offset) 
    length=10   cuantas filas traer (limit)

    3. Busqueda global
    search[value]=juan   texto escrito en buscador   
    search[regex]=false

    4. Ordenacion    
    order[0][column]=1  
        order[0] - primera regla de ordenacion
        [column]=1 - columna que se esta usando (por index) como criterio de orden 
        (ordenar por nombre, correo ...)
    order[0][dir]=asc   orden ascendente

    5. Columnas
    columns[0][data]=id     columna 0 - data, name, searchable, orderable      
    columns[0][name]=id
    columns[0][searchable]=false
    columns[0][orderable]=false
    columns[1]...

    -----------------------------------------
    Cómo lo usa Laravel:
    
    1. Búsqueda (WHERE)
    $request->search['value']

    2. Ordenación (ORDER BY)
    $request->order[0]['column']
    $request->columns[...]['name']

    3. Paginación (LIMIT / OFFSET)
    $request->length
    $request->start
    -----------------------------------------
    */
    
    let table = $('#tablaTipoAbonos').DataTable({
        
        /**
         * CONFIGURACIÓN DE TABLA
         * */

        /** Responsive **/
        responsive: true,

        /** Otras configuraciones tras inicialización (estética DT) **/
        initComplete: function () {
            // Buscador
            $('.dataTables_filter input').attr('placeholder', 'Buscar tipo de abono...');
        },
        
        /** Idioma y mensajes **/
        language: {
            // Idioma, mensajes general
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',

            // Buscador
            search: " ",

            // Selector de registros
            lengthMenu: "Mostrar  _MENU_  registros",
            
            // Info de paginación
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            
            // Contenido sin registros
            emptyTable: "No existen tipos de abono registrados",
            
            // En pantalla de carga
            processing: "Cargando..."
        },

        /** Paginacion **/
        "paging": true,
        "pagingType": "numbers",
        
        /** Selector de filas en cada paginacion **/
        "lengthChange": true,
        // [ [valores internos], [texto mostrado en selector] ]
        "lengthMenu": [[5, 10, 100], [5, 10, 100]], 

        /** Orden inicial **/
        // 1 - índice de la columna
        // "asc" - orden ascendente
        "order": [[ 1, "asc" ]],

        /** Indicador de carga **/
        // mostrado cuando está esperando respuesta AJAX, ordenando, filtrando
        "processing": true,

        /** Uso de modo server-side **/
        // para trabajar datos desde servidor
        "serverSide": true,


        /**
         * PETICIÓN AJAX
         * */

        // DataTables - manda parámetros de control del estado de la tabla  
        // (paginación, búsqueda, ordenación, etc.)
        // El servidor (Laravel) - reconstruye la consulta a BD para devolver 
        // los datos ya trabajados para Datatable
        "ajax": {
            "url": "{{ route('tipoAbonos.getListadoTipoAbonos') }}",
            "type": "POST",
            "data": {"_token": "{{ csrf_token() }}"}
        },



        /**
         * DEFINCIÓN DE CONTENIDO + CONFIGURACIÓN DE COLUMNAS 
         * */

        "columns": [

            // COL 0. 
            // Columna invisible, solo existe internamente para enviar peticiones AJAX
            {
                // Nombre del campo del JSON recibido desde servidor
                // su info que se mostrará en esa columna.
                data: 'id',

                // Nombre del campo del JSON enviado por DataTables al servidor 
                // para usarlo en ORDER BY, búsquedas, filtros server-side
                // Laravel recibe: $request->columns[x]['name']
                name: 'id',

                // No ordenable
                orderable: false,
                // No buscable
                searchable: false,
                // No visible
                visible: false
            },

            // COL 1-4. 
            // Ordenación y busqueda por descripción
            {
                data: 'descripcion',
                name: 'descripcion'
            },
            {
                data: 'precio',
                name: 'precio', 
                orderable: false,
                searchable: false,
            },
            {
                data: 'codigo',
                name: 'codigo', 
                orderable: false,
                searchable: false,
            },
            {
                data: 'icono',
                name: 'icono', 
                orderable: false,
                searchable: false,

                render: function(data, type, row, meta) {                        
                        return `<img src="data:image/png;base64,${data}" />`;
                },
            },

            // COL 5.
            // Botón de eliminar 
            {
                // No recibe datos
                data: null,
                name: 'eliminar',
                orderable: false,
                searchable: false,

                // Cómo se renderiza en cada fila
                // data - Valor de "data" de esa columna
                // type - Tipo de render (display, filter, sort)
                // row - Toda la fila completa
                // meta - Información adicional (índice fila, columna, ...)
                render: function(data, type, row, meta) {
                        // data-row="..." - atributo personalizado, contiene indice de fila
                        
                        // Button trigger modal
                        return `
                            <div class="d-flex justify-content-center">
                                <button 
                                type="button"
                                data-bs-toggle="modal" data-bs-target="#confirmModal" 
                                class="eliminar btn btn-danger btn-sm"                                
                                data-row="${meta.row}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        `;
                },
            }
        ],
    });



    /********************************
     * Callback de botones "eliminar"
     * ******************************/

    // ---- 1º Muestra modal de confirmacion ----
    
    let rowSeleccionada = null;

    $('#tablaTipoAbonos tbody')                                     // Selecciona el cuerpo de la tabla.
    .on( 'click', '.eliminar', function(e){ modalEliminar(e); }); // Escucha eventos de forma delegada.
    // Escucha de eventos "delegada":
    // - Porque DataTables crea y destruye filas dinámicamente con AJAX.
    // - $('.eliminar').click(...) solo funcionaría para botones iniciales.

    function modalEliminar(e){
        // 1- Obtener fila clicada        
        let btnEliminar = $(e.currentTarget); // elemento que escucha evento (botón)

        let indexRow = btnEliminar.attr('data-row'); // indice de la fila clicada (valor atributo data-row)
        // 2- Obtener datos completos de la fila clicada
        // P.ej:    row[0] = { id: 15, descripcion: "Tribuna", ... }
        rowSeleccionada = table.data()[indexRow];

        // 3- Cambiar contenido del modal
        $('#confirmModal .modal-title').text(
            'Eliminar tipo de abono'
        );

        $('#confirmModal .modal-body').text(
            `¿Desea eliminar el tipo de abono "${rowSeleccionada.descripcion}"? Una vez eliminado, no podrá revertirse.`
        );
    }
    
    // ---- 2º Confirmación desde el modal mostrado ----

    $('#btnConfirmar').on( 'click', function(){ eliminarTipoAbono(); }); 

    function eliminarTipoAbono(){
        if (!rowSeleccionada) return;

        $.ajax({
            url: "{{ route('tipoAbonos.deleteListadoTipoAbonos') }}",
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                idTipoAbono: rowSeleccionada.id
            },

            // Si respuesta OK (200):
            // Si no existe registro, se avisa y recarga tabla
            // Si existe registro, solo se recarga tabla
            success: function(response){

                if(response.errors){
                    $('#errorModal .modal-title').text(
                        'Ocurrió un error'
                    );
                    
                    $('#errorModal .modal-body').text(
                        `No se pudo procesar la solucitud. ${response.errors}`
                    );
                    
                    // Mostrar modal manualmente
                    let modalError = new bootstrap.Modal(
                        document.getElementById('errorModal')
                    );
                    
                    modalError.show();

                    /*window.alert(
                        'Ha ocurrido un problema en el servidor. '
                        + response.errors
                    );*/
                }

                table.ajax.reload();

                // Cerrar modal
                bootstrap.Modal.getInstance(
                    document.getElementById('confirmModal')
                ).hide();

                rowSeleccionada = null;
            },

            // Si error de servidor
            error: function (response) {
                console.error("ERROR: ", response);

                $('#errorModal .modal-title').text(
                    'Ocurrió un error'
                ); 
                    
                $('#errorModal .modal-body').text(
                    `Ha ocurrido un problema en el servidor. Inténtelo de nuevo más tarde.`
                );
                    
                // Mostrar modal manualmente
                let modalError = new bootstrap.Modal(
                    document.getElementById('errorModal')
                );
                    
                modalError.show();

                // window.alert("Ha ocurrido un problema en el servidor. Inténtelo de nuevo más tarde.");
            }
        });
    }

});

    
</script>

@endsection



        @section('header', '')


        @section('contenido')
        <!-- <main class="container d-flex justify-content-center text-center flex-column pb-5"> -->
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-2 fw-bold">Panel de Administración</h1>
            <h3 class="lh-base mb-5">Tipos de abonos</h3>

            <!-- <div class="row justify-content-center text-start py-5"> -->
            <div class="row justify-content-center text-center mx-1 mx-lg-0">

                <div class="table-responsive">
                    <table id="tablaTipoAbonos" class="table border nowrap" style="width:100%">

                        <thead>
                            <tr>
                                <!-- col 0 - columna invisible, para acceder al registro concreto de la BD -->
                                <th scope="col">Id</th>

                                <!-- columnas visibles -->
                                <th scope="col">Descripción</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Código</th>
                                <th scope="col">Icono</th>
                                <th scope="col">Operaciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            
                            <!-- EJEMPLO -->
                            <!-- <tr> -->
                                
                                <!-- DESCRIPCION -->
                                <!-- <td scope="row">Tribuna</td> -->
                                
                                <!-- PRECIO -->
                                <!-- <td scope="row">100 €</td> -->
                                
                                <!-- CODIGO -->
                                <!-- <td scope="row">T</td> -->
                                
                                <!-- ICONO -->
                                <!-- <td scope="row">
                                    <img src="silver.png"/>
                                </td>-->
                                
                                <!-- OPERACIONES -->
                                <!-- 
                                <td scope="row">
                                    <div class="d-flex justify-content-center">
                                        <a href="#" class="btn btn-primary mt-4">
                                            Eliminar
                                        </a>
                                    </div>
                                </td> 
                                -->

                            <!-- </tr> -->
                            <!-- FIN EJEMPLO -->

                        </tbody>
                    </table>
                </div>


                <x-layouts.acciones-admin
                    :mostrarCrear="true"
                    :mostrarAbonos="true"
                />

            </div>

            <!-- MODAL -->
            <x-layouts.modal-confirm />
            <x-layouts.modal-error />
            <x-layouts.modal-success />
        
        @endsection


        @section('footer', '')