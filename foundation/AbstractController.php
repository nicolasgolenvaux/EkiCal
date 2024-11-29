<?php declare(strict_types = 1);

namespace EkiCal\foundation;

use EkiCal\foundation\Router\Router;

abstract class AbstractController
{
    protected function redirect(string $name, array $data = []): void
    {
        header(sprintf('Location: %s', Router::get($name, $data)));
        die;
    }
}
