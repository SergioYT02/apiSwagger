<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\Facades\Swagger;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::group(['prefix' => 'api'], function () {
    // Otras rutas de la API...



    // Ruta para generar la documentación de Swagger/OpenAPI
    Route::get('/docs', function () {
        return view('swagger.index'); // Puedes personalizar esta vista si lo deseas
    });

    // Ruta para acceder al archivo JSON de la documentación de Swagger/OpenAPI
    Route::get('/docs-json', function () {
        return response()->json(Swagger::getRawDocs()); // Devuelve el archivo JSON generado por Swagger
    });
});
Route::group(['middleware'=>["auth:sanctum"]],function() {
    Route::get('/auth/users', [AuthController::class, 'listuser']);//muestra todos los datos
    Route::get('/auth/user', [PersonaController::class, 'index']); //mustra todos los datos
    Route::get('/auth/listuser', [PersonaController::class, 'list']); // muestra el usuario y el rol
    Route::get('/auth/list', [PersonaController::class, 'listuserrol']);  // muestra el usuario el nombre de la persona y el rol
    Route::put('/auth/update/{id}',[AuthController::class, 'updatename']);// actualiza el nombre del usuario
    Route::put('/auth/update-password/{id}',[AuthController::class, 'updatepassword']); // actualiza la contraseña
    Route::delete('/auth/delete/personas/{id}', [PersonaController::class, 'destroypersona']); 
    Route::delete('/auth/delete/users/{id}',[AuthController::class, 'destroyusuario']); //elimina un usuario
   
});
