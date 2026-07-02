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







/*** Autenticación con Laravel Sanctum
- Cada usuario tiene tokens API.
- Token enviado en cabecera Authorization: Authorization: Bearer <token_usuario>
- Middleware auth:sanctum protege rutas de contactos.
- Cada acción de API regenera token y lo devuelve al cliente.
*/


/*** Poner en marcha Sanctum

1) Instalación y configuración de Sanctum
1º Instalar el paquete:
composer require laravel/sanctum

2º Publicar la configuración y migraciones:
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

Esto creará la tabla personal_access_tokens que es donde se almacenarán los tokens.

* Documentación oficial: https://laravel.com/docs/12.x/sanctum#installation

3º Opcional. Agregar el middleware en config/sanctum.php y en app/Http/Kernel.php
Para API token, no necesitas este middleware, solo usarás los métodos de emisión de tokens.

Laravel 12 sigue usando EnsureFrontendRequestsAreStateful solo si trabajas con SPA. 
SPA: Single Page Application, es un tipo de aplicación web donde toda la interfaz se carga 
en una sola página y se actualiza dinámicamente usando JavaScript.

-------------------------------------------------------------------------------------------
2) Creación de tokens para usuarios
En tu controlador, puedes generar un token personal así:
$token = Auth::user->createToken('token-name')->plainTextToken;
return response()->json(['token' => $token]);

* Documentación oficial: https://laravel.com/docs/12.x/sanctum#issuing-api-tokens


2.2) Revocación de tokens
Auth::user->tokens()->where('id', $tokenId)->delete();

Para revocar todos los tokens de un usuario:
$user->tokens()->delete();

* Documentación oficial: https://laravel.com/docs/12.x/sanctum#revoking-tokens

-------------------------------------------------------------------------------------------
3) Uso del token en peticiones API

1º El cliente debe enviar el token en el encabezado Authorization como Bearer:
Authorization: Bearer <token>

2º En tu API, protege las rutas con el middleware auth:sanctum:
Route::middleware('auth:sanctum')->get('/user', function () { return auth()->user(); });

*Documentación oficial: https://laravel.com/docs/12.x/sanctum#protecting-routes

*/