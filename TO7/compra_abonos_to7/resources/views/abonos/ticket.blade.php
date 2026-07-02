@extends("layouts.default")


<!-- <head> -->
@section('title', 'Ticket de compra')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
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
        <!-- <main class="container d-flex justify-content-center text-center flex-column pb-5"> -->
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-2 fw-bold">
                <i class="bi bi-check-circle me-3" style="color: #27bb65;"></i>
                Compra realizada con éxito
            </h1>

            <h5 class="lh-base mb-5 fw-light">Tu abono ha sido registrado. Gracias por tu compra</h5>

            <!-- INFO -->
            <!-- <div class="row justify-content-center text-start py-5"> -->
            <div class="row justify-content-center text-start mx-1 mx-lg-0">

                <!-- <div class="wrapper-ticket 
                 col-12 col-md-10 col-lg-8 col-xl-6 
                 p-5"> -->
                <div class="wrapper-ticket  col-12 col-lg-9 col-xl-6 p-4 p-md-5">
                    
                    <div class="ticket-header d-flex">
                        <img src="{{ asset('images/logo.png') }}" class="logo img-fluid" alt="logo">
                        <div>
                            <h6 style="color:var(--accent-red)">UD ALMERÍA</h6>
                            <h4>Abono {{ $tipoAbono }}</h4>
                            <p>Temporada 2025/2026</p>
                        </div>
                    </div>

                    <hr/>

                    <div class="ticket-body container-fluid pb-3">
                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-person"></i>
                                Nombre
                            </div>
                            <div class="detalle col-12 col-md-8">
                                {{ $nombre }}
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-passport"></i>
                                DNI
                            </div>
                            <div class="detalle col-12 col-md-8">
                                {{ $dni }}
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-telephone"></i>    
                                Teléfono
                            </div>
                            <div class="detalle col-12 col-md-8">
                                {{ $telefono }}
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-calendar-event"></i>
                                Fecha de compra
                            </div>
                            <div class="detalle col-12 col-md-8">
                                {{ $fecha }}
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-award"></i>
                                Tipo de abono
                            </div>
                            <div class="col-12 col-md-8">
                                {{ $tipoAbono }}
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="detalle-header col-12 col-md-4 fw-bold">
                                <i class="bi bi-ticket-perforated"></i>
                                Asiento
                            </div>
                            <div class="col-12 col-md-8">
                                {{ $asiento }}
                            </div>
                        </div>
                        
                        <hr/>

                        <div class="row py-2">
                            <div class="importe-header col-12 col-md-4">
                                <p class="fs-4 fw-bold mb-0">Importe</p>
                            </div>
                            <div class="importe col-12 col-md-8">
                                <p class="fs-4 fw-bold mb-1">{{ $precio }}€</p>

                                @if ($edad < 12)
                                    <p class="tarifa-especial">* Tarifa especial Niños/as menores de 12 años: Rebaja de 80€.</p>
                                @elseif ($edad > 65)
                                    <p class="tarifa-especial">* Tarifa especial Jubilados y mayores de 65 años: Rebaja del 50%.</p>
                                @else 
                                    <p class="tarifa-especial">* Sin tarifa especial</p>
                                @endif
                            </div>
                        </div>

                    </div>

                    
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('abonos.compra') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-return-left"></i>                    
                            Volver
                        </a>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('abonos.downloadTicket', $id) }}" class="btn btn-primary mt-4">Descargar ticket</a>
                    </div>

                </div>

            </div>    
                        
        </main>
        @endsection



        @section('footer')
        <x-layouts.footer/>
        @endsection
