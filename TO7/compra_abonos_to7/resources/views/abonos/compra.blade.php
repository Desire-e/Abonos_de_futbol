
<!-- 
Hereda del layout. Poner nombre de la ruta dentro de /views + nombre del archivo:
../views/layouts/default.blade.php 
-->
@extends("layouts.default")


<!-- <head> -->
@section('title', 'Compra de abono')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}">
@endsection

@section('scripts', '')



<!-- <body> -->


        @section('header')
        <nav id="mainNav" class="mainNav-solid top-0 start-0 w-100">
            <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
                <a href="{{ route('abonos.index') }}">
                    <img src="{{ asset('images/logo.png') }}" class="logo img-fluid" alt="logo">
                </a> 
                <a href="{{ route('usuarios.login') }}" class="btn btn-outline-light">Login administrador</a>
            </div>
        </nav>   
        @endsection
        

        @section('contenido')
        <!-- **CHANGED -->
        <main class="container d-flex justify-content-center text-center flex-column my-5">
            <!-- **CHANGED -->
            <h1 class="lh-base mb-2 fw-bold">Formulario de compra</h1>
            <!-- **CHANGED -->
            <h3 class="lh-base mb-5">Datos del abonado</h3>

            <!-- FORMULARIO -->
             
                <div class="row justify-content-center text-center mx-1 mx-lg-0">
                    <div class="wrapper-compra col-12 col-md-10 col-lg-7 p-4 p-md-5">


                        <form action="{{ route('abonos.insert') }}" method="post" class="row g-3 text-start">
                        <!-- 
                        route() busca esa ruta en web.php
                        le manda a esa ruta el POST, al controlador y al metodo de este que se indica en web.php 
                        (AbonosController::insert(Request $request))
                        -->
                            @csrf
                            <!-- Protección de CSRF attacks (comprueba identidad) -->


                            <!-- NOMBRE -->
                            <div class="col-md-7">
                                <label for="nombre" class="form-label">Nombre y apellidos</label>
                                <input name="nombre" type="text" class="form-control" id="nombre"
                                value="{{ old('nombre') ?? request()->cookie('nombre') ?? '' }}">

                                @error('nombre')
                                <p class="error">{{ $message }}</p>
                                @enderror
                            </div>                    
                            <!-- 
                            old('...') -- Persistencia de valores invalidos          
                            Si la validación falla en AbonosController::insert(), Laravel:
                            - redirige a esta misma pagina
                            - guarda los valores anteriores invalidos y old() los recupera 

                            Si no hay un old(), se dan campos vacios (1ª vez entrando)
                            -->
                            <!-- 
                            request()->cookie('nombre') -- recoge la cookie guardada cuando se hizo insert()
                            expresion ?? expresion2 -- si la expresion existe y no es null, usala, sino usa expresion2 
                            -->


                            <!-- DNI -->
                            <div class="col-md-5">
                                <label for="dni" class="form-label">DNI</label>
                                <input name="dni" type="text" class="form-control" id="dni"
                                value="{{ old('dni') ?? request()->cookie('dni') ?? '' }}">
                                
                                @error('dni')
                                <p class="error">{{ $message }}</p>
                                @enderror
                            </div>                            
                            
                            <!-- TELEFONO -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input  name="telefono" type="text" class="form-control" id="telefono"
                                value="{{ old('telefono') ?? request()->cookie('telefono') ?? '' }}">
            
                                @error('telefono')
                                <p class="error">{{ $message }}</p>
                                @enderror                            
                            </div>
                                                        
                            <!-- NACIMIENTO -->
                            <div class="col-md-6">
                                <label for="nacimiento" class="form-label">Fecha de nacimiento</label>
                                <input name="nacimiento" type="date" class="form-control" id="nacimiento" name="nacimiento"
                                value="{{ old('nacimiento') ?? request()->cookie('nacimiento') ?? '' }}">

                                @error('nacimiento')
                                <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- CUENTA BANCARIA -->
                            <div class="col-md-7">
                                <label for="cuentaBancaria" class="form-label">Cuenta bancaria</label>
                                <input name="cuentaBancaria" type="text" class="form-control" id="cuentaBancaria" 
                                value="{{ old('cuentaBancaria') ?? request()->cookie('cuentaBancaria') ?? '' }}">

                                @error('cuentaBancaria')
                                <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- TIPO DE ABONO -->
                            <x-SelectTipoAbono select-tipo="{{old('abonoTipo')}}" />

                            <!-- TERMINOS -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input name="terminosCheck" type="checkbox" class="form-check-input" id="terminosCheck">
                                    <!-- @checked(old('terminosCheck'))> -->
                                    <label class="form-check-label" for="terminosCheck">Acepto los términos</label>

                                    @error('terminosCheck')
                                    <p class="error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>          
                            
                            <!-- Si tras 5 intentos no se pudo generar un código de asiento... -->
                            @error('asiento')
                            <p class="error">{{ $message }}</p>
                            @enderror
                            
                            <!-- SUBMIT -->
                            <!-- <div class="col-12 text-center"> -->
                            <div class="col-12 d-flex justify-content-center mt-5">
                                <!-- **CHANGED -->
                                <button type="submit" name="botonComprar" value="ok" class="btn btn-primary btn-lg w-100">
                                    Comprar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>    
                    
        </main>
        @endsection


        @section('footer')
        <x-layouts.footer/>
        @endsection

