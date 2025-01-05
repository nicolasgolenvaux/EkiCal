<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la page
 */
namespace App\Controllers;
use App\Models\Poney;
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
    public function index(): void
    {
        $titre = "Poneys";
        $poney = Poney::all();
        View::render('poneys.index',compact('titre','poney'));
    }
    public function poneyForm(): void

    {
        View::render('poneys.register');
    }

    public function register(): void
    {

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 2]],
            'tps_w' => ['required'],
            'weight' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('poney.form');
        }

        $poney = Poney::create([
            'name' => $_POST['name'],
            'tps_w' => $_POST['tps_w'],
            'weight' => $_POST['weight'],
            'image_path' => $_POST['image_path'],
            'birth' => $_POST['birth'],
            'medicalVisit' => $_POST['medicalVisit']
        ]);

        $this->redirect('poney');
    }
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $poney = Poney::where('id', $slug)->firstOrFail();
        $poney->delete();
        $this->redirect('poney');
    }

    public function edit($slug)
    {
        try {
            $poney = Poney::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('poneys.edit', ['poney' => $poney
        ]);
    }
    public function upload(): void
    {
        $uploadFile = "uploads/";
        if (!is_dir($uploadFile)) {
            mkdir($uploadFile, 0777, true);
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
                    $poney = Poney::get();
                    $poney->image_path = $destPath;
                    $poney->save();

                    Session::addFlash(Session::STATUS, 'Votre poney a été mis à jour !');
                    $this->redirect('poneys.edit');
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

        Session::addFlash(Session::STATUS, 'Le poids' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }

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

        Session::addFlash(Session::STATUS, 'La date de visite méeidcale de ' . $poney->name . ' a été mise à jour !');
        $this->redirect('poneys.edit', ['slug' => $poney->id]);
    }
    public function export(): void
    {
        $poney = Poney::select('id', 'name', 'tps_w', 'weight','birth','medicalVisit')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $i = 2;
        foreach ($poney as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->tps_w);
            $sheet->setCellValue('D' . $i, $item->weight);
            $sheet->setCellValue('E' . $i, $item->birth);
            $sheet->setCellValue('F' . $i, $item->medicalVisit);
            $i++;
        }

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }
}
