<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;



require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/db.php';

$app = AppFactory::create();


require __DIR__ . '/../src/modules/usuarios/routes.php';
require __DIR__ . '/../src/modules/carros/routes.php';
require __DIR__ . '/../src/modules/viajes/routes.php';
require __DIR__ . '/../src/modules/ciudades/routes.php';
require __DIR__ . '/../src/modules/consultas/routes.php';




$app->run();

