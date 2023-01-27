<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AuthController extends Controller
{
    public function registro(Request $peticion){
        $peticion->validate([
            'username' => 'required | unique:usuarios,username',
            'email' => 'required | unique:usuarios,email',
            'password' => 'required | confirmed',
        ]);

        $nuevo_usuario = new User();

        $nuevo_usuario->username = $peticion->username;
        $nuevo_usuario->email = $peticion->email;
        $nuevo_usuario->password = Hash::make($peticion->password);

        $nuevo_usuario -> save();

        return response()->json(["mensaje"=>"Usuario registrado correctamente"],201);
    }

    public function login(Request $peticion){
        $peticion->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $usuario = User::where("username", "=", $peticion->username)->first();
        if(isset($usuario))
        {
            if(Hash::check($peticion->password, $usuario->password)){
                $token = $usuario->createToken("auth_token")->plainTextToken;
                return response()->json(["mensaje"=>"Inicio de sesion exitoso", "token de acceso" => $token],201);
            }
            else{
                return response()->json(["mensaje"=>"Contraseña incorrecta"],401);
            }
        }
        else
        {
            return response()->json(["mensaje"=>"Usuario no encontrado"],404);
        }
    }
    public function perfil(){
        return Auth::user();
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json(["mensaje"=>"Sesion cerrada"],200);
    }
}
