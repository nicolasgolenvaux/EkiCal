<?php declare(strict_types = 1);

namespace EkiCal\foundation;
// Cette classe va nous permettre d'initialiser les composants du projet
// (BDD, Sessions,Routes, PHPDotenv...).
use Illuminate\Database\Capsule\Manager as Capsule;
use EkiCal\foundation\Exceptions\HttpException;
use EkiCal\foundation\Router\Router;
use Random\RandomException;
use Symfony\Component\Routing\Generator\UrlGenerator;
/**
 *
 */
class App
{
    protected Router $router;

    public function __construct() {

        $this->initDotenv();
        if (Config::get('app.env') === 'production') {
            $this->initProductionExceptionHandler();
        }
        $this->initSession();
        $this->initDatabase();
        $this->router = new Router(require ROOT.'/app/routes.php');
    }
    /**Cette méthode initialise l'utilisation du fichier '.env' pour récupérer les variables
     * d'environnement pour pouvoir y accéder dans les scripts via la dépendence 'phpdotenv'
     * @return void
     */
    protected function initDotenv(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(ROOT);
        $dotenv->safeLoad();
    }
    /**C'est un gestionnaire d'erreurs qui sera utilisé qu'en production
    On va l'initialiser dans le constructeur de la classe `App` en spécifiant
    si on est en production ou en développement.
    Grâce à la variable d'environnement `app.env` de la classe Config.
     * @return void
     */
    protected function initProductionExceptionHandler(): void
    {
        set_exception_handler(
            fn () => HttpException::render(500, 'Houston, on a un problème! 🚀')
        );
    }
    /**Cette méthode initialise les sessions.
     *Elle initialise la ...
     * @return void
     * @throws RandomException
     */
    protected function initSession(): void
    {
        Session::init();
        Session::add('_token', Session::get('_token') ?? $this->generateCsrfToken());
    }
    /**Cette méthode va générer un token aléatoire. On récupère un certain nombre d'octets aléatoire.
     * On la transforme en format hexadecimal. 1 octet = 2 caractères. On peut faire varier la taille
     * grâce au fichier config/hashing.
     * @throws RandomException
     */
    protected function generateCsrfToken(): string
    {
        $length = Config::get('hashing.csrf_token_length');
        $token = bin2hex(random_bytes($length));
        return $token;
    }

    protected function initDatabase(): void
    {
        date_default_timezone_set(Config::get('app.timezone'));
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'   => Config::get('database.driver'),
            'host'     => Config::get('database.host'),
            'database' => Config::get('database.name'),
            'username' => Config::get('database.username'),
            'password' => Config::get('database.password'),
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
    /**Cette fonction nous permettra de rendre la réponse
     * @return void
     */
    public function render(): void
    {
        $this->router->getInstance();
        Session::resetFlash();
    }
    /** On crée la méthode get pour avoir accès à la méthode getGenerator() en static.
     * @return UrlGenerator
     */
    public function getGenerator(): UrlGenerator
    {
        return $this->router->getGenerator();
    }
}
