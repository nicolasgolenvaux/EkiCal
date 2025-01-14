<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la
 */
namespace App\Controllers;
use App\Models\Agenda;
use App\Models\Client;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Exceptions\HttpException;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;
use EkiCal\foundation\View;
use Exception;
use EkiCal\foundation\Authentication as Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AgendaController extends AbstractController
{
    public function index(): void
    {
        $titre = 'Agenda';
        $agenda = Agenda::all();
        $day = date('Y-m-d');
        View::render('agendas.index', compact('titre', 'agenda','day'));
    }


    public function register(): void
    {


        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'jour' => ['required'],
            'start' => ['required'],
            'end' => ['required'],
            'client_id' => ['required'],
            'type' => ['required'],
            'nbr' => ['required'],

        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.form');
        }

        $user = Agenda::create([
            'jour' => $_POST['jour'],
            'start' => $_POST['start'],
            'end' => $_POST['end'],
            'client_id' => $_POST['client_id'],
            'type' => $_POST['type'],
            'nbr' => $_POST['nbr'],

        ]);

        $this->redirect('agenda');
    }
    public function agendaForm(): void

    {
        $client = Client::select('id', 'name')->get();
        View::render('agendas.register', compact('client'));
    }
    public function edit($slug)
    {
        try {
            $rdv = Agenda::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('agendas.edit', [
            'agendas' => $rdv,
        ]);
    }
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $user = Agenda::where('id', $slug)->firstOrFail();
        $user->delete();
        $this->redirect('agenda');
    }


    public function export(): void
    {
        $user = Agenda::select('id', 'jour', 'start', 'end','client_id','type','nbr')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $i = 2;
        foreach ($user as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->jour);
            $sheet->setCellValue('C' . $i, $item->start);
            $sheet->setCellValue('D' . $i, $item->end);
            $sheet->setCellValue('E' . $i, $item->client_id);
            $sheet->setCellValue('F' . $i, $item->type);
            $sheet->setCellValue('G' . $i, $item->nbr);
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
