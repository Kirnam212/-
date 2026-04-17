<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerVoteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionVoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->name('answers.store');
    Route::post('/questions/{question}/vote', [QuestionVoteController::class, 'store'])->name('questions.vote');
    Route::post('/answers/{answer}/vote', [AnswerVoteController::class, 'store'])->name('answers.vote');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');
