<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy as routing;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/config/db.php';

$app->group('/ciudades', function($group) use ($pdo) {

    $group->get('/lista', function($request, $response) use ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT idciudad, nombre FROM ciudad ORDER BY nombre ASC");
            $stmt->execute();
            $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($ciudades) {
                $respuesta = [
                    'codigo' => 200,
                    'msg' => 'Lista de ciudades obtenida correctamente',
                    'data' => $ciudades
                ];
                $status = 200;
            } else {
                $respuesta = [
                    'codigo' => 404,
                    'msg' => 'No hay ciudades registradas',
                    'data' => []
                ];
                $status = 404;
            }

        } catch (PDOException $e) {
            $respuesta = [
                'codigo' => 500,
                'msg' => 'Error en la base de datos: ' . $e->getMessage()
            ];
            $status = 500;
        }

        $response->getBody()->write(json_encode($respuesta));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*');
    });
});