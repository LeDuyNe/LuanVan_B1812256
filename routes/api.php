<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionBankController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\ExamineesController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\API\ResultController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('update-password');
    Route::patch('/update', [AuthController::class, 'updateInfo'])->name('update-info');

    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
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

        Route::group(['prefix' => 'questionbank'], function () {
            Route::patch('/update/{id}', [QuestionBankController::class, 'updateQuestionBank'])->name("questionbank.updateQuestionBank");
            Route::get('/', [QuestionBankController::class, 'getQuestionBank'])->name("questionbank.getQuestionBank");
            Route::patch('/update/question', [QuestionBankController::class, 'updateQuestion'])->name("questionbank.updateQuestion");
            Route::delete('/delete/question/{id}', [QuestionBankController::class, 'deleteQuestion'])->name("questionbank.deleteQuestion");
            Route::post('/create', [QuestionBankController::class, 'createQuestionBank'])->name("questionbank.createQuestionBank");
            Route::post('/add/{id}', [QuestionBankController::class, 'addQuestionBank'])->name("questionbank.addQuestionBank");
            Route::put('/active/{id}', [QuestionBankController::class, 'activeQuestionBank'])->name("questionbank.activeQuestionBank");
            Route::delete('/delete/{id}', [QuestionBankController::class, 'deleteQuestionBank'])->name("questionbank.deleteQuestionBank");
            Route::get('/{id}', [QuestionBankController::class, 'getDetailQuestionBank'])->name("questionbank.getDetailQuestionBank");
        });

        Route::group(['prefix' => 'result'], function () {
            Route::get('/{id}', [ResultController::class, 'getResult'])->name("result.getResult");
            Route::get('/detail/{id}', [ResultController::class, 'getDetailResult'])->name("result.getDetailResult");
            Route::patch('/{id}', [ResultController::class, 'updateResult'])->name("result.updateResult");
        });
    });

    Route::group(['prefix' => 'examinees', 'middleware' => ['examinees']], function () {
        Route::post('/submit', [ExamineesController::class, 'submitExam'])->name("examinees.submitExam");
        Route::get('/result', [ExamineesController::class, 'getResult'])->name("examinees.getResult");
        Route::get('/result/{id}', [ExamineesController::class, 'getDetailResult'])->name("examinees.getDetailResult");
        Route::get('/{id}', [ExamineesController::class, 'getExam'])->name("examinees.getExam");
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
