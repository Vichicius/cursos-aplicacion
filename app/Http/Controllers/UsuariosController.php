<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\CursosAdquirido;
use App\Models\Curso;

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

                $response['status'] = 1;
                $response['usuario'] = $usuario;

            }catch (\Exception $e){
                $response['status'] = 0;
                $response['msg'] = "Error al intentar añadir el usuario: ".$e->getMessage();
            }
            
        }else{
            $response['status'] = 0;
            $response['msg'] = "No has introducido todos los campos (nombre, foto, email, contraseña, activo): ".$e->getMessage();
        }

        //devolver $response como json
        return response()->json($response);
    }
    
    public function editar(Request $req, int $id){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        $usuario = Usuario::find($id);
        if ($usuario == null){
            $response['status'] = 0;
            $response['msg'] = "Id fuera de rango.";
            return response($response);
            
        }else{
            //comprobar qué datos quiere cambiar y cambiarlos 
            if (isset($data->nombre)) $usuario->nombre = $data->nombre;
            if (isset($data->foto)) $usuario->foto = $data->foto;
            if (isset($data->contrasena)) $usuario->contrasena = $data->contrasena;
            if (isset($data->activo)) $usuario->activo = $data->activo;

            //si ha intentado cambiar el email, avisarle que no se puede cambiar
            if (isset($data->email)) $response['alert'] = "No es posible cambiar el email, el resto de datos han sido cambiados";

            //sobreescribirlo
            $usuario->save();
            $response['usuario'] = $usuario;
        }

        //devolver $response como json
        return response()->json($response);
        
    }

    public function verCursosUsuario(Request $req, int $id){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        $usuario = Usuario::find($id);
        $relaciones = CursosAdquirido::all();
        $todosCursos = Curso::all();
        $idCursosAdquiridos = [];
        foreach ($relaciones as $key => $value) {
            if($value->user_id == $usuario->id){ //relaciones-> userid coincide con usuario id
                array_push($idCursosAdquiridos, $value->curso_id);
            }
        }
        $response["msg"] = $idCursosAdquiridos;
            

        return response()->json($response);
    }

}
