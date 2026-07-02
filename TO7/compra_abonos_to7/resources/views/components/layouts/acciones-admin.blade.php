
<!-- 
Valores por defecto del componente anónimo 
-->
@props([
    'mostrarCrear' => false,
    'mostrarTipoAbonos' => false,
    'mostrarAbonos' => false,
])


                <div class="container justify-content-center text-center g-3 mt-5 p-5 acciones-admin">
                    <div class="row">
                        <h3>Acciones de administración</h3>
                        <hr/>
                    </div>
                    
                    <!-- <div class="row justify-content-between"> -->
                        <div class="row g-3 justify-content-between">
                        @if($mostrarCrear)
                        <!-- <div class="col-auto"> -->
                        <!-- <div class="col-12 col-sm-6 col-lg-4"> -->
                        <div class="col-md col-12">

                            <!-- <div class="card" style="width: 13rem;"> -->
                            <div class="card h-100">

                                <a href="{{ route('tipoAbonos.formularioTipoAbonos') }}">
                                    <div class="card-body">
                                        <div class="icon">
                                            <i class="bi bi-plus-square-fill"></i>
                                        </div>
                                        <h6 class="card-title">Crear tipos de abonos</h6>                                        
                                    </div>
                                </a>
                            </div>

                        </div>
                        @endif

                        @if($mostrarTipoAbonos)
                        <!-- <div class="col-auto"> -->
                        <!-- <div class="col-12 col-sm-6 col-lg-4"> -->
                        <div class="col-md col-12">
                            
                            <!-- <div class="card" style="width: 13rem;"> -->
                            <div class="card h-100">
                                <a href="{{ route('tipoAbonos.listadoTipoAbonos') }}">
                                    <div class="card-body">
                                        <div class="icon">
                                            <i class="bi bi-list-ul"></i>
                                        </div>
                                        <h6 class="card-title">Ver tipos de abonos</h6>                                        
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($mostrarAbonos)
                        <!-- <div class="col-auto"> -->
                        <!-- <div class="col-12 col-sm-6 col-lg-4"> -->
                        <div class="col-md col-12">

                            <!-- <div class="card" style="width: 13rem;"> -->
                            <div class="card h-100">

                                <a href="{{ route('abonos.listado') }}">
                                    <div class="card-body">
                                        <div class="icon">
                                            <i class="bi bi-ticket-perforated"></i>
                                        </div>
                                        <h6 class="card-title">Ver abonos vendidos</h6>                                        
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>


                    <div class="row mt-5">
                        <!-- <div class="col"> -->
                        <div class="col-12">

                            <!-- <div class="card logout"> -->
                            <div class="card logout h-100">
                            
                                <a href="{{ route('usuarios.logout') }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <p class="card-text">Cerrar sesión</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>