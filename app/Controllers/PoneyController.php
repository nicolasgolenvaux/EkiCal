<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la page
 */
namespace App\Controllers;
use App\Models\Poney;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;

class PoneyController extends AbstractController
{
    public function index(): void
    {
        $titre = "Poneys";
        View::render('poneys.index',compact('titre'));
    }
}
