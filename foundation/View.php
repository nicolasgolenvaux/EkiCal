<?php declare(strict_types = 1);

namespace EkiCal\foundation;

use App\Models\Agenda;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
/**
 * Cette classe va nous permettre de récupérer des vues par des fonctions via Twig.
 *Ces fonctions vont rendre les vues disponibles à Twig car elles ne sont pas dans le même
 * scope.
 */
class View
{
    /**
     * @param string $view
     * @param array $data
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function render(string $view, array $data = []): void
    {
        $view = str_replace('.', '/', $view);
        if (!static::viewExists($view)) {
            throw new \InvalidArgumentException(
                sprintf('La vue %s n\'existe pas', $view)
            );
        }
        $twig = static::initTwig();
        echo $twig->render(
            sprintf('%s.%s', $view, Config::get('twig.template_extension')),
            $data
        );
    }
    /**
     * @param string $view
     * @return bool
     */
    protected static function viewExists(string $view): bool
    {
        return file_exists(
            sprintf('%s/resources/views/%s.%s', ROOT, $view, Config::get('twig.template_extension'))
        );
    }
    /**
     * @return Environment
     */
    protected static function initTwig(): Environment
    {
        $loader = new FilesystemLoader(ROOT.'/resources/views');

        $twig = new Environment($loader, [
            'cache' => ROOT.'/cache/twig',
            'auto_reload' => true,
        ]);
        foreach (Config::get('twig.functions') as $helper) {
            $twig->addFunction(new TwigFunction($helper, $helper));
        }
        return $twig;
    }
//On crée des fonctions propres aux vues que l'on va pouvoir utiliser
// dans nos fonctions templates Twig.
    /**Cette fonction crée un champ caché en html avec la variable
     * de session _token.
     * @return string
     */
    public static function csrfField(): string
    {
        return sprintf('<input type="hidden" name="_token" value="%s">', Session::get('_token'));
    }
    /**Cette fonction crée un champ caché en html avec la méthode HTTP.
     * @param string $httpMethod
     * @return string
     */
    public static function method(string $httpMethod): string
    {
        return sprintf('<input type="hidden" name="_method" value="%s">', $httpMethod);
    }
    /** Cette fonction permet de conserver les données lors d'erreurs de validation d'un champ.
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function old(string $key, mixed $default = null): mixed
    {
        $old = Session::getFlash(Session::OLD);
        return $old[$key] ?? $default;
    }

}
