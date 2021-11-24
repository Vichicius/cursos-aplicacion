<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class VideosController extends Controller
{
    //
    public function registrar(Request $req){
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
                $response['msg'] = "Error al intentar añadir el video: ".$e->getMessage();
                
            }
        }else{
            $response['status'] = 0;
            $response['msg'] = "Introduce titulo, foto, enlace y a que video pertenece";
            
        }
        return response()->json($response);
    }
}
