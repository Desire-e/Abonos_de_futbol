<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

use Illuminate\Support\Facades\Hash;
/* Para encriptar contraseñas de forma segura. */

use Illuminate\Support\Facades\Auth;
/* Para gestionar la autenticación de usuarios que extienden de \Auth\User: 
login, logout, comprobar si hay usuario autenticado, acceder al usuario actual */


class UsuariosController extends Controller{

    /** Formulario de login **/
    public function login() {
        // Comprueba si ya logueado
        // - si no logeado, permanece en formulario
        // - si logeado, dirige a listado

        if(Auth::check())  {
            return redirect()->route('abonos.listado');
        }

        return view('usuarios.login');
    }





    /** Procesa login **/
    public function authenticate(Request $request) {
        // Comprueba campos rellenos
        $datos = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Login. Comprueba:
        // - el usuario existe (su username existe registrado en BD tabla usuarios)
        // - la contraseña es correcta (coincide con el hash)
        if (Auth::attempt($datos)) {
            // Regenera el ID de sesión (evita ataques de session fixation)
            $request->session()->regenerate();

            // Dirige a listado
            return redirect()->route('abonos.listado');
        }


        // Si usuario no existe en BD / password incorrecta: 
        // permanece con mensaje de error (accesible con {{ $message }})
        return back()->withErrors([ 'username' => 'Nombre de usuario y/o contraseña incorrectos' ]);
    }
    



    
    /** Procesa logout **/
    public function logout(Request $request) {
        // elimina la autenticación del usuario.
        Auth::logout();

        // invalida la sesión actual
        $request->session()->invalidate();
        // regenera el token CSRF (seguridad extra)
        $request->session()->regenerateToken();

        return redirect()->route('usuarios.login');
    }



    
    public function forgot() {
        if(Auth::check())  {
            return redirect()->route('abonos.listado');
        }

        return view('usuarios.forgot');
    }

}