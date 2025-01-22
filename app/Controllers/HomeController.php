<?php declare(strict_types=1);

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

    public function updatePicture(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_FILES);
        $validator->mapFieldsRules([
            'picture' => ['required_file', 'image', 'square']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $image = $_FILES['picture'];

        if ($image['error'] !== UPLOAD_ERR_OK) {
            Session::addFlash(Session::ERRORS, ['picture' => ['Échec du téléchargement de l\'image.']]);
            $this->redirect('home');
        }

        $imageData = file_get_contents($image['tmp_name']);

        $user = Auth::get();
        $user->picture = $imageData;
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre avatar a été mis à jour !');
        $this->redirect('home');
    }

    public function upload(): void
    {
        $uploadFile = "uploads/"; // Dossier où les avatars seront sauvegardés
        if (!is_dir($uploadFile)) {
            mkdir($uploadFile, 0777, true);// s'il n'existe pas, on le crée avec les permissions.
        }

        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['picture']['tmp_name'];
            $fileName = basename($_FILES['picture']['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Validation des extensions
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                $newFileName = uniqid() . '.' . $fileExtension;
                $destPath = $uploadFile . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $user = Auth::get();
                    $user->image_path = $destPath;
                    $user->save();

                    Session::addFlash(Session::STATUS, 'Votre avatar a été mis à jour !');
                    $this->redirect('home');
                } else {
                    echo "Erreur lors du déplacement du fichier.";
                }
            } else {
                echo "Type de fichier non supporté.";
            }
        } else {
            echo "Aucun fichier téléchargé ou une erreur s'est produite.";
        }

    }
}
