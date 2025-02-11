<?php declare(strict_types = 1);

namespace App\Controllers;
use App\Models\Agenda;
use App\Models\Client;
use App\Models\Hours;
use App\Models\Poney;
use App\Models\PoneyChoice;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;
class AgendaController extends AbstractController
{
    public function index(): void
    {
        $titre  = 'Agenda';
        $agenda = Agenda::all();
        $day    = date('Y-m-d');
        $client  = Client::select('id', 'name')->get();
        $poneys = PoneyChoice::all();
        View::render('agendas.index', compact('titre', 'agenda','day','client','poneys'));

    }

    public function register(): void
    {
        $agendas = Agenda::all();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'jour'                    => ['required'],
            'start'                   => ['required',['checkAvailable',['jour','start']]],
            'client_id'               => ['required'],
            'type'                    => ['required'],
            'nbr'                     => ['required'],
            'facturation_type'        => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.form');
        }

        $agenda = Agenda::create([
            'jour'                   => $_POST['jour'],
            'start'                  => $_POST['start'],
            'client_id'              => $_POST['client_id'],
            'type'                   => $_POST['type'],
            'nbr'                    => $_POST['nbr'],
            'facturation_type'       => $_POST['facturation_type'],
        ]);


        $this->redirect('agenda',compact('agendas'));
    }
    public function agendaForm(): void
    {
        $agenda  = Agenda::select('jour','start','id','client_id','type','nbr')->orderBy('start','ASC')->get();
        $session = Hours::all();
        $client  = Client::select('id', 'name')->get();

        View::render('agendas.register', compact('client', 'agenda','session'));

    }

    public function edit($slug)
    {
        //$agenda       = Agenda::find($slug);
        $agenda = Agenda::with(['poneyChoosen', 'client'])->find($slug);
        //$poneys       = Poney::select('name', 'id','tps_w','image_path')->get();
        $poneys = Poney::select('name', 'id', 'tps_w', 'image_path')->get();
        //$poneysChoice = $agenda->poneyChoosen;
        $poneysChoice = $agenda->poneyChoosen->pluck('poney_id');
        //$poneysChoice = $poneysChoice->pluck('poney_id');
        //$poneyName    = Poney::find($poneysChoice);
        $poneyName = Poney::whereIn('id', $poneysChoice)->get();
        $client       = $agenda->client->name;
        $counts = PoneyChoice::selectRaw('COUNT(poneyChoice.poney_id) as total, poney_id')
            ->join('agendas', 'poneyChoice.agenda_id', '=', 'agendas.id')
            ->whereDate('agendas.jour',date('Y-m-d'))
            ->groupBy('poney_id')
            ->get();

        View::render('agendas.edit', [
            'agendas'      => $agenda,
            'poneys'       => $poneys,
            'poneysChoice' => $poneysChoice,
            'poneyName'    => $poneyName,
            'client'       => $client,
            'counts'       => $counts

        ]);
    }


    public function editStart($slug): void
    {
        $agenda     = Agenda::where('id', $slug)->firstOrFail();

        $validator  = Validator::get($_POST);
        $validator->mapFieldsRules([
            'start' => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.edit', ['slug' => $agenda->id]);
        }

        $agenda->start = $_POST['start'];
        $agenda->save();

        Session::addFlash(Session::STATUS, 'Le début a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
    }

    public function editNbr($slug): void
    {
        $agenda    = Agenda::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'nbr'  => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.edit', ['slug' => $agenda->id]);
        }

        $agenda->nbr= $_POST['nbr'];
        $agenda->save();

        Session::addFlash(Session::STATUS, 'Le nombre de participant a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
    }
    public function editType($slug): void
    {
        $agenda    = Agenda::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'type' => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.edit', ['slug' => $agenda->id]);
        }

        $agenda->type= $_POST['type'];
        $agenda->save();

        Session::addFlash(Session::STATUS, 'Le type du participant a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
    }

    public function editFact($slug): void
    {
        $agenda    = Agenda::where('id', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'facturation_type' => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.edit', ['slug' => $agenda->id]);
        }

        $agenda->facturation_type= $_POST['facturation_type'];
        $agenda->save();
        Session::addFlash(Session::STATUS, 'Le type de facturation a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
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
        $user = Agenda::select('id', 'jour', 'start','client_id','type','nbr')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $i = 2;
        foreach ($user as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->jour);
            $sheet->setCellValue('C' . $i, $item->start);
            $sheet->setCellValue('D' . $i, $item->client_id);
            $sheet->setCellValue('E' . $i, $item->type);
            $sheet->setCellValue('F' . $i, $item->nbr);
            $i++;
        }

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        }
        catch (Exception $e)
        {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }
    public function deletePoneyAgenda():void
    {
        $agenda_id = $_POST['agenda_id'];
        $poney_id  = $_POST['poney_id'];
        $poney     = PoneyChoice::where('agenda_id', $agenda_id)->where('poney_id', $poney_id);
        $poney->delete();
        $this->redirect('agendas.edit', ['slug' => $agenda_id]);
    }


}
