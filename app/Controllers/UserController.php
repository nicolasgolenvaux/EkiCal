<?php declare(strict_types=1);
/**
 * Ce Controller manipule la table des utilisateurs.
 */

namespace App\Controllers;

use Exception;
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
    /**Cette fonction affiche la page avec la liste des users.
     * @return void
     */
    public function index(): void
    {
        $titre = 'Users';
        $user = User::select('id', 'name', 'email', 'role', 'image_path')->orderBy('id', 'desc')->get();
        View::render('users.user', compact('titre', 'user'));

    }

    /**Cette fonction permet d'effacer un user de la table users.
     * @param $slug
     * @return void
     */
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $user = User::where('id', $slug)->firstOrFail();
        $user->delete();
        $this->redirect('user');
    }

    /**Cette méthode permet d'afficher le formulaire d'enregistrement d'un nouvel user.
     * @return void
     */
    public function registerForm(): void

    {
        View::render('users.register');
    }

    /**Cette méthode permet d'enregistrer un nouvel user dans la table users.
     * @return void
     */
    public function register(): void
    {

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'users']],
            'role' => ['required'],
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
            'role' => $_POST['role'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ]);

        $this->redirect('user');
    }

    /**Cette méthode permet d'afficher le formulaire d'édition de l'user.
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet de mettre à jour le nom de l'user
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet de mettre à jour le mail de l'user
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet de mettre à jour le mot de passe de l'user.
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet d'exporter en xlsx la table users.
     * @return void
     */
    public function export(): void
    {
        $user = User::select('id', 'name', 'email', 'role')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A' . 1,'n° id');
        $sheet->setCellValue('B' . 1,'Nom');
        $sheet->setCellValue('C' . 1,'E-mail');
        $sheet->setCellValue('D' . 1,'Role');

        $i = 3;
        foreach ($user as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->email);
            $sheet->setCellValue('D' . $i, $item->role);
            $i++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }
}
