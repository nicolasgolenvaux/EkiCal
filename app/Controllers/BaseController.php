<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la première page de l'application.
 * Elle ne fait qu'afficher la page index.html
 */
namespace App\Controllers;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;

class BaseController extends AbstractController
{
    public function index(): void
    {
        View::render('index');
    }
}
