<?php declare(strict_types = 1);
/**
 * Cette page affiche le message d'erreur.
 */
namespace EkiCal\foundation\Exceptions;

use EkiCal\foundation\View;

class HttpException extends \Exception
{
    public static function render(int $httpCode = 404, string $message = 'Page non trouvée'): void
    {
        http_response_code($httpCode);
        View::render('errors.default', [
            'httpCode' => $httpCode,
            'message' => $message,
        ]);
        die;
    }
}
