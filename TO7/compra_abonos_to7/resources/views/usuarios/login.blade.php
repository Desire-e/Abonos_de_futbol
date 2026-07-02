@extends("layouts.default")

<!-- <head> -->
@section('title', 'Inicio de sesión')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}">
@endsection

@section('scripts', '')


<!-- <body> -->


        @section('header', '')
        

        @section('contenido')
        <!-- <main class="container d-flex justify-content-center text-center flex-column pb-5"> -->
        <main class="container d-flex justify-content-center text-center flex-column my-5">
            
            <a href="{{ route('abonos.index') }}" class="btn-volver btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-return-left"></i>
                Volver
            </a>

            <!-- <h1 class="fs-1 fw-bold lh-lg mb-1">Acceso administrador</h1>
            <h3 class="lh-lg mb-5">Inicio de sesión</h3> -->
            <h1 class="lh-base mb-2 fw-bold">Acceso administrador</h1>
            <h3 class="lh-base mb-5">Inicio de sesión</h3>

            <!-- FORMULARIO -->

            <!-- <div class="row justify-content-center text-center">-->
            <div class="row justify-content-center text-center mx-1 mx-lg-0">
                <!-- <div class="wrapper-admin 
                 col-12 
                 col-md-8 
                 col-lg-6 
                 col-xl-4 
                 p-5">  -->
                <div class="wrapper-admin  col-11 col-md-7 col-lg-5  p-4 p-md-5">

                    <!-- <form action="{//{// route(//'usuarios.authenticate') }}" method="post" class="row g-3 gap-2 text-start"> -->
                    <form action="{{ route('usuarios.authenticate') }}" method="post" class="row g-3 text-start">
                        @csrf
                        <div class="col-12">
                            <label for="username" class="form-label">Nombre de usuario</label>
                            <input value = "{{ old('username') }}" type="text" name="username" class="form-control admin" id="username">

                            @error('username')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">Contraseña</label>
                            <input  value = "{{ old('password') }}" name="password" type="password" class="form-control admin" id="password">
                            @error('password')
                            <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 d-flex justify-content-center">
                            <button type="submit" name="login" value="ok" class="btn btn-secondary">Acceder</button>
                        </div>
                    </form>

                    <hr />

                    <div class="mt-4">
                        <a href="{{ route('google.login') }}" class="btn btn-outline-light btn-sm">Iniciar sesión con Google</a>
                        @error('google')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('usuarios.forgot') }}">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>
            </div>    
                        
        </main>
        @endsection





        @section('footer')
        <x-layouts.footer/>
        @endsection
