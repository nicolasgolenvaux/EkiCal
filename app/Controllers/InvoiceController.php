<?php declare(strict_types = 1);
/**
 * Ce Controller gère toutes les méthodes liées à la table 'invoices'.
 */
namespace App\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\View;
use EkiCal\foundation\Authentication as Auth;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class InvoiceController extends AbstractController
{
    /**Cette méthode affiche la pages facturation et sélectionne les factures du mois en cours.
     * @return void
     */
    public function index(): void
    {
        $titre = 'Facturation';
        $date = Carbon::now();
        $invoices = Invoice::select('id', 'name', 'total', 'created_at')
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->orderBy('id')->groupBy('id')
            ->get();
        $record = Invoice::whereMonth('created_at', $_POST['month']->month)->whereYear('created_at', $_POST['year']->year)->get();
        View::render('invoices.index', compact('titre', 'invoices', 'record'));
    }

    /**Cette méthode efface une facture de la table invoices.
     * @param $slug
     * @return void
     */
    public function delete($slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }
        $invoices = Invoice::where('id', $slug)->firstOrFail();
        $invoices->delete();
        $this->redirect('invoices');
    }

    /**Cette méthode affiche le détail d'une facture client dans la page show.
     * @param $slug
     * @return void
     */
    public function show($slug): void
    {
        $invoice = Invoice::all()->where('id', $slug);
        $total = $invoice->first()->total;
        $tva = $invoice->first()->tva;
        $htva = $invoice->first()->htva;

        View::render('invoices.show', compact('invoice', 'total', 'tva', 'htva'));

    }

    /**
     * @return void
     */
    public function detailInvoice(): void
    {
        $record = Invoice::whereMonth('created_at', $_POST['month']->month)->whereYear('created_at', $_POST['year']->year)->get();
        View::render('invoices.index', compact('record'));
    }

    /**Cette méthode permet d'exporter la table des facturations du mois en cours.
     * @return void
     */
    public function exportInvoices(): void
    {
        $date = Carbon::now();
        $invoices = Invoice::select('id', 'name', 'total', 'created_at')
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->orderBy('id')->groupBy('id')
            ->get();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="invoices_table_export.xlsx"');
        header('Cache-Control: max-age=0');

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A' . 1, 'n° id');
        $sheet->setCellValue('B' . 1, 'Nom');
        $sheet->setCellValue('C' . 1, 'total');
        $sheet->setCellValue('D' . 1, 'Date de création');


        $i = 3;
        foreach ($invoices as $item) {
            $sheet->setCellValue('A' . $i, $item->id);
            $sheet->setCellValue('B' . $i, $item->name);
            $sheet->setCellValue('C' . $i, $item->total);
            $sheet->setCellValue('D' . $i, $item->created_at);

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
            echo "Erreur lors de l'exportation : " . $e->getMessage();
        }

    }
}