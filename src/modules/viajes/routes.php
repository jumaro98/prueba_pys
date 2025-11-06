<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy as routing;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/config/db.php';
/* $app = AppFactory::create(); */

$app->group('/viajes', function (routing $group) use ($pdo) {


    ///// crear viajes //////////
    $group->post('/crear', function(Request $request, Response $response) use ($pdo) {

        // Leer JSON del body
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();

        // Validar campos requeridos
        $camposObligatorios = ['idcarro', 'idciudad_origen', 'idciudad_destino', 'tiempo_horas', 'fecha'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
                $response->getBody()->write(json_encode($respuesta));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        // Validar existencia del carro
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM carro WHERE idcarro = :idcarro");
        $stmt->execute([':idcarro' => $datos['idcarro']]);
        $carroExiste = $stmt->fetchColumn();

        if (!$carroExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'El carro con ese ID no existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validar ciudad de origen
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ciudad WHERE idciudad = :id");
        $stmt->execute([':id' => $datos['idciudad_origen']]);
        $ciudadOrigenExiste = $stmt->fetchColumn();

        if (!$ciudadOrigenExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'La ciudad de origen con ese ID no existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validar ciudad de destino
        $stmt->execute([':id' => $datos['idciudad_destino']]);
        $ciudadDestinoExiste = $stmt->fetchColumn();

        if (!$ciudadDestinoExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'La ciudad de destino con ese ID no existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Insertar viaje
        $insert = $pdo->prepare("
            INSERT INTO viaje (idcarro, idciudad_origen, idciudad_destino, tiempo_horas, fecha)
            VALUES (:idcarro, :idciudad_origen, :idciudad_destino, :tiempo_horas, :fecha)
        ");
        $insert->execute([
            ':idcarro' => $datos['idcarro'],
            ':idciudad_origen' => $datos['idciudad_origen'],
            ':idciudad_destino' => $datos['idciudad_destino'],
            ':tiempo_horas' => $datos['tiempo_horas'],
            ':fecha' => $datos['fecha']
        ]);

        $viaje_id = $pdo->lastInsertId();

        if ($viaje_id) {
            $respuesta = ['codigo' => 201, 'msg' => 'Viaje creado correctamente', 'idviaje' => $viaje_id];
            $status = 201;
        } else {
            $respuesta = ['codigo' => 500, 'msg' => 'Error al crear el viaje'];
            $status = 500;
        }

        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });



        ////// Consultar viajes por placa////
    $group->get('/lista',function(Request $request, Response $response) use ($pdo){

        try {

        $consulta =$pdo->prepare("SELECT v.*, c.placa, 
                                      co.nombre AS ciudad_origen, 
                                      cd.nombre AS ciudad_destino
                               FROM viaje v
                               INNER JOIN carro c ON v.idcarro = c.idcarro
                               INNER JOIN ciudad co ON v.idciudad_origen = co.idciudad
                               INNER JOIN ciudad cd ON v.idciudad_destino = cd.idciudad
                               ORDER BY v.idviaje");
        $consulta->execute();



        $viajes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $respuesta =$viajes;

            if ($viajes) {
                $respuesta = [
                    'codigo' => 200,
                    'msg' => 'Lista de viajes obtenida correctamente',
                    'data' => $viajes
                ];
                $status = 200;
            } else {
                $respuesta = [
                    'codigo' => 404,
                    'msg' => 'No hay viajes registrados',
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
            ->withHeader('Content-Type', 'application/json');


        

    });


        ////// Consultar viajes por placa////
    $group->get('/{placa}',function(Request $request, Response $response, $args) use ($pdo){

        $placa_carro=$args['placa'];
        $consulta =$pdo->prepare("SELECT * FROM viaje v INNER JOIN carro c ON v.idcarro =c.idcarro WHERE c.placa=:placa");

        $consulta->bindParam(':placa',$placa_carro);
        $consulta->execute();

        $vehiculo = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($vehiculo));

        return $response->withHeader('Content-Type','application/json');

    });


    /////////// Actualizar viaje //////
    $group->put('/actualizar', function(Request $request, Response $response) use ($pdo) {
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();

        // Campos obligatorios
        $camposObligatorios = ['idviaje', 'idcarro', 'idciudad_origen', 'idciudad_destino', 'tiempo_horas', 'fecha'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
                $response->getBody()->write(json_encode($respuesta));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        try {
            // Verificar existencia del viaje
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM viaje WHERE idviaje = :id");
            $stmt->execute([':id' => $datos['idviaje']]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('El viaje con ese ID no existe');
            }

            // Verificar existencia del carro
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM carro WHERE idcarro = :id");
            $stmt->execute([':id' => $datos['idcarro']]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('El carro con ese ID no existe');
            }

            // Verificar ciudades
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM ciudad WHERE idciudad = :id");
            $stmt->execute([':id' => $datos['idciudad_origen']]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('La ciudad de origen no existe');
            }
            $stmt->execute([':id' => $datos['idciudad_destino']]);
            if (!$stmt->fetchColumn()) {
                throw new Exception('La ciudad de destino no existe');
            }

            // Actualizar viaje
            $update = $pdo->prepare("
                UPDATE viaje
                SET idcarro = :idcarro,
                    idciudad_origen = :idciudad_origen,
                    idciudad_destino = :idciudad_destino,
                    tiempo_horas = :tiempo_horas,
                    fecha = :fecha
                WHERE idviaje = :idviaje
            ");

            $update->execute([
                ':idviaje' => $datos['idviaje'],
                ':idcarro' => $datos['idcarro'],
                ':idciudad_origen' => $datos['idciudad_origen'],
                ':idciudad_destino' => $datos['idciudad_destino'],
                ':tiempo_horas' => $datos['tiempo_horas'],
                ':fecha' => $datos['fecha']
            ]);

            $respuesta = [
                'codigo' => 200,
                'msg' => 'Viaje actualizado correctamente',
                'viaje' => $datos
            ];
            $status = 200;

        } catch (Exception $e) {
            $respuesta = ['codigo' => 400, 'msg' => $e->getMessage()];
            $status = 400;
        }

        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });


});