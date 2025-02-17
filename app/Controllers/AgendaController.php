<?php declare(strict_types = 1);

namespace App\Controllers;
use App\Models\Agenda;
use App\Models\Client;
use App\Models\Hours;
use App\Models\Poney;
use App\Models\PoneyChoice;
use DateTime;
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
    /** Cette méthode affiche la liste des rendez-vous du jour actuel. Elle ramène les champs de la table
     * poneyChoice, agendas et le nom et l'id des clients.
     * @return void
     */
    public function index(): void
    {
        $titre  = 'Agenda';
        $agenda = Agenda::all();
        $day = $_GET['day'] ?? date('Y-m-d');
        try {
            $date = new DateTime($day);
        } catch (Exception $e) {
            $date = new DateTime(); // En cas d'erreur, on prend la date du jour
        }
        $previous_day = clone $date;
        $previous_day->modify('-1 day');
        $next_day = clone $date;
        $next_day->modify('+1 day');
        $client  = Client::select('id', 'name')->get();
        $poneys = PoneyChoice::all();

        View::render('agendas.index',  [
            'day'           => $date->format('Y-m-d'),
            'previous_day'  => $previous_day->format('Y-m-d'),
            'next_day'      => $next_day->format('Y-m-d'),
            'titre'         => $titre,
            'poneys'        => $poneys,
            'clients'       => $client,
            'agendas'       => $agenda,
            ]);
    }

    /**Cette méthode crée un rendez-vous dans la table agendas. Le prix est calculé en fonction du type de facturation.
     * @return void
     */
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
        if ($_POST['facturation_type'] == 'acte') {
            $prix = $_POST['nbr'] * 50;
        }else{
            $prix = 100;
        }
        $agenda = Agenda::create([
            'jour'                   => $_POST['jour'],
            'start'                  => $_POST['start'],
            'client_id'              => $_POST['client_id'],
            'type'                   => $_POST['type'],
            'nbr'                    => $_POST['nbr'],
            'facturation_type'       => $_POST['facturation_type'],
            'prix'                   => $prix,

        ]);

        $this->redirect('agenda',compact('agendas'));
    }

    /** Cette méthode affiche la page formulaire d'enregistrement d'un rendez-vous. Elle affiche les rendez-vous
     * de la journée.
     * @return void
     */
    public function agendaForm(): void
    {
        $agenda  = Agenda::select('jour','start','id','client_id','type','nbr')->orderBy('start','ASC')->get();
        $session = Hours::all();
        $client  = Client::select('id', 'name')->get();

        View::render('agendas.register', compact('client', 'agenda','session'));

    }

    /**Cette méthode permet d'afficher la page formulaire d'édition d'un rendez-vous. Elle donne les informations du
     * client liées au rendez-vous et les heures de travail de chaque poney.
     *
     * @param $slug
     * @return void
     */
    public function edit($slug): void
    {
        $agenda = Agenda::with(['poneyChoosen', 'client'])->find($slug);
        $poneys = Poney::select('name', 'id', 'tps_w', 'image_path')->get();
        $poneysChoice = $agenda->poneyChoosen->pluck('poney_id');
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


    /**Cette méthode permet de modifier l'heure de début d'un rendez-vous.
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet de modifier le nombre de participants au rendez-vous. Elle calcule
     * le prix en fonction de celui-ci.
     * @param $slug
     * @return void
     */
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
        if( $agenda->facturation_type == 'acte' ) {
        $agenda->prix= $_POST['nbr']*50;
        }else{
            $agenda->prix= 100;
        }
        $agenda->save();

        Session::addFlash(Session::STATUS, 'Le nombre de participant a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
    }

    /**Cette méthode permet de modifier le type de participant lié au rendez-vous.
     * @param $slug
     * @return void
     */
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

    /**Cette méthode permet de modifier le type de facturation du rendez-vous.
     * @param $slug
     * @return void
     */
    public function editFact($slug): void
    {
        $agenda    = Agenda::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'facturation_type' => ['required'],
            'nbr'  => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agendas.edit', ['slug' => $agenda->id]);
        }

        $agenda->facturation_type= $_POST['facturation_type'];
        if( $agenda->facturation_type == 'acte' ) {
            $agenda->prix= $_POST['nbr']*50;
        }else{
            $agenda->prix= 100;
        }
        $agenda->save();

        Session::addFlash(Session::STATUS, 'Le type de facturation a été mis à jour !');
        $this->redirect('agendas.edit', ['slug' => $agenda->id]);
    }

    /**Cette méthode permet d'effacer un rendez-vous.
     * @param $slug
     * @return void
     */
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $user = Agenda::where('id', $slug)->firstOrFail();
        $user->delete();
        $this->redirect('agenda');
    }

    /**Cette méthode permet d'exporter la liste des rendez-vous actifs.
     * @return void
     */
    public function exportAgenda(): void
    {
        $agenda = Agenda::select('id', 'jour', 'start','client_id','type','nbr')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="agenda_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A' . 1,'n° id');
        $sheet->setCellValue('B' . 1,'Jour');
        $sheet->setCellValue('C' . 1,'Début');
        $sheet->setCellValue('D' . 1,'n° Client');
        $sheet->setCellValue('E' . 1,'Type');
        $sheet->setCellValue('F' . 1,'Participant');

        $i = 3;
        foreach ($agenda as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->jour);
            $sheet->setCellValue('C' . $i, $item->start);
            $sheet->setCellValue('D' . $i, $item->client_id);
            $sheet->setCellValue('E' . $i, $item->type);
            $sheet->setCellValue('F' . $i, $item->nbr);
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
        }
        catch (Exception $e)
        {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }

    /**Cette méthode permet d'effacer un poney lié au rendez-vous.
     * @return void
     */
    public function deletePoneyAgenda():void
    {
        $agenda_id = $_POST['agenda_id'];
        $poney_id  = $_POST['poney_id'];
        $poney = PoneyChoice::where('agenda_id', $agenda_id)->where('poney_id', $poney_id);
        $poney->delete();
        $this->redirect('agendas.edit', ['slug' => $agenda_id]);
    }


}
