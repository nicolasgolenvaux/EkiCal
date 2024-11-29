<?php declare(strict_types = 1);

namespace EkiCal\foundation\Router;

use EkiCal\foundation\Exceptions\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    protected RouteCollection $routes;
    protected RequestContext $context;
    protected Request $request;
    protected array $params;
    protected string $controller;
    protected string $method;

    public function __construct(array $routes)
    {
        $this->initCSRF();
        $this->provisionRoutes($routes);
        $this->makeRequestContext();
        //Si l'URL ne matche pas, elle renvoie la vue exception 404

        try {
            [$this->controller, $this->method] = $this->urlMatching();
        } catch (\Exception) {
            HttpException::render();
        }
    }
    /**Cette méthode initialise la verification du token pour éviter la faille CSRF.
     * On vérifie si la requête est de type POST. On vérifie s'il existe un token et
     * s'il est différent de la variable de session.
     * @return void
     */
    protected function initCSRF(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
                    throw new HttpException();
                }
            } catch (HttpException) {
                HttpException::render(403, 'Vous ne pouvez pas faire ça !');
            }
        }
    }
    /**Cette méthode ajoute toutes les routes du tableau routes.php dans une propriété.
     * - Elle prend en argument un tableau
     * - On va la retourner dans une propriété de SymfonyRoute de classe RouteCollection
     * - On ajoute les routes en bouclant sur le tableau et en ajoutant la clé et la route dans la propriété routes.
     * @param array $routes
     * @return void
     */
    protected function provisionRoutes(array $routes): void
    {
        $this->routes = new RouteCollection();
        foreach ($routes as $key => $route) {
            $this->routes->add($key, $route);
        }
    }
    /**- La propriété `request` va contenir les infos sur la requête qui a été faite.
     * Cela nous permettra de les récupérer plus facilement que par les superGlobals.
     * - La propriété `RequestContext` va nous permettre de configurer le contexte de la requête pour
     * savoir quelle route on va devoir utiliser par rapport à la requête qu'on a reçu.
     * - La méthode `createFromGlobals` crée une nouvelle requête en prenant les infos
     * qui sont contenues dans les super variables Globals.
     * - On crée une nouvelle instance `RequestContext`
     * - On transmet dans la variable context le comportement qui est contenu dans la propriété `request`
     *
     * Pour gérer les autres méthodes HTTP, on insére un champ caché dans notre code pour y écrire
     * la méthode que l'on veut utiliser autre que POST ou GET.
     * S'il existe une valeur dans le champ `_method` de POST, c'est qu'il y a une méthode différente et si elle
     * existe dans le tableau.
     * On modifie la méthode dans l'attribut `context`
     * @return void
     */
    protected function makeRequestContext(): void
    {
        $this->request = Request::createFromGlobals();
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);
        if (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), Route::HTTP_METHODS)) {
            $this->context->setMethod($_POST['_method']);
        }
    }
    /**Cette méthode qui va s'occuper de trouver la bonne route par rapport à l'URI et la méthode HTTP.

    Elle retourne un tableau
    - On utilise la classe UrlMatcher avec les routes comme premier argument et le contexte en second.
    - On stocke tout dans la propriété `params`
    - On lui donne l'URI' complète grâce à la propriété `getPathInfo` qui se trouve dans `matcher`

    Dans le constructeur, on décompose l'urlMatching en deux variables.
     * @return array
     */
    protected function urlMatching(): array
    {
        $matcher = new UrlMatcher($this->routes, $this->context);
        $this->params = $matcher->match($this->request->getPathInfo());

        return [$this->params['_controller'], $this->params['_method']];
    }

    public function getInstance(): void
    {
        $this->cleanParams();
        call_user_func_array([new $this->controller(), $this->method], $this->params);
    }
    /**Cette méthode permet d'effacer les données non nécessaires
    - On boucle sur le tableau La méthode `params`
    - Si une clé commence par un underscore, on la supprime.
     * @return void
     */
    protected function cleanParams(): void
    {
        foreach ($this->params as $key => $param) {
            if (str_starts_with($key, '_')) {
                unset($this->params[$key]);
            }
        }
    }

    /** Cette fonction permet de générer des uri par rapport au nom de la route.
     * @return UrlGenerator
     */
    public function getGenerator(): UrlGenerator
    {
        return new UrlGenerator($this->routes, $this->context);
    }
    /**La méthode getGenerator n'est accèssible que via une instance de classe.
     * Elle va accéder à ma variable globale APP qui contient une instance de app et
     * accéder à la méthode getGenerator. On génèrera une URI de n'importe où.
     * @param string $name
     * @param array $data
     * @return string
     */
    public static function get(string $name, array $data = []): string
    {
        $generator = $GLOBALS['app']->getGenerator();
        $uri = $generator->generate($name, $data);
        return $uri;
    }
}
