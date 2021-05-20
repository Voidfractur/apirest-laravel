<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LibroController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//cuando esta en la carpeta raiz a a retornar la vista welcome

/*Route::get('/', function () {
    return view('welcome');


});*/
/*
//cuando este en la ruta registro va a llamar esta vista
Route::get('registro',function(){
    return "Bienvenido al registro de usuarios";
});

//cuando este en la ruta registro va a llamar esta vista
Route::get('registro/libros/{libroid}',function($libroId){
    return "Bienvenido al registro de libros por ID: ".$libroId;
});
*/
Route::get('consulta/{id}', [LibroController::class, 'buscarLibro']);
Route::get('login', [UsuarioController::class, 'login']);
Route::get('usuarios', [UsuarioController::class, 'index']);
Route::post('registro', [UsuarioController::class, 'store']);
Route::get('libros', [LibroController::class, 'index']);
Route::post('libros', [LibroController::class, 'store']);
Route::put('libros/{id}', [LibroController::class, 'update']);
Route::delete('libros/{id}', [LibroController::class, 'destroy']);
