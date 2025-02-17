<?php declare(strict_types = 1);
/**
 * Cette classe gère les différentes méthodes retournées et crée une uri pour le routage de Symfony.
 */
namespace EkiCal\foundation\Router;

use EkiCal\foundation\AbstractController;
use Symfony\Component\Routing\Route as SymfonyRoute;

class Route
{
    /**Liste des différentes méthodes HTTP
     *
     */
    public const HTTP_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**Cette fonction appelle une méthode magique qui retourne une uri, l'action voulue et la méthode appelée.
     * @param string $httpMethod
     * @param array $arguments
     * @return SymfonyRoute
     */
    public static function __callStatic(string $httpMethod, array $arguments): SymfonyRoute
    {
        if (!in_array(strtoupper($httpMethod), static::HTTP_METHODS)) {
            throw new \BadMethodCallException(
                sprintf('Méthode HTTP indisponible (%s)', $httpMethod)
            );
        }
        [$uri, $action] = $arguments;
        return static::make($uri, $action, $httpMethod);
    }

    protected static function make(string $uri, array $action, string $httpMethod): SymfonyRoute
    {
        [$controller, $method] = $action;
        if (!static::checkIfActionExists($controller, $method)) {
            throw new \InvalidArgumentException(
                sprintf('L\'action n\'existe pas (%s)', implode(', ', $action))
            );
        }

        return new SymfonyRoute($uri, [
            '_controller' => $controller,
            '_method' => $method,
        ],
            options: [
                'utf8' => true,
            ],
            methods: [$httpMethod]);
    }

    protected static function checkIfActionExists(string $controller, string $method): bool
    {
        return class_exists($controller) && is_subclass_of($controller, AbstractController::class) && method_exists($controller, $method);
    }
}
