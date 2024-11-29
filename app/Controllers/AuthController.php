<?php declare(strict_types = 1);
/**
 * Ce Controller gère toutes les méthodes liées à la table 'users'.
 * Elle utilise la classe Authentication qui contient les méthodes
 * pour vérifier si un visiteur est authentifié ou pas.
 * Elle utilise la classe Session pour intialiser une session et gérer les
 * variables de session 'flash'.
 * La classe validator va gérer la conformité des formulaires
 */
namespace App\Controllers;

use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Authentication as Auth;//pour avoir moins à écrire :)
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;

class AuthController extends AbstractController
{
    //On lie le formulaire d'inscription à une uri
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }
        //header('Location:./resources/views/auth/register.php');
        View::render('auth.register');
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'users']],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('register.form');
        }

        $user = User::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ]);

        Auth::authenticate($user->id);
        $this->redirect('home');
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        View::render('auth.login');
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->validate() && Auth::verify($_POST['email'], $_POST['password'])) {
            $user = User::where('email', $_POST['email'])->first();
            Auth::authenticate($user->id);
            $this->redirect('home');
        }

        Session::addFlash(Session::ERRORS, ['Identifiants erronés']);
        Session::addFlash(Session::OLD, $_POST);
        $this->redirect('login.form');
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $this->redirect('login.form');
    }
}
