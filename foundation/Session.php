<?php declare(strict_types = 1);
/**
 * Cette classe est utilisée pour créer un système d'authentification pour les utilisateurs.
 * En créant une variable de session 'id'. On pourra vérifier s'il a bien
 * un id. S'il n'en a pas, c'est un simple visiteur. Cette classe va nous permettre de
 * créer tout cela plus facilement.
 */
namespace EkiCal\foundation;
/**
 * Les variables des sessions flash sont des variables de session qui ne seront
 * disponible qu'à la prochaine requête du client. C'est pratique pour les messages d'erreurs
 * et les validations. Elle sera supprimée qu'à la fin de la prochaine requête.
 */
class Session
{
    public const FLASH = '_flash';
    public const OLD = '_old';
    public const STATUS = '_status';
    public const ERRORS = '_errors';

    /**Cette méthode va initialiser une session et  nous permettre de plus facilement
     * utiliser la fonction 'session_start'
     * @return void
     */
    public static function init(): void
    {
        session_start();
    }
    /**Cette méthode va nous permettre d'ajouter une variable de session
     * @param string $key Pour indiquer à quelle clé sera accessible la valeur de ma super-globale
     * @param mixed $value On ne sait pas quelle valeur on va attribuer à la variable de session
     * @param bool $isFlash On indique si on veut créer une variable de session flash.
     * @return mixed On ne sait pas quelle valeur on va attribuer à la variable de session
     */
    public static function add(string $key, mixed $value, bool $isFlash = false): mixed
    {
        if ($isFlash) {
            return $_SESSION[static::FLASH][$key] = $value;
        }
        return $_SESSION[$key] = $value;
    }

    /**On crée une méthode 'raccourci' à la méthode add pour insérer une variable de session flash
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function addFlash(string $key, mixed $value): mixed
    {
        return static::add($key, $value, true);
    }
    /**Cette méthode récupère la valeur de la variable de session.
     * Si elle n'existe pas, on retourne null.
     * @param string $key La cé pour laquelle on souhaite récupérer la valeur
     * @param bool $isFlash // on distingue si on veut une variable de session classique ou flash
     * @return mixed
     */
    public static function get(string $key, bool $isFlash = false): mixed
    {
        if ($isFlash) {
            return $_SESSION[static::FLASH][$key] ?? null;
        }
        return $_SESSION[$key] ?? null;
    }
    /** On crée une méthode 'raccourci' à la méthode get pour accéder directement aux variables flash
     * @param string $key
     * @return mixed
     */
    public static function getFlash(string $key): mixed
    {
        return static::get($key, true);
    }
    /**Cette méthode supprime une variable de session
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    /**Cette méthode nous permet de vider le tableau correspondant à la valeur '_flash'
     * @return void
     */
    public static function resetFlash(): void
    {
        $_SESSION[static::FLASH] = [];
    }
}
