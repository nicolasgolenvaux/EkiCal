<?php declare(strict_types = 1);

namespace App\Controllers;

use App\Models\Poney;
use App\Models\PoneyChoice;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Exceptions\HttpException;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use EkiCal\foundation\Authentication as Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PoneyController extends AbstractController
{
    /**Cette fonction affiche la page index et les informations des poneys.
     * @return void
     */
    public function index(): void
    {
        $titre = "Poneys";
        $poney = Poney::all();
        $counts = PoneyChoice::selectRaw('COUNT(poneyChoice.poney_id) as total, poney_id')
            ->join('agendas', 'poneyChoice.agenda_id', '=', 'agendas.id')
            ->whereDate('agendas.jour',date('Y-m-d'))
            ->groupBy('poney_id')
            ->get();

        View::render('poneys.index', compact('titre', 'poney','counts'));
    }

    /**Cette fonction affiche la page d'enregistrement d'un poney
     * @return void
     */
    public function poneyForm(): void
    {
        View::render('poneys.register');
    }

    /**Cette fonction enregistre les données du poney dans la table poneys.
     * @return void
     */
    public function register(): void
    {
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name'      => ['required', ['lengthMin', 2]],
            'tps_w'     => ['integer', ['min', 0], ['max', 7]],
            'weight'    => ['integer', ['min', 50], ['max', 500]],
            'birth'     => ['date', ['dateAfter', '2000-01-01']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poney.form');
        }

        $poney = Poney::create([
            'name'          => $_POST['name'],
            'tps_w'         => $_POST['tps_w'],
            'weight'        => $_POST['weight'],
            'birth'         => $_POST['birth'],
            'medicalVisit'  => $_POST['medicalVisit']
        ]);

        $this->redirect('poney');
    }

    /**Cette fonction efface le poney de la base donnée.
     * @param $slug : Détermine l'id du poney.
     * @return void
     */
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $poney = Poney::where('id', $slug)->firstOrFail();
        $poney->delete();
        $this->redirect('poney');
    }


    /**Cette fonction recherche et affiche les informations du poney.
     * @param $slug : Détermine l'id du poney.
     * @return void
     */
    public function edit($slug)
    {
        try {
            $poney = Poney::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('poneys.edit', [
            'poney' => $poney
        ]);
    }

    /**Cette fonction télécharge la photo du poney.
     * @param $slug
     * @return void
     */
    public function upload($slug): void
    {
        $uploadFile = "uploads/";
        if (!is_dir($uploadFile)) {
            mkdir($uploadFile, 0777, true);
        }
        $validator = Validator::get($_POST + $_FILES);
        $validator->mapFieldsRules([
            'file' => ['required_file', 'image', 'square'],
        ]);

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
                    $poney = Poney::where('id', $slug)->firstOrFail();
                    $poney->image_path = $destPath;
                    $poney->save();

                    Session::addFlash(Session::STATUS, 'La photo de votre poney a été mis à jour !');
                    $this->redirect('poneys.edit', ['slug' => $poney->id]);
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

    /**Cette fonction permet de modifier le poids d'un cheval.
     * @param $slug
     * @return void
     */
    public function editWeight($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'weight' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->weight = $_POST['weight'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'Le poids de ' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**Cette fonction permet de modifier le temps de travail journalier d'un cheval.
     * @param $slug
     * @return void
     */
    public function editTpsw($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'tps_w' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->tps_w = $_POST['tps_w'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'Le temps de travail de ' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**Cette fonction permet de modifier la date de la visite médicale d'un cheval.
     * @param $slug
     * @return void
     */
    public function editMedical($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'medicalVisit' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->medicalVisit = $_POST['medicalVisit'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'La date de la visite médicale de ' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**Cette fonction permet de modifier la date de naissance d'un cheval.
     * @param $slug
     * @return void
     */
    public function editBirth($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'birth' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->birth = $_POST['birth'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'La date de naissance de ' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**Cette fonction permet de modifier le pédigréed'un cheval.
     * @param $slug
     * @return void
     */
    public function editPedigree($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'pedigree' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->pedigree = $_POST['pedigree'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'Le pedigree de ' . $poney->name . ' a été mis à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**Cette fonction permet de modifier le nom d'un cheval.
     * @param $slug
     * @return void
     */
    public function editName($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poneys.edit', ['slug' => $poney->id]);
        }
        $poney->name = $_POST['name'];
        $poney->save();

        Session::addFlash(Session::STATUS, 'Le nom de ' . $poney->name . ' a été mis à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

    /**
     * @return void
     */
    public function export(): void
    {
        $poney = Poney::select('id', 'name', 'tps_w', 'weight', 'birth', 'medicalVisit')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="poneys_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A' . 1,'n° id');
        $sheet->setCellValue('B' . 1,'Nom');
        $sheet->setCellValue('C' . 1,'Temps de travail');
        $sheet->setCellValue('D' . 1,'Poids');
        $sheet->setCellValue('E' . 1,'Date de naissance');
        $sheet->setCellValue('F' . 1,'visite médicale');

        $i = 3;
        foreach ($poney as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->tps_w);
            $sheet->setCellValue('D' . $i, $item->weight);
            $sheet->setCellValue('E' . $i, $item->birth);
            $sheet->setCellValue('F' . $i, $item->medicalVisit);
            $i++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }

}
