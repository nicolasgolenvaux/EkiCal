<?php declare(strict_types=1);

namespace App\Controllers;

use App\Models\PoneyChoice;
use EkiCal\foundation\AbstractController;
use EkiCal\foundation\Session;
use EkiCal\foundation\Validator;



class PoneyChoiceController extends AbstractController
{
    /**Cette méthode permet d'enregistrer dans la table pivot poneyChoice.
     * @return void
     */
    public function registerPoneyChoice():void
    {
        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'agenda_id' => ['required'],
            'poney_id' => ['required'],

        ]);
        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('agenda');
        }
            $poney = PoneyChoice::create([
                'agenda_id' => $_POST['agenda_id'],
                'poney_id' => $_POST['poney_id']
            ]);

       $this->redirect('agendas.edit',['slug' => $poney->agenda_id]);
    }
}
