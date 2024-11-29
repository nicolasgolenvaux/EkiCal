<?php declare(strict_types = 1);

namespace App\Controllers;

use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Authentication as Auth;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;

class HomeController extends AbstractController
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $user = Auth::get();

        View::render('home', [
            'user' => $user,
        ]);
    }

    public function updateName(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->name = $_POST['name'];
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre nom a été mis à jour !');
        $this->redirect('home');
    }

    public function updateEmail(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email', ['unique', 'email', 'users']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->email = $_POST['email'];
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre adresse e-mail a été mise à jour !');
        $this->redirect('home');
    }

    public function updatePassword(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'password_old' => ['required', 'password'],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre mot de passe a été mis à jour !');
        $this->redirect('home');
    }
}
