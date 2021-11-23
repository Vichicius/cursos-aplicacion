<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuariosController extends Controller
{
    //
    public function registrar(Request $req){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);
        $usuario = new Usuario;
        if (isset($data->nombre) && isset($data->foto) && isset($data->email) && isset($data->contrasena)){
            $usuario->nombre = $data->nombre;
            $usuario->foto = $data->foto;
            $usuario->email = $data->email;
            $usuario->contrasena = $data->contrasena;
            $usuario->activo = True;
            $usuario->save();
        }else{
            print_r("nooo");
        }
        //devolver el objeto en json
        return response()->json($usuario);
    }
}
