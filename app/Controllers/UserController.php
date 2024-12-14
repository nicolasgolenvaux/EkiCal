<?php declare(strict_types=1);
/**
 * Ce Controller affiche la table des utilisateurs.
 */

namespace App\Controllers;

use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;

class UserController extends AbstractController
{
    public function index(): void
    {
        $titre = 'Utilisateurs';
        $users = User::select('id','name','email','role') ->orderBy('id', 'asc')->get();
        View::render('users.user',compact('titre','users'));
    }
}
