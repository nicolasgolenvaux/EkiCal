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

    // Authentification

    'login.form' => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connexion', [AuthController::class, 'login']),
    'logout' => Route::get('/deconnexion', [AuthController::class, 'logout']),

    // Espace membre

    'home' => Route::get('/compte', [HomeController::class, 'index']),
    'home.updateName' => Route::patch('/compte', [HomeController::class, 'updateName']),
    'home.updateEmail' => Route::patch('/compte/email', [HomeController::class, 'updateEmail']),
    'home.updatePassword' => Route::patch('/compte/password', [HomeController::class, 'updatePassword']),

    // Users
    'user' => Route::get('/users', [UserController::class, 'index']),
    'register.form' => Route::get('/inscription', [UserController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [UserController::class, 'register']),
    'users.edit' => Route::get('/edit/{slug}/modifier', [UserController::class, 'edit']),
    'users.editName' => Route::patch('/user/editName/{slug}', [UserController::class, 'editName']),
    'users.editEmail' => Route::patch('/user/editEmail/{slug}', [UserController::class, 'editEmail']),
    'users.editPassword' => Route::patch('/user/editPassword/{slug}', [UserController::class, 'editPassword']),
    'users.delete' => Route::delete('/delete/{slug}', [UserController::class, 'delete']),
    // Agenda

    'agenda' => Route::get('/agenda', [AgendaController::class, 'index']),

    // Poneys

    'poney' => Route::get('/poney', [PoneyController::class, 'index']),

    // export Excell
    'sheet' => Route::get('/export', [userController::class, 'export']),
];


