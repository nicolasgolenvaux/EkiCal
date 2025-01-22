<?php declare(strict_types=1);

/**
 * On doit ajouter ce fichier à l'autoloader pour pouvoir avoir accès aux fonctions dans tous
 * les fichiers.
 */

use App\Models\Agenda;
use EkiCal\foundation\Authentication;
use EkiCal\foundation\Router\Router;
use EkiCal\foundation\Session;
use EkiCal\foundation\View;


if (!function_exists('auth')) {
    /**Cette fonction me permet d'accéder à la classe Authentication
     * en créant une instance.
     * @return Authentication
     */
    function auth(): Authentication
    {
        return new Authentication();
    }
}

if (!function_exists('route')) {
    /**Cette fonction permet aux templates de pouvoir générer facilement des URI
     * pour accéder à mes différentes pages.
     */
    function route(string $name, array $data = []): string
    {
        return Router::get($name, $data);
    }
}

if (!function_exists('errors')) {
    /**On va permettre à cette fonction de récupérer soit une erreur définie soit toutes
     * les erreurs. En paramètre, on indique null ou un string.
     * @param string|null $field
     * @return array|null
     */
    function errors(?string $field = null): ?array
    {
        $errors = Session::getFlash(Session::ERRORS);
        if ($field) {
            return $errors[$field] ?? null;
        }
        return $errors;
    }
}

if (!function_exists('status')) {
    /**Cette fonction retourne soit un massage de validation soit un null
     * s'il n'y en a pas.
     * @return string|null
     */
    function status(): ?string
    {
        return Session::getFlash(Session::STATUS);
    }
}

if (!function_exists('csrf_field')) {
    /**Cette fonction retourne le champ CSRF crée dans ma vue
     * @return string
     */
    function csrf_field(): string
    {
        return View::csrfField();
    }
}

if (!function_exists('method')) {
    /**Cette fonction retourne le champ method crée dans ma vue.
     * @param string $httpMethod
     * @return string
     */
    function method(string $httpMethod): string
    {
        return View::method($httpMethod);
    }
}

if (!function_exists('old')) {
    /**Cette fonction permet d'accéder aux anciennes valeurs.
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function old(string $key, mixed $default = null): mixed
    {
        return View::old($key, $default);
    }

}


