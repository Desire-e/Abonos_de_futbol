@extends("layouts.default")


<!-- <head> -->
@section('title', 'Listado de abonos')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/listado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/acciones-admin.css') }}">
@endsection

@section('scripts', '')



        @section('header', '')

        @section('contenido')
        <!-- <main class="container d-flex justify-content-center text-center flex-column pb-5"> -->
        <main class="container d-flex justify-content-center text-center flex-column my-5">

            <h1 class="lh-base mb-2 fw-bold">Panel de Administración</h1>
            <h3 class="lh-base mb-5">
                @if(empty($abonos))
                    No se encontraron abonos registrados 
                @endif

                Abonos vendidos
            </h3>

            <!-- CAMPOS DEL REGISTRO -->
            
            <!-- INFO -->

            <!-- <div class="row justify-content-center text-start py-5"> -->
            <div class="row justify-content-center text-start mx-1 mx-lg-0">

                <div class="table-responsive">
                    <table class="table border">

                        <thead>
                            <tr>
                                <th scope="col">Tipo de abono</th>
                                <th scope="col">Código de asiento</th>
                                <th scope="col">Datos del abonado</th>
                                <th scope="col">Datos del abonado especial</th>
                                <th scope="col">Importe</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($abonos as $abono)
                            <tr>
                                <!-- MEDALLA -->
                                <td scope="row">
                                    <!-- 
                                    MOSTRAR BLOB EN VISTA
                                    Vienen de la BD datos en MEDIUMBLOB (binario)
                                    Construye imagen con base64_encode() 
                                    -->

                                    <!-- <img src="data:image/png;base64, {{ base64_encode($abono->tipoAbono->icono) }}" width="200"> -->

                                    <!-- @if(!empty($abono->tipoAbono->icono))
                                    <img src="data:{{ $abono->tipoAbono->mime_type ?? 'image/png' }};base64,{{ base64_encode($abono->tipoAbono->icono) }}" alt="Icono">
                                    @else <p>Sin icono</p> @endif -->
                                    
                                    @if($abono->icono_base64)
                                    <img src="data:image/png;base64,{{ $abono->icono_base64 }}">
                                    @else 
                                    <p>Sin icono</p>
                                    @endif

                                    
                                    <!-- 
                                    @if ($abono->tipoAbono->descripcion === 'Tribuna')
                                    <i class="bi bi-award-fill tribuna"></i>
                                    @elseif($abono->tipoAbono->descripcion === 'Preferencia')
                                    <i class="bi bi-award-fill preferencia"></i>
                                    @elseif($abono->tipoAbono->descripcion === 'Fondo')
                                    <i class="bi bi-award-fill fondo"></i>
                                    @endif                             
                                    -->
                                </td>
                                
                                <!-- ASIENTO -->
                                <td>{{ $abono->asiento }}</td>

                                <!-- INFO DEL ABONADO -->
                                <td>
                                    <div class="d-flex gap-3">
                                        <!-- <p>{{ $abono->abonado }}</p> -->
                                        {{ $abono->abonado }}
                                        <i class="bi bi-telephone-fill" title="{{ $abono->telefono }}"></i>
                                        <i class="bi bi-bank2" title="{{ $abono->cuenta_bancaria }}"></i>
                                    </div>
                                </td>

                                <!-- EDAD DESCUENTO -->
                                <td>{{ $abono->descuento }}</td>
                                
                                <!-- PRECIO TOTAL -->
                                <td>{{ $abono->precio }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <x-layouts.acciones-admin
                    :mostrarCrear="true"
                    :mostrarTipoAbonos="true"
                />
            </div>    
                        
        </main>
        @endsection

        @section('footer', '')