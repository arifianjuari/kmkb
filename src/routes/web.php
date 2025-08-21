<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Authentication Routes...
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes...
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes...
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Pathway Management Routes
Route::group(['middleware' => 'auth'], function () {
    Route::resource('pathways', App\Http\Controllers\PathwayController::class);
    Route::resource('cases', App\Http\Controllers\PatientCaseController::class);
    Route::resource('reports', App\Http\Controllers\ReportController::class);
});

// Admin Routes
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('audit-logs', App\Http\Controllers\AuditLogController::class);
});

// Mutu Team Routes
Route::group(['middleware' => ['auth', 'role:mutu']], function () {
    Route::resource('pathways', App\Http\Controllers\PathwayController::class);
    Route::get('pathways/{pathway}/builder', [App\Http\Controllers\PathwayController::class, 'builder'])->name('pathways.builder');
});

// Klaim Team Routes
Route::group(['middleware' => ['auth', 'role:klaim']], function () {
    Route::resource('cases', App\Http\Controllers\PatientCaseController::class);
    Route::get('cases/upload', [App\Http\Controllers\PatientCaseController::class, 'showUploadForm'])->name('cases.upload');
    Route::post('cases/upload', [App\Http\Controllers\PatientCaseController::class, 'upload'])->name('cases.upload.process');
});

// Management Routes
Route::group(['middleware' => ['auth', 'role:manajemen']], function () {
    Route::resource('reports', App\Http\Controllers\ReportController::class);
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});
