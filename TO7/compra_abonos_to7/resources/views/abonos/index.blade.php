@extends("layouts.default")


<!-- <head> -->
@section('title', 'UD Almería B - Abonos')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .hero::before{
            background: url('{{ asset('images/hero-1.jpg') }}');
        }
    </style>
@endsection

@section('scripts', '')



<!-- <body> -->

        @section('header')
        <!-- solo en esta vista tiene css distinto -->
        <nav id="mainNav" class="position-fixed top-0 start-0 w-100">
            <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
                <a href="{{ route('abonos.index') }}">
                    <img src="{{ asset('images/logo.png') }}" class="logo img-fluid" alt="logo">
                </a> 
                <a href="{{ route('usuarios.login') }}" class="btn btn-outline-light">Login administrador</a>
            </div>
        </nav>   
        @endsection


        @section('contenido')
        <!-- HERO -->
        <section class="hero text-center d-flex flex-column min-vh-100">
            <div>
                <h1 class="display-4 fw-bold">Compra tu Abono Online</h1>
                <p class="lead my-4">
                    Reserva tu asiento en segundos. Seguro, rápido y sin registro.
                </p>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 my-lg-5 my-md-0 mx-4">
                <a href="{{ route('abonos.compra') }}" class="btn btn-primary btn-lg px-4 me-md-2">Comprar ahora</a>
                <a href="#moreInfo" class="btn btn-outline-light btn-lg px-4 me-md-2">Más información</a>
            </div>


            <div class="ventajas rounded-4 mt-auto mb-4 mx-3 p-3 
            mx-md-5 p-md-5">
                <div class="row text-center g-3">
                    <div class="col-12 
                    col-md-4">
                        <i class="bi bi-house"></i>
                        Todos los partidos de Liga y Copa en casa
                    </div>

                    <div class="col-12 
                    col-md-4">
                        <i class="bi bi-piggy-bank"></i>
                        Ahorra un 30% respecto a entradas individuales
                    </div>

                    <div class="col-12 
                    col-md-4">
                        <i class="bi bi-calendar4-event"></i>
                        Preventa para competiciones europeas y eventos
                    </div>
                </div>
            </div>
        </section>

        
        <main class="container py-5">
            <div id="moreInfo"></div>
            <!-- TIPOS DE ABONO -->
             
            <section class="container-lg text-center d-flex justify-content-center flex-column py-5 gap-5">
                <h1 class="lh-base mb-2 fw-bold">Nuestros tipos de abonos</h1>

                <div class="cardsTiposAbonos row g-4   d-flex justify-content-center ">
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 rounded-4 p-4">
                            <i class="bi bi-award-fill tribuna mb-4"></i>

                            <h5 class="card-title fs-1 fw-bold">500€</h5>
                            <h5 class="card-title fs-3 fw-bold">Tribuna</h5>
                            <div class="d-flex flex-column flex-grow-1">
                                <p class="card-text mb-4">
                                    Disfruta de la mejor experiencia en el estadio desde la tribuna principal. 
                                    Asientos cómodos, con excelente visión del campo y acceso privilegiado a servicios exclusivos. 
                                    Ideal para quienes quieren vivir cada partido con la máxima emoción y comodidad.
                                </p>
                                
                                <a href="{{ route('abonos.compra') }}" class="btn btn-primary btn-lg mt-auto">Comprar</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 rounded-4 p-4 card-abonos">
                            <i class="bi bi-award-fill preferencia mb-4"></i>

                            <h5 class="card-title fs-1 fw-bold">300€</h5>
                            <h5 class="card-title fs-3 fw-bold">Preferencia</h5>

                            <div class="d-flex flex-column flex-grow-1">
                                <p class="card-text mb-4">
                                    Ubicación estratégica con buena visibilidad del juego y fácil acceso a las zonas de restauración. 
                                    Perfecto para quienes buscan disfrutar del fútbol con comodidad sin estar en la tribuna principal.
                                </p>
                                
                                <a href="{{ route('abonos.compra') }}" class="btn btn-primary btn-lg mt-auto">Comprar</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 rounded-4 p-4">
                            <i class="bi bi-award-fill fondo mb-4"></i>

                            <h5 class="card-title fs-1 fw-bold">110€</h5>
                            <h5 class="card-title fs-3 fw-bold">Fondo</h5>

                            <div class="d-flex flex-column flex-grow-1">
                                <p class="card-text mb-4">
                                    Vive la pasión del fútbol junto a la hinchada más animada.
                                    Asientos económicos en la zona de fondo, donde la energía de la afición crea un ambiente inolvidable. 
                                    Ideal para los seguidores más fervientes.
                                </p>
                                
                                <a href="{{ route('abonos.compra') }}" class="mt-auto btn btn-primary btn-lg">Comprar</a>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            
            <div id="discounts"></div>
            <section class="container-lg text-center d-flex justify-content-center flex-column py-5 gap-5">
                <h1 class="lh-base mb-2 fw-bold">Precios especiales</h1>
                
                <div class="row justify-content-center g-4 cardDescuentos">
                    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title">Jubilados</h5>
                                <p class="card-text">
                                    Descuento especial para mayores de 65 años. 
                                    Disfruta del fútbol con un precio reducido.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title">Menores de 12 años</h5>
                                <p class="card-text">
                                    Tarifa especial para los más pequeños de la casa.
                                    Vive el fútbol en familia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="{{ route('abonos.compra') }}" class="btn btn-secondary btn-lg px-4">Benefíciate</a>
                </div>
            </section>
        </main>
        @endsection


        @section('footer')
        <x-layouts.footer/>
        @endsection




    <!-- ***MOD ver como implementar fuera de la vista -->
    <!-- para animar fondo del nav al bajar del hero -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const nav = document.getElementById("mainNav");
            const hero = document.querySelector(".hero");

            window.addEventListener("scroll", () => {
                const heroBottom = hero.offsetTop + hero.offsetHeight;

                if (window.scrollY >= heroBottom - 80) {
                    nav.classList.add("nav-scrolled");
                } else {
                    nav.classList.remove("nav-scrolled");
                }
            });

        });


    </script>