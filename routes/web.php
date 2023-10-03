<?php

use Illuminate\Support\Facades\Route;

// DB Connection
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

// Controllers
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $dbStatus = "DOWN";
    // Test database connection
    try {
        DB::connection()->getPdo();
        $dbStatus = "UP";
    } catch (\Exception $e) {
        \Log::error("Could not connect to the database. Please check your configuration. error:" . $e );
    }
    $arr = [
        "Hello" => "World",
        "DB" => $dbStatus
    ];
    return $arr;
});

Route::get('/token', function () {
    $arr = [
        "CSRF Token" => csrf_token()
    ];
    return $arr;
});

// Handle if user not logged in or invalid sanctum token
Route::get('/login', function () {
    $arr = [
        "Please" => "Login"
    ];
    return $arr;
})->name("login");

// Handle registration functions
Route::controller(RegisterController::class)->group(function () {
    Route::get('/register/verifymail', 'verifymail')->name("verifymail");
    Route::post('/register/newregister', 'newregister');
});

// Handle authentication functions
Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/login', 'login');
    Route::middleware('auth:sanctum')->post('/auth/logout', 'logout');
    Route::middleware('auth:sanctum')->any('/hello', 'authtest'); // test using sanctum as middleware, can access this route if sanctum token valid
});
