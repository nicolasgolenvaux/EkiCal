<?php declare(strict_types=1);

namespace EkiCal\foundation;
// J'utilise 'Valitron' pour valider et mettre des règles dans nos formulaires.
// On lui passe les données sous forme de tableau.
// On ne peut valider que les champs textuels, pas les fichiers. Mais on peut créer des nouvelles règles.
use Illuminate\Database\Capsule\Manager as Capsule;
use Valitron\Validator as ValitronValidator;

class Validator
{
    /** Cette méthode va nous permettre de récupérer une instance correctement configurée
     * de Valitron
     * @param array $data Les données qu'on souhaite valider
     * @return ValitronValidator
     */
    public static function get(array $data): ValitronValidator
    {
        $validator = new ValitronValidator(
            data: $data, // On utilise les arguments nommés.
            lang: 'fr' // Pour avoir les messages en français.
        );
        //Par défaut, les noms du champ sont récupérés via l'attribut name.
        // On va personnaliser les labels pour des champs particuliers. La méthode labels
        // attend un tableau en paramètre. C'est celui qui est retourné par le fichier validation.php
        $validator->labels(require ROOT . '/resources/lang/validation.php');
        static::addCustomRules($validator);
        return $validator;
    }

    /**Cette méthode crée des règles personnalisées pour Valitron.
     * @param ValitronValidator $validator
     * @return void
     */
    protected static function addCustomRules(ValitronValidator $validator): void
    {
        $validator->addRule('unique', function (string $field, mixed $value, array $params, array $fields) {
            return !Capsule::table($params[1])->where($params[0], $value)->exists();
        }, '{field} est invalide');

        $validator->addRule('password', function (string $field, mixed $value, array $params, array $fields) {
            $user = Authentication::get();
            return password_verify($value, $user->password);
        }, '{field} est erroné !');

        $validator->addRule('required_file', function (string $field, mixed $value, array $params, array $fields) {
            return isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK;
        }, '{field} est obligatoire !');
    }
}
