<?php declare(strict_types=1);

/**
 * Ce fichier ramène un tableau de routes utilisées par les Controllers
 */
use App\Controllers\AgendaController;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\HomeController;
use App\Controllers\BaseController;
use App\Controllers\InvoiceController;
use App\Controllers\PoneyChoiceController;
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
    'clients.editTVA' => Route::patch('client/editTVA/{slug}', [ClientController::class, 'editTVA']),
    'clients.invoice' => Route::get('client/invoice/{slug}',[ClientController::class, 'invoiceGenerate']),

    // Agenda

    'agenda' => Route::get('/agenda', [AgendaController::class, 'index']),
    'agendas.form' => Route::get('/inscriptionRdv', [AgendaController::class, 'agendaForm']),
    'agendas.request' => Route::post('/inscriptionRdv', [AgendaController::class, 'register']),
    'agendas.edit' => Route::get('/edit/{slug}/modifierAgenda', [AgendaController::class, 'edit']),
    'agendas.delete' => Route::delete('/deleteAgenda/{slug}', [AgendaController::class, 'delete']),
    'agendas.editStart' => Route::patch('/agenda/editStart/{slug}', [AgendaController::class, 'editStart']),
    'agendas.editNbr' => Route::patch('/agenda/editNbr/{slug}', [AgendaController::class, 'editNbr']),
    'agendas.editType' => Route::patch('/agenda/editType/{slug}', [AgendaController::class, 'editType']),
    'agendas.editFact' => Route::patch('/agenda/editFact/{slug}', [AgendaController::class, 'editFact']),
    'poneyAgenda.delete' => Route::delete('/deletePoneyAgenda', [AgendaController::class, 'deletePoneyAgenda']),

    // PoneyChoice

    'registerPoneyChoice' => Route::post('/registerPoneyChoice', [PoneyChoiceController::class, 'registerPoneyChoice']),

    // Invoices
    'invoices' => Route::get('/invoices', [InvoiceController::class, 'index']),
    'invoicesDate' => Route::post('/invoices', [InvoiceController::class, 'index']),
    'invoices.delete' => Route::delete('/deleteInvoice/{slug}', [InvoiceController::class, 'delete']),
    'invoices.show' => Route::get('/showInvoice/{slug}', [InvoiceController::class, 'show']),
    'invoices.detailInvoice' => Route::post('/detailsInvoice',[InvoiceController::class, 'detailInvoice']),
    //'invoices.history' => Route::post('/historyInvoice',[InvoiceController::class, 'historyInvoice']),


    // export Excell
    'invoicesheet' => Route::get('/exportInvoices', [InvoiceController::class, 'exportInvoices']),
    'sheet' => Route::get('/export', [UserController::class, 'export']),
    'poneysheet' => Route::get('/exportPoney', [PoneyController::class, 'export']),
    'clientsheet' => Route::get('/exportClient', [ClientController::class, 'export']),
    'rdvsheet' => Route::get('/exportAgenda', [AgendaController::class, 'exportAgenda']),

];


