<?php declare(strict_types=1);

use App\Controllers\AgendaController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\BaseController;
use EkiCal\foundation\Router\Route;
use App\Controllers\UserController;
use App\Controllers\PoneyController;


return [
    'index' => Route::get('/', [BaseController::class, 'index']),
    'user' => Route::get('/users', [UserController::class, 'index']),

    // Authentification

    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']),
    'login.form' => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connexion', [AuthController::class, 'login']),
    'logout' => Route::get('/deconnexion', [AuthController::class, 'logout']),

    // Espace membre

    'home' => Route::get('/compte', [HomeController::class, 'index']),
    'home.updateName' => Route::patch('/compte', [HomeController::class, 'updateName']),
    'home.updateEmail' => Route::patch('/compte/email', [HomeController::class, 'updateEmail']),
    'home.updatePassword' => Route::patch('/compte/password', [HomeController::class, 'updatePassword']),

    // Agenda

    'agenda' => Route::get('/agenda', [AgendaController::class, 'index']),

    // Poneys

    'poney' => Route::get('/poney', [PoneyController::class, 'index']),
];


