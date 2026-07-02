<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AbonosController;
use App\Http\Controllers\Api\UsuariosController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


/*** RUTAS DE LA API.
/routes/api.php:
- Para APIs REST
- No usa sesiones ni @csrf
- Usa JSON
- Usa tokens Sanctum para autentificar

/routes/web.php:
- Para apps web
- Usa sesiones y @csrf para autentificar
- Usa vistas (HTML) Y cookies
*/




// ---- Muestra formulario de compra -- manda tipo_abonos para imprimir en su vista
Route::get('/abonos/tipoAbonos',[AbonosController::class, 'tipoAbonos'])->name('rest.abonos.api.tipoAbonos');

// ---- Realiza registro de compra en BD -- tabla abonos
Route::post('/abonos/insert',[AbonosController::class, 'insert'])->name('rest.abonos.insert');

// ---- Muestra ticket de compra -- tabla abonos
Route::get('/abonos/ticket/{id}',[AbonosController::class, 'ticket'])->name('rest.abonos.ticket');



// ---- Extra. Servir imagenes
Route::get('/abonos/imageResources',[AbonosController::class, 'imageResources'])->name('rest.abonos.imageResources');




// LOGIN / LOGOUT (SANCTUM) 

// ---- Muestra listado de abonos para los admin logueados -- tabla abonos
// Route::get('/abonos/listado',[AbonosController::class, 'listado'])
// ->name('rest.abonos.listado')->middleware('auth:sanctum');

// ---- Procesa login -- tabla usuarios
// Route::post('/usuarios/login',[UsuariosController::class, 'login'])->name('rest.usuarios.login');

// ---- Procesa logout -- revocar token
// Route::get('/usuarios/logout',[UsuariosController::class, 'logout'])->name('rest.usuarios.logout')
// ->middleware('auth:sanctum');
