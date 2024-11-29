<?php
declare(strict_types = 1);
//Ce fichier va récupérer toutes les requêtes qui viennent de l'extérieur.
//Il va créer l'application et rendre la réponse qu'on attend par rapport
//à la route demandée par le client et par rapport à la méthode demandée.


/** On définit une constante disponible partout dans le projet
qui va nous permettre d'avoir le répertoire de base du projet
On remplace le dossier '/public' par rien et on récupère la
 constante __DIR__ (le chemin absolu du répertoire dans lequel on est actuellement).**/
define('ROOT', str_replace('\public', '', __DIR__));

// On requiert l'auto-loader de composer pour avoir les dépendence partout dans le projet
require_once ROOT.'/vendor/autoload.php';

// On crée une instance de 'App' pour initializer les composants
$app = new EkiCal\foundation\App();
$app->render();
