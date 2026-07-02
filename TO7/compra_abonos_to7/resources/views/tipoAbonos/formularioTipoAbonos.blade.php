
<!-- 
Hereda del layout. Poner nombre de la ruta dentro de /views + nombre del archivo:
../views/layouts/default.blade.php 
-->
@extends("layouts.default")


<!-- <head> -->
@section('title', 'Nuevo tipo de abono')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones-admin.css') }}">
@endsection


@section('scripts')

    <!-- PETICIONES AJAX (usando JS con librería JQuery) -->

    <script>
        $(document).ready(function () {
            
            /*******************************
             * VALIDAR CAMPOS EN TIEMPO REAL
             * *****************************/

            /* Timers */

            let timerDescripcion;
            let timerPrecio;
            let timerCodigo;
            let timerIcono;
            
            /* Evento input: cuando el valor de un elemento cambia (inputs, selects...) */

            $('#descripcion').on('input', function() {
                // espera 500 ms sin escribir para crear nueva petición
                // evita saturar servidor de peticiones
                clearTimeout(timerDescripcion);
                timerDescripcion = setTimeout(function() {
                    validarDescripcion();
                }, 500);
            });
            
            $('#precio').on('input', function(){
                clearTimeout(timerPrecio);
                timerPrecio = setTimeout(function() { validarPrecio(); }, 500);
            });

            $('#codigo').on('input',  function(){
                clearTimeout(timerCodigo);
                timerCodigo = setTimeout(function() { validarCodigo(); }, 500);
            });

            $('#icono').on('input',  function(){ 
                clearTimeout(timerIcono);
                timerIcono = setTimeout(function() { validarIcono(); }, 500);
            });

            /* Callbacks de eventos */

            function validarDescripcion(){
                $.ajax({
                    // -------------------------------------------------------------------------------------
                    // 1º Datos de petición
                    url: "{{ route('tipoAbonos.validarDescripcion') }}",
                    type: 'POST',
                    data: {
                        /*****
                        Sobre CSRF -- Obligatorio incluir para peticiones POST
                        Incluir como -- data: { _token: "{{ csrf_token() }}", ... }

                        Obligatorio:
                        - Peticiones POST
                        - Formularios: <form method="POST"> @csrf ...
                        - AJAX manual, sin FormData
                        
                        No obligatorio:
                        - AJAX con FormData: new FormData($('#form')[0])
                        lo incluye automáticamente porque está en el HTML
                        - Peticiones GET
                        *****/
                        "_token": "{{ csrf_token() }}",
                        
                        // value de un campo de un formulario con id='descripcion'
                        descripcion: $('#descripcion').val().trim(),
                    },
                    // -------------------------------------------------------------------------------------
                    // 2º Función success (si Laravel responde OK 200)
                    success: function(response){
                        console.log(response);
                        
                        // Si la respuesta JSON contiene atributo errors - validación de campo
                        if (response.errors != null) {
                            // .text(...) contenido de texto del elemento, se le inserta mensaje de error
                            $('#error-descripcion').text(response.errors[0]);
                            // muestra mensaje (display none por defecto)
                            $('#error-descripcion').show();
                        }
                        else {
                            $('#error-descripcion').text("");
                            $('#error-descripcion').hide();
                        }
                    },
                    // -------------------------------------------------------------------------------------
                    // 3º Función error (si Laravel responde ERROR 500, ...)
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
                    }
                });
            };


            function validarPrecio() {
                $.ajax({
                    url: "{{ route('tipoAbonos.validarPrecio') }}",
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        precio: $('#precio').val(),
                    },


                    success: function(response){
                        if (response.errors != null) {
                            $('#error-precio').text(response.errors[0]);
                            $('#error-precio').show();
                        }
                        else{
                            $('#error-precio').text("");
                            $('#error-precio').hide();
                        }
                    },


                    error: function (response) {
                        console.log("ERROR: ", response);

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
                    }
                });
            };

            
            function validarCodigo() {
                $.ajax({
                    url: "{{ route('tipoAbonos.validarCodigo') }}",
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        codigo: $('#codigo').val().trim(),
                    },


                    success: function(response){
                        if (response.errors != null) {
                            $('#error-codigo').text(response.errors[0]);
                            $('#error-codigo').show();
                        }
                        else{
                            $('#error-codigo').text("");
                            $('#error-codigo').hide();
                        }
                    },


                    error: function (response) {
                        console.log("ERROR: ", response);

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
                    }
                });
            };

            /********************************
             * ENVÍO DE UN INPUT FILE EN AJAX
             * ******************************/
            function validarIcono(){

                /*****
                FormData( [elementoForm] )
                - Contiene todos los datos de un formulario
                - Permite enviar por ajax cualquier input
                - Los input type=file en formato multipart/form-data
                
                Por qué necesario:
                - En jQuery, al usar data: { ... } -- manda datos serializados 
                  (intenta convertir datos a una cadena de texto)
                - Para mandar input file usar -- data: formData 
                *****/


                // 1º) Crea un objeto especial para contener y enviar datos de formulario.
                // formData = { };
                let formData = new FormData();

                // 2º) Obtiene del elemento HTML, su 1er archivo
                // $('#icono') = { 0: <input type="file" id="icono">, length: 1 }
                // $('#icono')[0] = <input type="file" id="icono">
                let archivo = $('#icono')[0].files[0];

                // 3º) Añade al objeto contenedor de datos de formulario los campos 
                // y sus valores con append(nombre, valor, [fileName.png])
                
                // Añade el campo file con el valor
                formData.append('icono', archivo);
                // Añade un campo llamado "_token" con su valor 
                // (input hidden generado con @csrf en el <form>).
                formData.append('_token', '{{ csrf_token() }}');
                // FormData = {
                //    _token: "{{ csrf_token() }}",
                //    icono: [archivo binario]
                // }


                // Petición
                $.ajax({
                    url: "{{ route('tipoAbonos.validarIcono') }}",
                    type: 'POST',
                    data: formData,

                    // Evita que jQuery convierta los datos (FormData) a string.
                    processData: false,
                    // Permite que el navegador genere automáticamente multipart/form-data.
                    contentType: false,


                    success: function(response){
                        if (response.errors != null) {
                            $('#error-icono').text(response.errors[0]);
                            $('#error-icono').show();
                        }
                        else{
                            $('#error-icono').text("");
                            $('#error-icono').hide();
                        }
                    },


                    error: function (response) {
                        console.log("ERROR: ", response);

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
                    }
                });
            }; 



            /*******************************************************
             * VALIDAR FORMULARIO TRAS SUBMIT (INCLUYENDO INPUT FILE)
             * *****************************************************/
            
            $('#tipoAbonoForm').on('submit', function (e) { insertTipoAbono(e); });

            function insertTipoAbono(e) {
                // Previene comportamiento por defecto del evento
                e.preventDefault();
                
                // Introduce datos del formulario (campos + csrf)
                let formData = new FormData($('#tipoAbonoForm')[0]);

                // Petición AJAX
                $.ajax({
                    url: "{{ route('tipoAbonos.insertTipoAbono') }}",
                    type: 'POST',
                    data: formData,

                    processData: false,
                    contentType: false,


                    success: function(response){
                        if (response.errors != null) {                            
                            // Aviso emergente de errores
                            
                            $('#errorModal .modal-title').text(
                                'Ocurrió un error'
                            );
                            
                            $('#errorModal .modal-body').text(
                                'Algunos campos no son válidos. Corrija e intente de nuevo.'
                            );
                            
                            // Mostrar modal manualmente
                            let modalError = new bootstrap.Modal(
                                document.getElementById('errorModal')
                            );
                            
                            modalError.show();


                            // Mensajes de error por campo
                            if (response.errors.descripcion != null) {
                                $('#error-descripcion').text(response.errors.descripcion[0]);
                                $('#error-descripcion').show();
                            }
                            if (response.errors.precio != null) {
                                $('#error-precio').text(response.errors.precio[0]);
                                $('#error-precio').show();
                            }
                            if (response.errors.codigo != null) {
                                $('#error-codigo').text(response.errors.codigo[0]);
                                $('#error-codigo').show();
                            }
                            if (response.errors.icono != null) {
                                $('#error-icono').text(response.errors.icono[0]);
                                $('#error-icono').show();
                            }
                        }
                        else {
                            $('#successModal .modal-title').text(
                                'Operación realizada'
                            );
                            
                            $('#successModal .modal-body').text(
                                'Tipo de abono creado correctamente'
                            );
                            
                            // Mostrar modal manualmente
                            let modalSuccess = new bootstrap.Modal(
                                document.getElementById('successModal')
                            );
                            
                            modalSuccess.show();

                            // window.alert(response.success);
                        }
                    },


                    error: function (response) {
                        console.log("ERROR: ", response);

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
                        
                    }
                });
            }; 
        });



    </script>
