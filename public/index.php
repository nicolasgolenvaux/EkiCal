<?php
declare(strict_types = 1);
//Ce fichier va rÃ©cupÃ©rer toutes les requÃªtes qui viennent de l'extÃ©rieur.
//Il va crÃ©er l'application et rendre la rÃ©ponse qu'on attend par rapport
//Ã  la route demandÃ©e par le client et par rapport Ã  la mÃ©thode demandÃ©e.


/** On dÃ©finit une constante disponible partout dans le projet
qui va nous permettre d'avoir le rÃ©pertoire de base du projet
On remplace le dossier '/public' par rien et on rÃ©cupÃ¨re la
constante __DIR__ (le chemin absolu du rÃ©pertoire dans lequel on est actuellement).**/
define('ROOT', str_replace(DIRECTORY_SEPARATOR.'public', '', __DIR__));

// On requiert l'auto-loader de composer pour avoir les dÃ©pendence partout dans le projet
require_once ROOT.'/vendor/autoload.php';

// On crÃ©e une instance de 'App' pour initializer les composants
$app = new EkiCal\foundation\App();
$app->render();
