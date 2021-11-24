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
            try{
                $usuario->nombre = $data->nombre;
                $usuario->foto = $data->foto;
                $usuario->email = $data->email;
                $usuario->contrasena = $data->contrasena;
                $usuario->activo = True;
                $usuario->save();
            }catch (\Exception $e){
                $response['msg'] = "Error al intentar añadir el usuario: ".$e->getMessage();
                $response['status'] = 0;
                return response($response);
            }
            
        }else{
            print_r("nooo");
        }
        //devolver el objeto usuario en json
        return response()->json($usuario);
    }
    public function editar(Request $req, int $id){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        
        $usuario = Usuario::find($id);
        if ($usuario == null){
            $response['msg'] = "Id fuera de rango.";
            $response['status'] = 0;
            return response($response);
        }
        

        //comprobar qué datos quiere cambiar y cambiarlos 
        if (isset($data->nombre)) $usuario->nombre = $data->nombre;
        if (isset($data->foto)) $usuario->foto = $data->foto;
        if (isset($data->contrasena)) $usuario->contrasena = $data->contrasena;
        if (isset($data->activo)) $usuario->activo = $data->activo;
        //si ha intentado cambiar el email, avisarle que no se puede cambiar
        if (isset($data->email)) $response['alert'] = "No es posible cambiar el email";

        //sobreescribirlo
        $usuario->save();
        $response['usuario'] = $usuario;
        return response()->json($response);
        
    }
}
