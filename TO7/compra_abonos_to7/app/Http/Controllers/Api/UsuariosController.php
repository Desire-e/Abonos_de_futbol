<?php
/**** LOGIN / LOGOUT (SANCTUM) 



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
// Validator para validar formularios o datos recibidos (POST, JSON, etc.)


class UsuariosController extends Controller{
    
    // Procesa login 
    public function login(Request $request) {
        try {
            // Validar json (valor de campos recibidos)
            $validUser = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);

            // Si falla, manda respuesta fallida -- 401 no autorizado
            if($validUser->fails()){
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => null,
                    'errors' => $validUser->errors(),
                    'token' => null
                ], 401);
            }

            
            // Comprueba usuario existente y contraseña coincidente
            if(Auth::attempt($request->only(['username', 'password']))) {
                // Obtener el usuario y mandar token recien creado
                return response()->json([
                    'status' => true,
                    'data' => null,
                    'message' => 'Administrador logueado correctamente',
                    'errors' => null,    
                    // usuario y creación de su token
                    'token' => Auth::user()->createToken("API_TOKEN")->plainTextToken
                ], 200); // 200 OK
            }
            // Respuesta fallida ante credenciales inválidas
            else {
                $validUser->errors()->add('password', 'Nombre de usuario y/o contraseña incorrectos');
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => null,
                    'errors' => $validUser->errors(),
                    'token' => null
                ], 401);  
            }

        }
        // Respuesta fallida ante excepciones
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => null,
                'errors' => $e->getMessage(),
                'token' => null
            ], 500);
        }

    }
    


    
    // Procesa logout 
    public function logout() {
        try {
            // Borra el token de autentificacion.
            // **Se vuelve a crear en cada login y operación que requiere 
            // tener cuenta (regenerar para mantener sesión abierta).
            Auth::user()->tokens()->delete();

            // Da respuesta en json al cliente REST
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Sesión cerrada correctamente',
                'errors' => null,
                'token' => null
            ], 200); 
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'errors' => null,
            ], 500);
        }
    }

}

*/