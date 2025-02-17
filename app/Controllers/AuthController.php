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

use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Authentication as Auth; //pour avoir moins à écrire :)
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;

class AuthController extends AbstractController
{
    /**Cette fonction redirige l'utilisateur vers la page de connexion. S'il est déjà
     * connecté, il est redirigé vers la page de son compte.
     * @return void
     */
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }
        View::render('auth.login');
    }

    /**Cette fonction redirige l'utilisateur vers sa page 'compte' s'il est déjà
     * identifié. Sinon, on vérifie la validité des champs, la concordance avec son mot de passe et
     * son mail, ensuite, il est redirigé vers la page 'agenda'.
     * @return void
     */
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email'     => ['required', 'email'],
            'password'  => ['required'],
        ]);

        if ($validator->validate() && Auth::verify($_POST['email'], $_POST['password'])) {
            $user = User::where('email', $_POST['email'])->first();
            Auth::authenticate($user->id);
            $this->redirect('agenda');
        }

        Session::addFlash(Session::ERRORS, ['Identifiants erronés']);
        Session::addFlash(Session::OLD, $_POST);
        $this->redirect('login.form');
    }

    /**Cette fonction vérifie si la variable d'environnement existe et supprime la variable
     * de session SESSION_ID via la méthode 'logout".
     * @return void
     */
    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $this->redirect('login.form');
    }

}
