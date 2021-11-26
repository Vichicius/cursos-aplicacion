<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Cursosadquirido;


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

        $cursosadquirido = new Cursosadquirido;
        if(isset($data->usuario_id) && isset($data->curso_id)){
            try{

                $cursosadquirido->usuario_id = $data->usuario_id;
                $cursosadquirido->curso_id = $data->curso_id;
                $cursosadquirido->save();

                $response['status'] = 1;
                $response['cursosadquirido'] = $cursosadquirido;

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

        $curso = new Curso;

        if(isset($data->filtro)){ //filtrar la búsqueda
            $todosCursos = Curso::all();

            //Curso::where('titulo', "LIKE", "%" . $data->filtro . "%");

            foreach ($todosCursos as $key => $value) {
                if(str_contains($value->titulo, $data->filtro)){
                    $response["Cursos"][$key] = $value;
                }
            }

        }else{ //mostrar todos los cursos
            $todosCursos = Curso::all();
            foreach ($todosCursos as $key => $value) {
                $response["Cursos"][$key] = $value;
            }
        }

        return response()->json($response);
    }


}
