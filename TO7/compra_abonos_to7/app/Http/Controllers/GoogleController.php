<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class GoogleController extends Controller
{
    /******************************************** 
     * REDIRIGE A FORMULARIO LOGIN CON GOOGLE
     *******************************************/
    // Laravel Socialite usa driver Google
    // Genera URL OAuth y redirige al usuario a formulario login de Google
    public function login() {
        return Socialite::driver('google')->redirect();
    }
  


    /************************************************************
     * CALLBACK. PROCESAR RESPUESTAS DE GOOGLE AL INTENTAR LOGIN
     ************************************************************/
    // Después del login, Google envía petición GET con parametro ?code=XXXX
    public function callback() {
        
        try {
            // 1º Google devuelve usuario
            // Contiene datos del usuario: id de google, nombre, email, avatar
            $usuarioGoogle = Socialite::driver('google')->user();
            
            // dd($usuarioGoogle);

            // 2º Comprueba en BD si el usuario ya estaba registrado, comprobando su ID
            $existeUsuario = Usuario::where('google_id', $usuarioGoogle->id)->first();

            // 3º 
            // a) SI USUARIO REGISTRADO (existe registro con ese ID)
            if($existeUsuario) {

                // Inicia sesión si usuario existe en BD:
                // login() - inicia sesión sin comprobar usuario + contraseña, solo 
                // comprueba que existe en BD (login con Google no usa contraseña)
                // attemp() - comprueba usuario + contraseña sin hash
                Auth::login($existeUsuario);
                
                return redirect()->route('abonos.listado');
            }
            // b) SI USUARIO NO REGISTRADO
            else {
                // Guarda datos de usuarioGoogle en datos temporales de sesión
                session(['usuario_google' => $usuarioGoogle]);

                // Muestra página de confirmación
                // Si confirma, se hace petición POST a register()
                return view('google.confirmarRegistro');
            }   

        }
        // 4º Si algo falla:
        // configuración incorrecta
        // token inválido
        // usuario cancela
        catch (Exception $e) {
            return redirect()->route('usuarios.login')
            ->withErrors(['google' => $e->getMessage()]);
            // dd($e->getMessage());

        }
    }



    // public function index() {
    //     if(Auth::check() == true) { return redirect()->route('abonos.listado'); }

    //     return view('abonos.index');
    // }
    
    /*******************************************
     * REGISTRO DE NUEVO USUARIO TRAS CONFIRMAR 
     *******************************************/
    public function register(Request $request){
        // Si no se hizo click a ninguno de los botones
        $request->validate([
            'accion' => 'required|in:confirmar,cancelar'
        ]);    
    
    
        // Obtiene usuarioGoogle de datos temporales de sesión
        $usuarioGoogle = session('usuario_google');
        
        // Si se accede a esta ruta sin callback() previo, redirige a login
        if (!$usuarioGoogle) {
            return redirect()->route('usuarios.login');
        }


        // Evalúa si se canceló o se confirmó
        if($request->accion === 'cancelar'){
            // Elimina usuarioGoogle almacenado temporalmente en sesión
            session()->forget('usuario_google');
            return redirect()->route('usuarios.login');
        } 
        else if ($request->accion === 'confirmar'){
            
            try {
                // Registra usuario, usando sus datos obtenidos de Google
                $datos = array();
                $datos['username'] = $usuarioGoogle->email;
                $datos['password'] = Hash::make(Str::random(32)); // Cadena aleatoria de 32 caracteres
                $datos['google_id'] = $usuarioGoogle->id;
                    
                $nuevoUsuario = Usuario::create($datos);
            
                // Hacer login automático
                Auth::login($nuevoUsuario);

                // Elimina usuarioGoogle almacenado temporalmente en sesión
                session()->forget('usuario_google');

                return redirect()->route('abonos.listado');
            } 
            catch(Exception $e){
                return redirect()->route('usuarios.login')->withErrors(['google' => $e->getMessage()]);
                // dd($e->getMessage());
            }
        } 
        else {
            session()->forget('usuario_google');
            return redirect()->route('usuarios.login');
        }
    }

}