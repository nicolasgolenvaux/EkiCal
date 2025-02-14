<?php declare(strict_types = 1);
/**
 * Ce Controller gère toutes les méthodes liées à la table 'users'.
 * Elle utilise la classe Authentication qui contient les méthodes
 * qui vérifient si un visiteur est authentifié ou pas.
 * Elle utilise la classe Session pour initializer une session et gérer les
 * variables de session 'flash'.
 * La classe validator va gérer la conformité des formulaires.
 */
namespace App\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PoneyChoice;
use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;


class InvoiceController extends AbstractController
{
    public function index(): void
    {
        $titre  = 'Facturation';
        $invoices = Invoice::select('id','name','total','updated_at')->orderBy('id')->groupBy('id')->get();
        View::render('invoices.index', compact('titre', 'invoices'));
    }

}
