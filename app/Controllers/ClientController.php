<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la page des clients.
 */

namespace App\Controllers;
use App\Models\Agenda;
use App\Models\Client;
use App\Models\Invoice;
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

class ClientController extends AbstractController
{
    /** Cette fonction affiche la liste des clients.
     * @return void
     */
    public function index(): void
    {
        $titre = "Clients";
        $client = Client::select('id','name','email','phone','tva')->orderBy('name','ASC')->get();
        View::render('client.index', compact('titre', 'client'));
    }

    /**Cette fonction affiche le formulaire d'enregistrement d'un nouveau client.
     * @return void
     */
    public function clientForm(): void

    {
        View::render('client.register');
    }

    /**Cette fonction permet d'nergistrer un nouveau client dans la table clients.
     * @return void
     */
    public function register(): void
    {

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'clients']],
            'phone' => ['required']
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('clients.form');
        }

        $client = Client::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ]);

        $this->redirect('client');
    }

    /**Cette fonction permet d'effacer un client de la table clients.
     * @param $slug
     * @return void
     */
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $client->delete();
        $this->redirect('client');
    }

    /** Cette fonction affiche le formulaire de modification d'un client.
     * @param $slug
     * @return void
     */
    public function edit($slug)
    {
        try {
            $client = Client::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('client.edit', [
            'client' => $client
        ]);
    }

    /**Cette fonction permet de modifier le nom d'un client.
     * @param $slug
     * @return void
     */
    public function editName($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('clients.edit', ['slug' => $client->id]);
        }
        $client->name = $_POST['name'];
        $client->save();

        Session::addFlash(Session::STATUS, 'Le nom de ' . $client->name . ' a été mis à jour !');
        $this->redirect('clients.edit', ['slug' => $client->id]);
    }

    /**Cette fonction permet de modifier l'email' d'un client.
     * @param $slug
     * @return void
     */
    public function editEmail($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('clients.edit', ['slug' => $client->id]);
        }
        $client->email = $_POST['email'];
        $client->save();

        Session::addFlash(Session::STATUS, 'L\'email de ' . $client->name . ' a été mis à jour !');
        $this->redirect('clients.edit', ['slug' => $client->id]);
    }

    /**Cette fonction permet de modifier le téléphone d'un client.
     * @param $slug
     * @return void
     */
    public function editPhone($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'phone' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('clients.edit', ['slug' => $client->id]);
        }
        $client->phone = $_POST['phone'];
        $client->save();

        Session::addFlash(Session::STATUS, 'Le téléphone de ' . $client->name . ' a été mis à jour !');
        $this->redirect('clients.edit', ['slug' => $client->id]);
    }

    /**Cette fonction permet de modifier le numéro de tva d'un client.
     * @param $slug
     * @return void
     */
    public function editTVA($slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'tva' => ['required'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('clients.edit', ['slug' => $client->id]);
        }
        $client->tva = $_POST['tva'];
        $client->save();

        Session::addFlash(Session::STATUS, 'Le numéro d\'entreprise de ' . $client->name . ' a été mis à jour !');
        $this->redirect('clients.edit', ['slug' => $client->id]);
    }

    /**Cette fonction permet d'exporter la liste des clients'.
     * @return void
     */
    public function export(): void
    {
        $client = Client::select('id', 'name', 'email', 'phone','tva')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="client_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A' . 1,'n° id');
        $sheet->setCellValue('B' . 1,'Nom');
        $sheet->setCellValue('C' . 1,'E-mail');
        $sheet->setCellValue('D' . 1,'Téléphone');
        $sheet->setCellValue('E' . 1,'Tva');

        $i = 3;
        foreach ($client as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->email);
            $sheet->setCellValue('D' . $i, $item->phone);
            $sheet->setCellValue('E' . $i, $item->tva);
            $i++;
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }

    /**Cette méthode crée une entrée dans la table invoices avec les informations nécessaires à la facturation.
     *Elle efface automatiquement le rendez-vous de la table agendas. Elle redirige vers la page d'affichage facturation
     * @param $slug
     * @return void
     */
    public function invoiceGenerate($slug): void
    {
        $agenda=Agenda::where('client_id', $slug)->get();
        $client=Client::where('id', $slug)->first();
        $poneys = PoneyChoice::all();
        $total = $agenda->sum('prix');
        $htva = $total * 0.79;
        $tva = $total * 0.21;

        if (empty($agenda)) {
            echo 'Il n y a pas de rendez-vous a facturer à ' . $client->name . '!';
            $this->redirect('client');
        } else {
            for ($i = 0; $i < count($agenda); $i++) {
                $invoice = Invoice::create([
                    'id' => $agenda[0]->id . date('Ymd'),
                    'name' => $client->name,
                    'email' => $client->email,
                    'jour' => $agenda[$i]->jour,
                    'heure' => $agenda[$i]->start,
                    'nbr' => $agenda[$i]->nbr,
                    'facturation_type' => $agenda[$i]->facturation_type,
                    'prix' => $agenda[$i]->prix,
                    'qt' => $agenda->count(),
                    'total' => $total,
                    'htva' => $htva,
                    'tva' => $tva,
                ]);

                Agenda::where('client_id', $slug)->delete();
            }
        }
        View::render('client.invoice',compact('agenda','client','poneys','total','htva','tva'));
    }
}