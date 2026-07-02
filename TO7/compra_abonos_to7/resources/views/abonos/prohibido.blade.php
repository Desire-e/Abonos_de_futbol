@extends("layouts.default")

<!-- <head> -->
@section('title', 'Contenido protegido')

@section('css')
    <link href="{{ asset('css/alerta.css') }}" rel="stylesheet">
@endsection

@section('scripts', '')



<!-- <body> -->
        @section('header', '')

        @section('contenido')
         
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-2 fw-bold">Alerta de acceso</h1>

            <hr/>

            <div class="row justify-content-center pt-4">
                <div class="col-11 col-md-10 col-lg-8">
                <!-- 
                col-* - min 0 (xs)
                col-sm-* - min 576px (moviles grandes en adelante)
                col-md-* - 768px (tablets en adelante)
                col-lg-* - 992px (pc en adelante...)
                col-xl-* - 1200px
                col-xxl-* - 1400px
                -->
                    <div class="alerta-wrapper p-4 p-md-5 d-flex align-items-center flex-column text-center">
                        <i class="bi bi-exclamation-triangle-fill alerta"></i>
                        
                        <h3 class="lh-base">Contenido protegido</h3>

                        <p>
                            El contenido al que intenta acceder solo está permitido para cuentas autorizadas.
                            Inicie sesión como administrador.
                        </p>
                        
                        <a href="{{ route('abonos.index') }}" class="btn btn-primary mb-3">Volver a página de inicio</a>
                        <a href="{{ route('usuarios.login') }}" class="btn btn-outline-light">Iniciar sesión como administrador</a>
                    </div>
                </div>
            </div>
                                    
        </main>
        @endsection

        @section('footer', '')
