<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\VideosController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('usuario')->group(function(){
    Route::put('/registrar', [UsuariosController::class,'registrar']); //1 X
    Route::put('/editar/{id}', [UsuariosController::class,'editar']); //2 X
    Route::put('/cursos/{id}', [UsuariosController::class,'verCursosUsuario']); //6 ver los cursos adquiridos del usuario
    Route::put('/videos/{id}', [UsuariosController::class,'verVideosUsuario']); //7 ver los videos del curso adquirido previamente

});
Route::prefix('curso')->group(function(){
    Route::put('/crear', [CursosController::class,'crear']); //3 X
    Route::put('/comprar', [CursosController::class,'comprar']);//5 X
    Route::put('/cursos', [CursosController::class,'verCursos']); //4 ver todos los cursos

});
Route::prefix('videos')->group(function(){
    Route::put('/crear', [VideosController::class,'crear']); //3 X. Se le asigna al curso directamente
    Route::put('/ver', [VideosController::class,'ver']);//8
});
