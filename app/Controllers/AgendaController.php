<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la
 */
namespace App\Controllers;
use App\Models\Agenda;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;

class AgendaController extends AbstractController
{
    public function index(): void
    {
        $titre = 'Agenda';
        View::render('agendas.index',compact('titre'));
    }
}
