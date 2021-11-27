<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Cursoadquirido;
use App\Models\Videovisto;
use App\Models\Curso;
use App\Models\Video;

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
        $relaciones = Cursoadquirido::all();

        $idCursosAdquiridos = [];

        //Primero sacar las ids de los cursos que tiene el usuario
        foreach ($relaciones as $key => $value) { 
            if($value->usuario_id == $usuario->id){ //usuario_id coincide con el id del usuario
                array_push($idCursosAdquiridos, $value->curso_id);
            }
        }
        if (count($idCursosAdquiridos) == 0){
            $response["status"] = 0;
            $response["msg"] = "El usuario no tiene ningun curso";
        }
        //Ahora sacar los cursos y mostrarle lo que necesita (titulo, foto y numero de videos)
        foreach ($idCursosAdquiridos as $key => $value) {
            $cursoadquirido = Curso::find($value);
            $response[$key+1]["Titulo"] = $cursoadquirido->titulo;
            $response[$key+1]["Foto"] = $cursoadquirido->foto;
            $videos = Video::where('curso_id', $cursoadquirido->id)->get();
            
            $numeroVideos = count($videos);
            $response[$key+1]["Videos"] = $numeroVideos;
        }
        

        return response()->json($response);
    }
    public function verVideosDelCursoAdquirido(Request $req, int $id){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        //$id es el id del curso deseado
        if(isset($data->usuario_id)){
            try{
                $curso_id = $id;
                $usuario_id = $data->usuario_id;
                $usuario = Usuario::find($usuario_id);
                $curso = Curso::find($curso_id);


                $relaciones = Cursoadquirido::all();

                $idCursosAdquiridos = [];
        
                //Primero sacar las ids de los cursos que tiene el usuario
                foreach ($relaciones as $key => $value) { 
                    if($value->usuario_id == $usuario->id){ //usuario_id coincide con el id del usuario
                        array_push($idCursosAdquiridos, $value->curso_id);
                    }
                }
                if(in_array($curso_id, $idCursosAdquiridos)){ //comprobar que el curso en cuestion está en el array de los cursos que tiene el usuario 
                    //sacar los videos del curso
                    $videos = Video::where('curso_id', $curso->id)->get();
                                    
                    foreach ($videos as $key => $video) { //recorre todos los videos del curso
                        $response["msg"][$key+1]['Titulo'] = $video->titulo;
                        $response["msg"][$key+1]['Foto'] = $video->foto;
                        try{
                            $videosVistosUsuario = Videovisto::where('usuario_id', $usuario_id)->get();
                            foreach ($videosVistosUsuario as $key2 => $tablaVideosVistos) {
                                if($tablaVideosVistos->video_id == $video->id){//Si está registrado su visita
                                    $response["msg"][$key+1]['Visto'] = "Si";
                                }
                            }
                            //Si el usuario ha visto algun video pero no este
                            if ($response["msg"][$key+1]['Visto'] == null) $response["msg"][$key+1]['Visto'] = "No";
                        }catch(\Exception $e){
                            //el usuario no ha visto ningún video de ningún curso
                            $response["msg"][$key+1]['Visto'] = "No";
                        }

                    }
                }else{
                    $response['status'] = 0;
                    $response['msg'] = "Error: El usuario no tiene comprado el curso ";
                }
                
            }catch (\Exception $e){
                $response['status'] = 0;
                $response['msg'] = "Error al buscar los videos del curso: ".$e->getMessage();
            }
            
        }else{
            $response['status'] = 0;
            $response['msg'] = "Error al introducir los datos de la API. Introduce usuario_id";
        }
        return response()->json($response);
    }

}
