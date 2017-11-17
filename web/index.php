<?php
// Cette directive détermine si les erreurs doivent être affichées à l'écran ou non (cf php manual)
// C'est une directive nécessaire en développement mais qui ne doit jamais être utilisée sur un système en production. (e.g. systèmes connectés à Internet).
ini_set('display_errors', 0);

// on charge les namespace situe dans le repertoire vendor (cf composer psr-4)
require_once __DIR__.'/../vendor/autoload.php';

// on inclu nos fichiers qui définissent les services utilise par l'application
$app = require __DIR__.'/../app/app.php';
// un fichier de conf des services
require __DIR__.'/../config/prod.php';
// nos le comportement de l'app pour les routes
require __DIR__.'/../app/routes.php';

$app->run();
