<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy as routing;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/config/db.php';
/* $app = AppFactory::create(); */

$app->group('/carros', function (routing $group) use ($pdo) {
    
    $group->post('/crear', function(Request $request, Response $response) use ($pdo) {
        // Leer JSON del body
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();

        // Validar campos requeridos
        $camposObligatorios = ['placa', 'color', 'fecha_ingreso'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
                $response->getBody()->write(json_encode($respuesta));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        // Validar existencia del carro
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM carro WHERE placa = :placa");
        $stmt->execute([':placa' => $datos['placa']]);
        $carroExiste = $stmt->fetchColumn();

        if ($carroExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'El carro con esa placa ya existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }


        $insert_carro = $pdo->prepare("
            INSERT INTO carro (placa, color, fecha_ingreso)
            VALUES (:placa, :color, :fecha_ingreso)
        ");
        $insert_carro->execute([
            ':placa' => $datos['placa'],
            ':color' => $datos['color'],
            ':fecha_ingreso' => $datos['fecha_ingreso']
        ]);

        $carro_id = $pdo->lastInsertId();

        if ($carro_id) {
            $respuesta = ['codigo' => 201, 'msg' => 'Carro creado correctamente', 'carro_id' => $carro_id];
            $status = 201;
        } else {
            $respuesta = ['codigo' => 500, 'msg' => 'Error al crear el carro'];
            $status = 500;
        }

        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');


    });

    //lista carros
    $group->get('/lista', function(Request $request, Response $response) use ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM carro ORDER BY idcarro");
            $carros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $respuesta = ['data' => $carros];
            $status = 200;
        } catch (PDOException $e) {
            $respuesta = ['data' => [], 'error' => $e->getMessage()];
            $status = 500;
        }
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });

    /////////// Actualizar carro //////
    $group->put('/actualizar', function(Request $request, Response $response) use ($pdo) {
        // Leer JSON del body
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();

        // Validar campos requeridos
        $camposObligatorios = ['placa', 'color'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
                $response->getBody()->write(json_encode($respuesta));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

            // Validar existencia del carro
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM carro WHERE placa = :placa");
        $stmt->execute([':placa' => $datos['placa']]);
        $carroExiste = $stmt->fetchColumn();

        if (!$carroExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'El carro con esa placa no existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $insert_carro = $pdo->prepare("
            UPDATE carro SET color=:color WHERE placa= :placa
        ");
        $insert_carro->execute([
            ':placa' => $datos['placa'],
            ':color' => $datos['color'],

        ]);

        $respuesta = ['codigo' => 201, 'msg' => 'Color actualizado correctamente', 'color_carro' => $datos['color']];
        $status = 201;
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');


    });

    /////////// Eliminar carro //////
$group->delete('/eliminar/{id}', function(Request $request, Response $response, $args) use ($pdo) {
        $stmt = $pdo->prepare("DELETE FROM carro WHERE idcarro=:id");
        $stmt->execute([':id' => $args['id']]);
        $respuesta = ['codigo' => 200, 'msg' => 'Carro eliminado correctamente'];
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });








});



