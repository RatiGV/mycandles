<?php

use App\Http\Controllers\Front\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/test', [IndexController::class, 'test']);
Route::post('/generate', [IndexController::class, 'exportUsers']);
Route::post('/generate-companies', [IndexController::class, 'exportCompanies']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
