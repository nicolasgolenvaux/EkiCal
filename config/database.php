<?php declare(strict_types = 1);
/**
 * Ce tableau liste les informations nécessaires à l'établissement
 *  de la connection à la base de données SQLite.
 */

return [
    'driver'   => env('DB_DRIVER', 'sqlite'),
    'host'     => env('DB_HOST', 'localhost'),
    'name'     => env('DB_DATABASE', './database/eki_cal'),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
];
