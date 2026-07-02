@extends("layouts.default")

<!-- <head> -->
@section('title', 'Olvidaste tu contraseña')

@section('css')
    <link href="{{ asset('css/alerta.css') }}" rel="stylesheet">
@endsection

@section('scripts', '')


<!-- <body> -->

        @section('header', '')        

        @section('contenido')
        <main class="container d-flex justify-content-center text-center flex-column my-5">
            <a href="{{ url()->previous() }}" class="btn-volver btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-return-left"></i>        
                Volver
            </a>


            <!-- FORMULARIO -->
             
            <div class="row justify-content-center text-center mx-1 mx-lg-0">
                <h1 class="lh-base mb-2 fw-bold">Página en proceso</h1>
                <h3 class="lh-base mb-5">Pues haz memoria...</h3>
            
                <!-- <div class="wrapper-compra col-12 col-md-10 col-lg-7 p-4 p-md-5">
                </div> -->
            </div>
        </main>
        @endsection

        @section('footer', '')