@endsection


<!-- <body> -->

        @section('header', '')
        

        @section('contenido')
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-5 fw-bold">Panel de Administración</h1>

            <div class="row justify-content-center text-center mx-1 mx-lg-0">
                <div class="wrapper-admin   col-12 col-md-12 col-lg-5 p-4 p-md-5">
            <!-- <div class="d-flex flex-column align-items-center text-center py-5"> -->
                <!-- <div class="d-flex flex-column  gap-3 
                wrapper-admin col-12 col-md-12 col-lg-8 col-xl-6 p-5"> -->
        
                    <h3 class="lh-base">Nuevo tipo de abono</h3>
            
                    <hr/>
                    
                    <!-- 
                    FORMULARIO PARA PETICIÓN AJAX.

                    No necesario action="POST" method="POST"
                    - Con él, navegador enviar petición al servidor directamente, recarga página  
                    - Sin él, JS hace la petición al servidor con AJAX madiante evento 
                        on.("submit" ...), sin recargar página

                    No necesario enctype="multipart/form-data"
                    - Para enviar archivos, new FormData() ya lo "incluye"

                    Necesario id="" 
                    - Para seleccionar formulario
                    -->

                    <form class="row g-3 text-start" id="tipoAbonoForm" enctype="multipart/form-data"> 
                        @csrf

                        <!-- 
                        INPUTS PARA PETICIÓN AJAX.

                        Necesario value={{ old('...') }} -- persistencia
                        
                        Mensajes de error:
                        - Usar elemento por defecto display:none + vacío
                        - Al recibir errores hacer en el elemento -- .show() + .val("mensaje error")
                        - Si no recibes errores, limpieza -- .hide() + .val("")
                        -->


                        <!-- DESCRIPCION -->
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input name="descripcion" type="text" class="form-control admin" id="descripcion"
                            value="">

                            <p style="display:none" class="error" id="error-descripcion"></p>
                        </div>


                        <!-- PRECIO -->
                        <div class="col-12">
                            <label for="precio" class="form-label">Precio</label>
                            <input name="precio" type="number" class="form-control admin" id="precio"
                            value="">

                            <p style="display:none" class="error" id="error-precio"></p>
                        </div>


                        <!-- CODIGO (1 string) -->
                        <div class="col-12">
                            <label for="codigo" class="form-label">Código</label>
                            <input name="codigo" type="text" class="form-control admin" id="codigo"
                            maxlength="1" minlength="1"
                            value="">

                            <p style="display:none" class="error" id="error-codigo"></p>
                        </div>


                        <!-- ICONO -->
                        <div class="col-12">
                            <label for="icono" class="form-label">Icono</label>
                            <!-- <input name="icono" type="file" accept="image/png" id="icono"> -->
                            <input name="icono" type="file" accept="image/png" id="icono" class="form-control admin"/>
                            <p style="display:none" class="error" id="error-icono"></p>
                        </div>


                        <!-- SUBMIT -->
                        <div class="col-12 text-center">
                            <button type="submit" name="btn-registrar" value="ok" class="btn btn-secondary mt-4">
                                Registrar tipo de abono
                            </button>
                        </div>
                    </form>
                    
                </div>


            </div>

            <x-layouts.acciones-admin
                :mostrarAbonos="true"
                :mostrarTipoAbonos="true"
            />
            
            <!-- MODAL -->
            <x-layouts.modal-confirm />
            <x-layouts.modal-error />
            <x-layouts.modal-success />
                    
        </main>
        @endsection


        @section('footer')
        <x-layouts.footer/>
        @endsection
