<?php

use Illuminate\Support\Facades\Route;

/** Mis controladores **/
use App\Http\Controllers\AbonosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\TipoAbonosController;
use App\Http\Controllers\GoogleController;



/** Rutas que ejecutan controllers + action **/

// ---- Landing
Route::get('/',[AbonosController::class, 'index'])->name('abonos.index');
Route::get('/abonos/index',[AbonosController::class, 'index'])->name('abonos.index');



// ---- Muestra formulario de compra
Route::get('/abonos/compra',[AbonosController::class, 'compra'])->name('abonos.compra');
// ---- Realiza registro de compra en BD -- tabla abonos
Route::post('/abonos/insert',[AbonosController::class, 'insert'])->name('abonos.insert');



// ---- Muestra ticket de compra
// {id}     el id del abono recien creado en BD
Route::get('/abonos/ticket{id}',[AbonosController::class, 'ticket'])->name('abonos.ticket');
/**
 * URL firmada 
 * (control de acceso a ticket de una persona que no lo compró, sin Auth::id())
 */
// Route::get('/abonos/ticket{id}',[AbonosController::class, 'ticket'])->name('abonos.ticket')->middleware('signed');



// ---- Muestra listado de abonos para los admin logueados
Route::get('/abonos/listado',[AbonosController::class, 'listado'])->name('abonos.listado');
// ---- Muestra página de aviso (contenido protegido)
Route::get('/abonos/prohibido',[AbonosController::class, 'prohibido'])->name('abonos.prohibido');



// ---- Muestra formulario de login
Route::get('/usuarios/login',[UsuariosController::class, 'login'])->name('usuarios.login');
// ---- Procesa login
Route::post('/usuarios/authenticate',[UsuariosController::class, 'authenticate'])->name('usuarios.authenticate');
// ---- Procesa logout
Route::get('/usuarios/logout',[UsuariosController::class, 'logout'])->name('usuarios.logout');
// ---- Olvido contraseña
Route::get('/usuarios/forgot',[UsuariosController::class, 'forgot'])->name('usuarios.forgot');



// ---- Formulario admin. Añadir registros tipo_abonos
Route::get('/tipoAbonos/formularioTipoAbonos',[TipoAbonosController::class, 'formularioTipoAbonos'])
->name('tipoAbonos.formularioTipoAbonos');
// ---- Añadir registros tipo_abonos
Route::post('/tipoAbonos/insertTipoAbono',[TipoAbonosController::class, 'insertTipoAbono'])
->name('tipoAbonos.insertTipoAbono');
// ---- Validaciones en tiempo real
Route::post('/tipoAbonos/validarDescripcion',[TipoAbonosController::class, 'validarDescripcion'])
->name('tipoAbonos.validarDescripcion');
Route::post('/tipoAbonos/validarPrecio',[TipoAbonosController::class, 'validarPrecio'])
->name('tipoAbonos.validarPrecio');
Route::post('/tipoAbonos/validarCodigo',[TipoAbonosController::class, 'validarCodigo'])
->name('tipoAbonos.validarCodigo');
Route::post('/tipoAbonos/validarIcono',[TipoAbonosController::class, 'validarIcono'])
->name('tipoAbonos.validarIcono');

// ---- Listado de tipo abonos. Ver registros tipo_abonos
Route::get('/tipoAbonos/listadoTipoAbonos',[TipoAbonosController::class, 'listadoTipoAbonos'])
->name('tipoAbonos.listadoTipoAbonos');
Route::post('/tipoAbonos/getListadoTipoAbonos',[TipoAbonosController::class, 'getListadoTipoAbonos'])
->name('tipoAbonos.getListadoTipoAbonos');
Route::post('/tipoAbonos/deleteListadoTipoAbonos',[TipoAbonosController::class, 'deleteListadoTipoAbonos'])
->name('tipoAbonos.deleteListadoTipoAbonos');



// ---- Autentificación con Google Gmail (OAuth 2.0)
// Login Google - Envía usuario a Google login.
Route::get('/google/login',[GoogleController::class, 'login'])->name('google.login');

// Callback - Procesa respuestas de Google al intentar login
// Google devolverá el usuario autenticado:
// Haciendo al Servidor petición GET con parametro ?code=XXXXX
Route::get('/google/callback',[GoogleController::class, 'callback'])->name('google.callback');

// Tras confirmar / cancelar registro si aun no lo estaba
Route::post('/google/register',[GoogleController::class, 'register'])->name('google.register');



// ---- Extra. Generar y descargar ticket forzado (sin almacenar en disco)
Route::get('/abonos/downloadTicket/{id}',[AbonosController::class, 'downloadTicket'])->name('abonos.downloadTicket');
