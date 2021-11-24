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
        $curso = new Curso;

        if(isset($data->titulo) && isset($data->descripcion) && isset($data->foto)){
            try{

                $curso->titulo = $data->titulo;
                $curso->descripcion = $data->descripcion;
                $curso->foto = $data->foto;
                $curso->save();

                $response['status'] = 1;
                $response['curso'] = $curso;

            }catch (\Exception $e){

                $response['status'] = 0;
                $response['msg'] = "Error al intentar añadir el curso: ".$e->getMessage();
                
            }
        }else{
            $response['status'] = 0;
            $response['msg'] = "Introduce titulo, descripcion y la url de la foto";
            
        }
        return response()->json($response);
    }
}
