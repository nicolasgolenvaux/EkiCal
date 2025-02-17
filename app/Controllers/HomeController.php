<?php declare(strict_types=1);
/**
 * Cette classe gère toutes les fonctions d'identification de l'user.
 * La classe Auth vérifie s'il est connecté.
 * La classe session initialize les composants pour la classe Validator qui
 * fait les vérifications de conformité.
 * La classe View affiche les éléments.
 */
namespace App\Controllers;

use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Authentication as Auth;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class HomeController extends AbstractController
{
    /**Cette fonction vérifie la connection et ramène les informations
     * d'authentification de l'utilisateur.
     * Elle affiche la page 'home'.
     * @return void

     */
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

    /**Cette fonction met à jour le nom de l'utilisateur.
     * @return void
     */
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

    /**Cette fonction met à jour l'email' de l'utilisateur.
     * @return void
     */
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

    /**Cette fonction met à jour le mot de passe de l'utilisateur.
     * @return void
     */
    public function updatePassword(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'password_old'  => ['required', 'password'],
            'password'      => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre mot de passe a été mis à jour !');
        $this->sendEmail();
        $this->redirect('home');
    }

    /**Cette fonction permet d'envoyer un mail, mais ne fonctionne pas pour des raisons smtp...
     * @return void
     */
    public function sendEmail() :void
    {
        $debug = true;
        try {
            $mail = new PHPMailer($debug);
            if ($debug) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }

            // Authentification via SMTP
            $mail->isSMTP();true;
            // Connexion
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = "nicolasgolenvaux@gmail.com";
            $mail->Password = "xXwGyzCOIlMQwh8hWLvx";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->setFrom('nicolasgolenvaux@gmail.com', 'nom');
            $mail->addAddress('nicolasgolenvaux@gmail.com', 'nom');
            //$mail->addAttachment("/home/user/Desktop/image.png", "image.png");
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isHTML(true);
            $mail->Subject = 'Objet de votre email';
            $mail->Body = 'Le texte de votre email en HTML. Il est également possible des mettre des éléments en <b>gras</b>, par exemple.';
            $mail->AltBody = 'Le texte comme simple élément textuel';
            $mail->send();
        }catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: ".$e->getMessage();
        }
    }


    /**Cette fonction met à jour l'avatar de l'utilisateur.
     * @return void
     */
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

    /**Cette fonction vérifie et donne les droits d'accès pour enregistrer le fichier
     * dans l'application et injecter le chemin dans la base de donnée.
     * @return void
     */
    public function upload(): void  //→ Créer une règle Validator
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
