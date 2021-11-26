<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Video;
use App\Models\Cursoadquirido;
use Illuminate\Support\Facades\DB;


class CursosController extends Controller
{
    //
    public function crear(Request $req){
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

    public function comprar(Request $req){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        $cursosadquiridos = new Cursoadquirido;
        if(isset($data->usuario_id) && isset($data->curso_id)){
            try{

                $cursosadquiridos->usuario_id = $data->usuario_id;
                $cursosadquiridos->curso_id = $data->curso_id;
                $cursosadquiridos->save();

                $response['status'] = 1;
                $response['cursosadquirido'] = $cursosadquiridos;

            }catch (\Exception $e){

                $response['status'] = 0;
                $response['msg'] = "Error al intentar comprar el curso: ".$e->getMessage();
                
            }
        }else{
            $response['status'] = 0;
            $response['msg'] = "Introduce el id del usuario y el id del curso comprado";
            
        }
        return response()->json($response);
    }

    public function verCursos(Request $req){
        //recoger la info del request (viene en json)
        $jdata = $req->getContent();
        //pasar el json a objeto
        $data = json_decode($jdata);

        $todosCursos = Curso::all();

        if(isset($data->filtro)){ //si ha puesto un filtro:

            foreach ($todosCursos as $key => $value) {
                if(str_contains($value->titulo, $data->filtro)){//curso seleccionado aplicando filtro
                    $response[$key+1]["Titulo"] = $value->titulo;
                    $response[$key+1]["Foto"] = $value->foto;
                    $videos = Video::where('curso_id', $value->id)->get();

                    $numeroVideos = count($videos);
                    $response[$key+1]["Videos"] = $numeroVideos;
                }
            }

        }else{ //mostrar todos los cursos
            foreach ($todosCursos as $key => $value) {
                $response[$key+1]["Titulo"] = $value->titulo;
                $response[$key+1]["Foto"] = $value->foto;
                $videos = Video::where('curso_id', $value->id)->get();
                
                $numeroVideos = count($videos);
                $response[$key+1]["Videos"] = $numeroVideos;
            }
        }

        return response()->json($response);
    }


}
