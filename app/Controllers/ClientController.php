<?php declare(strict_types = 1);
/**
 * Ce Controller affiche la page
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
    public function index(): void
    {
        $titre = "Clients";
        $client = Client::select('id','name','email','phone','tva')->orderBy('name','ASC')->get();
        View::render('client.index', compact('titre', 'client'));
    }
    public function clientForm(): void

    {
        View::render('client.register');
    }
    public function clientSearch($keywords): void

    {
        $name = Client::select('name')->get();
        $keywords=$_GET['keywords'];
        $valider=$_GET['valider'];
        if(isset($valider) && !empty(trim($keywords))){
            $res = $name->where('name', 'like', '%' . $keywords . '%')->get();
            echo $res;
        }
        else{
            echo 'aucun client trouvé.';
        }
    }

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
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $client = Client::where('id', $slug)->firstOrFail();
        $client->delete();
        $this->redirect('client');
    }

    public function edit($slug)
    {
        try {
            $client = Client::where('id', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('client.edit', ['client' => $client
        ]);
    }

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
    public function export(): void
    {
        $client = Client::select('id', 'name', 'email', 'phone')->orderBy('id', 'desc')->get();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_table_export.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $i = 2;
        foreach ($client as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->email);
            $sheet->setCellValue('D' . $i, $item->phone);
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
    public function invoiceGenerate($slug): void
    {
        $agenda=Agenda::where('client_id', $slug)->get();
        $client=Client::where('id', $slug)->first();
        $poneys = PoneyChoice::all();
        $total = $agenda->sum('prix');
        $htva = $total * 0.79;
        $tva = $total * 0.21;

        for ($i = 0; $i < count($agenda); $i++) {
        $invoice = Invoice::create([
            'id' => $agenda[$i]->id."/".date('Ymd'),
            'name' => $client->name,
            'email' => $client->email,
            'jour' => $agenda[$i]->jour,
            'heure' => $agenda[$i]->start,
            'nbr' => $agenda[$i]->nbr,
            'facturation_type' => $agenda[$i]->facturatin_type,
            'prix' => $agenda[$i]->prix,
            'qt' => $agenda->count(),
            'total' => $total,
            'htva' => $htva,
            'tva' => $tva,
        ]);
        }

        View::render('client.invoice',compact('agenda','client','poneys','total','htva','tva'));
    }
}