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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth::routes();
// Route::
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/home', 'HomeController@index')->name('home');
// Route::resource('examinfo','ExaminfoController');
// Route::resource('makequestion' , 'QuestionController');
// Route::resource('student','StudentController');
// Route::resource('answer','AnswerController');
// Route::resource('result' , 'ResultController');

