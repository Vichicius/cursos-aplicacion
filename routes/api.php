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
    Route::put('/registrar', [UsuariosController::class,'registrar']);
    Route::put('/editar/{id}', [UsuariosController::class,'editar']);
});
Route::prefix('curso')->group(function(){
    Route::put('/crear', [CursosController::class,'crear']);
});
Route::prefix('videos')->group(function(){
    Route::put('/crear', [CursosController::class,'crear']); //aqui se le asigna al curso directamente
});
