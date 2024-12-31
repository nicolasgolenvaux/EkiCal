<?php declare(strict_types=1);
/**
 * Ce Controller affiche la table des utilisateurs.
 */

namespace App\Controllers;


use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Authentication as Auth;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;
use EkiCal\foundation\Exceptions\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends AbstractController
{
    public function index(): void
    {
        $titre = 'Users';
        $user = User::select('id', 'name', 'email', 'role')->orderBy('id', 'desc')->get();
        View::render('users.user', compact('titre', 'user'));
    }

    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $user = User::where('id', $slug)->firstOrFail();
        $user->delete();
        $this->redirect('user');
    }

    public function registerForm(): void

    {
        View::render('users.register');
    }

    public function register(): void
    {

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'users']],
            'role' => ['required'],
            'password' => ['required', ['lengthMin', 2], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('register.form');
        }

        $user = User::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ]);

//        Auth::authenticate($user->id);
        $this->redirect('user');
    }

    public function edit($slug)
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        try {
            $user = User::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('users.edit', [
            'user' => $user,
        ]);
    }

    public function editName($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $user = User::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('users.edit', ['slug' => $user->id]);
        }

        $user->name = $_POST['name'];
        $user->save();

        Session::addFlash(Session::STATUS, 'Le nom a été mis à jour !');
        $this->redirect('users.edit', ['slug' => $user->id]);
    }

    public function editEmail($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $user = User::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email', ['unique', 'email', 'users']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('users.edit', ['slug' => $user->id]);
        }

        $user->email = $_POST['email'];
        $user->save();

        Session::addFlash(Session::STATUS, 'L\'adresse e-mail a été mise à jour !');
        $this->redirect('users.edit', ['slug' => $user->id]);
    }

    public function editPassword($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $user = User::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            $this->redirect('users.edit', ['slug' => $user->id]);
        }

        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();

        Session::addFlash(Session::STATUS, 'Le mot de passe a été mis à jour !');
        $this->redirect('users.edit', ['slug' => $user->id]);
    }

    public function export(): void
    {
        $user = User::select('id', 'name', 'email', 'role')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $i = 2;
        foreach ($user as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->email);
            $sheet->setCellValue('D' . $i, $item->role);
            $i++;
        }

// Préparer le fichier pour téléchargement
        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }
}
