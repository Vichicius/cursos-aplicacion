<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Videovisto;
use App\Models\Cursoadquirido;


class VideosController extends Controller
{
    //
    public function crear(Request $req){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        $video = new Video;

        if(isset($data->titulo) && isset($data->foto) && isset($data->enlace) && isset($data->curso_id)){
            try{

                $video->titulo = $data->titulo;
                $video->foto = $data->foto;
                $video->enlace = $data->enlace;
                $video->curso_id = $data->curso_id;
                $video->save();

                $response['status'] = 1;
                $response['video'] = $video;

            }catch (\Exception $e){

                $response['status'] = 0;
                $response['msg'] = "Error al intentar añadir el video, puede que el link esté repetido: ".$e->getMessage();
                
            }
        }else{
            $response['status'] = 0;
            $response['msg'] = "Introduce titulo, foto, enlace y a que video pertenece";
            
        }
        return response()->json($response);
    }

    public function ver(Request $req){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        /*
        Me pasa: 
        el usuario_id
        el enlace del vide
        Le paso: 
        nada
        Ocurre: 
        registro la visita en la tabla Videovisto
        */

        if(isset($data->usuario_id) && isset($data->enlace)){//si me pone todo ok
            try{
                //Sacar los cursos que tiene
                $cursosDelUsuario1 = Cursoadquirido::where('usuario_id', $data->usuario_id)->get('curso_id');
                $cursosDelUsuario = [];
                foreach ($cursosDelUsuario1 as $key => $value) {
                    array_push($cursosDelUsuario, $value->curso_id);
                }

                //Parar si no tiene ningun curso
                if(count($cursosDelUsuario) == 0){
                    $response["msg"] = "El usuario no tiene ningun curso";
                    return response()->json($response);
                    die;
                }

                $response["ids de los cursos del usuario"] = $cursosDelUsuario;

                //sacar id del video
                $idVideo = Video::where('enlace', $data->enlace)->value('id');
                $response["idVideo"] = $idVideo;
                //sacar id curso del video
                $idCurso = Video::where('id', $idVideo)->value('curso_id');
                $response["idCurso"] = $idCurso;
                
                if(in_array($idCurso, $cursosDelUsuario)){ //si tiene acceso al curso al que pertenece el video crea la visita
                    $videovisto = new Videovisto;
                    $videovisto->usuario_id = $data->usuario_id;
                    $videovisto->video_id = $idVideo;
                    $videovisto->save();
                    //añadir al response id video y enlace video.
                    $enlace = Video::where('id', $idVideo)->value('enlace');
                    $response["respuesta al usuario"]["video_id"] = $idVideo;
                    $response["respuesta al usuario"]["enlace"] = $enlace;
                    $response["status"] = 1;
                    $response["videovisto"] = $videovisto;
                }else{
                    $response["status"] = 0;
                    $response["msg"] = "El usuario no puede acceder al video porque no tiene el curso comprado";
                }
                
                return response()->json($response);

            }catch (\Exception $e){

                $response['status'] = 0;
                $response['msg'] = "Error al intentar registrar la visita: ".$e->getMessage();
                
            }
        }else{
            $response['status'] = 0;
            $response['msg'] = "Introduce el id del usuario y el id del video visto";
            
        }
        return response()->json($response);
    }
}
