<?php declare(strict_types = 1);
/**Cette classe permet de vérifier si le visiteur est authentifié ou pas.
 * Cette classe ne contient pas la logique à la validation
 * de formulaire qui seront propre à chaque projet.
 */
namespace EkiCal\foundation;

use App\Models\User;

class Authentication
{

    /**Cette constante (='user_id') sera l'identifiant des variables de session qui va me permettre de savoir si un utilisateur est connecté via la bdd.
     *
     */
    protected const SESSION_ID = 'user_id';

    /**Cette méthode va nous permettre de vérifier si un utilisateur est connecté ou pas.
     * On vérifie si la variable de session existe.
     * @return bool
     */
    public static function check(): bool
    {
        return (bool) Session::get(static::SESSION_ID);
    }

    /**Cette méthode vérifie si une personne est authentifiée
     * et si elle a un rôle d'administrateur.
     * @return bool
     */
    public static function checkIsAdmin(): bool
    {
        // Pour pouvoir avoir l'équivalent boolean de notre valeur,
        // on utilise l'opérateur de cast parce que la fonction get ramène soit null,
        // soit un user_id.
        return static::check() && static::get()->role === 'admin';
    }
    /**Cette méthode permet de vérifier si les champs email et password
     * correspondent à ceux de la bdd.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function verify(string $email, string $password): bool
    {
        //On va sélectionner l'entrée dans la bdd qui correspond à l'email
        // écrit dans le formulaire. On indique que l'on veut que le premier résultat qui
        // a l'adresse mail correspondant. Normalement, il ne peut y avoir deux fois l'adresse mail
        //On vérifie si elle est connectée et si dans la bdd le champ rôle est bien 'admin'.
        $user = User::where('email', $email)->first();
        // On vérifie si user n'est pas null et si le mot de passe correspond bien à celui
        // qui est en bdd.
        return $user && password_verify($password, $user->password);
    }
    /**Cette méthode va créer une variable de session avec comme valeur $id
     * @param int $id
     * @return void
     */
    public static function authenticate(int $id): void
    {
        Session::add(static::SESSION_ID, $id);
    }
    /** Cette méthode va déconnecter l'utilisateur en supprimant la variable
     * de session SESSION_ID.
     * @return void
     */
    public static function logout(): void
    {
        Session::remove(static::SESSION_ID);
    }
    /**Cette méthode retourne soit la valeur null ou un entier ('id' dans la table bdd)
     * @return int|null
     */
    public static function id(): ?int
    {
        return Session::get(static::SESSION_ID);
    }

    /**On récupère soit l'instance complète, soit un null.
     * @return User|null
     */
    public static function get(): ?User
    {
        return User::find(static::id());
    }
}
