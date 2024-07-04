<?php

use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\GraficoController;
use App\Http\Controllers\NuevoController;

Route::get('/graficos/index2', [NuevoController::class, 'index2'])->name('graficos.index2');
Route::post('/graficos/filtrar2', [NuevoController::class, 'filtrar2'])->name('graficos.filtrar2');

Route::get('/grafico', [GraficoController::class, 'index']);
Route::post('/grafico/filtrar', [GraficoController::class, 'filtrar'])->name('grafico.filtrar');

//Route::post('/grafico/filtrar', [GraficoController::class, 'filtrar']);
// Route::get('/grafico/{productoId}', [GraficoController::class, 'show']);
Route::get('/', HomeController::class);
Route::get('/post', [PostController::class,'index']);
Route::get('/post/create', [PostController::class,'create']);
Route::get('/post/{post}', [PostController::class,'show']);


// Route::get('/', function () {
//     return view('welcome');
// });



// Route::get('/post/{post}/{category}', function($post,$category){
//     return "post 2 {$post} de la categoria fronend {$category}";
// });

// Route::get('/post/{post}/{category?}', function($post, $category=null){

//     if ($category){
//         return "post 2 {$post} de la categoria fronend {$category}";
//     }
//     return "aqui se mostrara el {$post}";
    
// });