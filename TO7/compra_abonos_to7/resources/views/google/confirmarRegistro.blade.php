@extends("layouts.default")

<!-- <head> -->
@section('title', 'Confirmación de registro')

@section('css')
    <link href="{{ asset('css/alerta.css') }}" rel="stylesheet">
@endsection

@section('scripts', '')




<!-- <body> -->
        @section('header', '')

        @section('contenido')
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-2 fw-bold">Nuevo registro</h1>

            <hr/>

            <div class="row justify-content-center pt-4">
                <div class="col-11 col-md-10 col-lg-8">
                    <div class="alerta-wrapper confirmaRegistro 
                    p-4 p-md-5 d-flex align-items-center flex-column text-center">

                        <i class="bi bi-person-fill alerta"></i>

                        <h3 class="lh-base">Usuario sin cuenta</h3>
                        
                        <p>Aún no tiene una cuenta registrada con este correo, ¿desea crearla?</p>

                        <form method="POST" action="{{ route('google.register') }}" class="d-flex gap-3">
                            @csrf
                            <button type="submit" name="accion" value="confirmar" class="btn btn-secondary">Confirmar</button>                       
                            <button type="submit" name="accion" value="cancelar" class="btn btn-outline-light">Cancelar</button>
                            
                            @error('accion')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </form>
                    
                    </div>
                </div>            
            </div>        

                    
        </main>
        @endsection

        @section('footer', '')



