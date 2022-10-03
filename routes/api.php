<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\API\CreatorController;
use App\Http\Controllers\API\ExamineesController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/error', [AuthController::class, 'permissionError'])->name('permission-error');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/delegate/{id}', [AdminController::class, 'delegate'])->name("admin.delegate");
        Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name("admin.delete");
    });

    Route::group(['prefix' => 'creator', 'middleware' => ['creator']], function () {
        Route::get('/', [CreatorController::class, 'index']);
        Route::group(['prefix' => 'category'], function () {
            Route::get('/', [CategoryController::class, 'getCategories'])->name("category.getCategories");
            Route::get('/{id}', [CategoryController::class, 'getCategorie'])->name("category.getCategorie");
            Route::post('/create', [CategoryController::class, 'createCategory'])->name("category.createCategory");
            Route::put('/active/{id}', [CategoryController::class, 'activeCategory'])->name("category.activeCategory");
            Route::patch('/update/{id}', [CategoryController::class, 'updateCategory'])->name("category.updateCategory");
            Route::delete('/delete/{id}', [CategoryController::class, 'deleteCategory'])->name("category.deleteCategory");
        });
        Route::group(['prefix' => 'exam'], function () {
            Route::get('/', [ExamController::class, 'getExams'])->name("exam.getExams");
            Route::get('/{id}', [ExamController::class, 'getDetailExam'])->name("exam.getDetailExam");
            Route::post('/create', [ExamController::class, 'createExam'])->name("exam.createExam");
            Route::put('/active/{id}', [ExamController::class, 'activeExam'])->name("exam.activeExam");
            Route::patch('/update/{id}', [ExamController::class, 'updateExam'])->name("exam.updateExam");
            Route::delete('/delete/{id}', [ExamController::class, 'deleteExam'])->name("exam.deleteExam");
        });

        Route::group(['prefix' => 'question'], function () {
            // Route::get('/{id}', [QuestionController::class, 'getQuestion'])->name("question.getExams");
            Route::get('/{examId}', [QuestionController::class, 'getQuestionsByExamId'])->name("question.getQuestionsByExamId");
            Route::patch('/update/{id}', [QuestionController::class, 'updateQuestion'])->name("question.updateQuestion");
            Route::delete('/delete/{id}', [QuestionController::class, 'deleteQuestion'])->name("question.deleteQuestion");
        });
    });

    Route::group(['prefix' => 'examinees', 'middleware' => ['examinees']], function () {
        Route::get('/', [ExamineesController::class, 'index']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});


// Route::resource('examinfo','ExaminfoController');

// Route::resource('examinfo','ExaminfoController');
// Route::resource('makequestion' , 'QuestionController');
// Route::resource('student','StudentController');
// Route::resource('answer','AnswerController');
// Route::resource('result' , 'ResultController');
