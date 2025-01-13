<?php declare(strict_types=1);

use App\Controllers\AgendaController;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
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
    'home.updatePicture' => Route::patch('/compte/picture', [HomeController::class, 'updatePicture']),
    'home.upload' => Route::post('/compte/upload', [HomeController::class, 'upload']),

    // Users
    'user' => Route::get('/users', [UserController::class, 'index']),
    'register.form' => Route::get('/inscription', [UserController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [UserController::class, 'register']),
    'users.edit' => Route::get('/edit/{slug}/modifier', [UserController::class, 'edit']),
    'users.editName' => Route::patch('/user/editName/{slug}', [UserController::class, 'editName']),
    'users.editEmail' => Route::patch('/user/editEmail/{slug}', [UserController::class, 'editEmail']),
    'users.editPassword' => Route::patch('/user/editPassword/{slug}', [UserController::class, 'editPassword']),
    'users.delete' => Route::delete('/delete/{slug}', [UserController::class, 'delete']),

    // Poneys

    'poney' => Route::get('/poney', [PoneyController::class, 'index']),
    'poney.form' => Route::get('/inscriptionPoney', [PoneyController::class, 'poneyForm']),
    'poney.request' => Route::post('/inscriptionPoney', [PoneyController::class, 'register']),
    'poneys.edit' => Route::get('/edit/{slug}/modifierPoney', [PoneyController::class, 'edit']),
    'poneys.editTpsw' => Route::patch('/poney/editTpsw/{slug}', [PoneyController::class, 'editTpsw']),
    'poneys.editWeight' => Route::patch('/poney/editWeight/{slug}', [PoneyController::class, 'editWeight']),
    'poneys.editMedical' => Route::patch('/poney/editMedical/{slug}', [PoneyController::class, 'editMedical']),
    'poneys.delete' => Route::delete('/deletePoney/{slug}', [PoneyController::class, 'delete']),
    'poney.upload' => Route::patch('/poney/upload/{slug}', [PoneyController::class, 'upload']),
    'poneys.editPedigree' => Route::patch('/poney/editPedigree/{slug}', [PoneyController::class, 'editPedigree']),
    'poneys.editName' => Route::patch('/poney/editName/{slug}', [PoneyController::class, 'editName']),
    'poneys.editBirth' => Route::patch('/poney/editBirth/{slug}', [PoneyController::class, 'editBirth']),

    // Clients

    'client' => Route::get('/client', [ClientController::class, 'index']),
    'clients.form' => Route::get('/inscriptionClient', [ClientController::class, 'clientForm']),
    'clients.request' => Route::post('/inscriptionClient', [ClientController::class, 'register']),
    'clients.edit' => Route::get('/edit/{slug}/modifierClient', [ClientController::class, 'edit']),
    'clients.delete' => Route::delete('/deleteClient/{slug}', [ClientController::class, 'delete']),
    'clients.editName' => Route::patch('/client/editName/{slug}', [ClientController::class, 'editName']),
    'clients.editEmail' => Route::patch('/client/editEmail/{slug}', [ClientController::class, 'editEmail']),
    'clients.editPhone' => Route::patch('/client/editPhone/{slug}', [ClientController::class, 'editPhone']),

    // Agenda

    'agenda' => Route::get('/agenda', [AgendaController::class, 'index']),

    // export Excell
    'sheet' => Route::get('/export', [UserController::class, 'export']),
    'poneysheet' => Route::get('/export', [PoneyController::class, 'export']),
    'clientsheet' => Route::get('/export', [ClientController::class, 'export']),
];


