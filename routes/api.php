<?php

use App\Http\Controllers\API\Admin\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\API\Student\StudentController;
use App\Http\Controllers\API\Teacher\TeacherController;
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
Route::get('/error', [HomeController::class, 'permissionError'])->name('permission-error');

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function(){
        Route::get('/users', [AdminController::class, 'getUsers']);        
    });

    Route::group(['prefix' => 'teacher', 'middleware' => ['teacher']], function(){
        Route::get('/', [TeacherController::class, 'index']);        
    });

    Route::group(['prefix' => 'student', 'middleware' => ['student']], function(){
        Route::get('/', [StudentController::class, 'index']);         
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});


// Route::resource('examinfo','ExaminfoController');
// Route::resource('makequestion' , 'QuestionController');
// Route::resource('student','StudentController');
// Route::resource('answer','AnswerController');
// Route::resource('result' , 'ResultController');


