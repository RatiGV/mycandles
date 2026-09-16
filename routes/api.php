<?php
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
/*
 * /test, /generate და /generate-companies მარშრუტები ამოღებულია, რადგან
 * მათი კონტროლერი App\Http\Controllers\Front\IndexController არ არსებობს.
 * ამის გამო `php artisan route:list` ReflectionException-ით ითიშებოდა.
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
