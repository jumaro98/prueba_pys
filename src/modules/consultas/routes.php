<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy as routing;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/config/db.php';


$app->group('/consultas', function (routing $group) use ($pdo) {
$group->get('/{id}', function(Request $request, Response $response, $args) use ($pdo) {
    $id = $args['id'];
    $data = [];

    switch($id) {
        case 1: // 3.1
            $stmt = $pdo->query("SELECT color, COUNT(*) AS cantidad FROM carro GROUP BY color");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 2: // 3.2
            $stmt = $pdo->prepare("
                SELECT c.placa, cd_dest.nombre AS ciudad_destino, v.tiempo_horas
                FROM viaje v
                INNER JOIN carro c ON v.idcarro = c.idcarro
                INNER JOIN ciudad cd_origen ON v.idciudad_origen = cd_origen.idciudad
                INNER JOIN ciudad cd_dest ON v.idciudad_destino = cd_dest.idciudad
                WHERE cd_origen.nombre = :ciudad AND v.fecha >= :fecha
            ");
            $stmt->execute([':ciudad'=>'Medellin', ':fecha'=>'2025-10-08']);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 3: // 3.3
            $stmt = $pdo->prepare("
                SELECT c.placa, c.fecha_ingreso, AVG(v.tiempo_horas) AS promedio_horas
                FROM viaje v
                INNER JOIN carro c ON v.idcarro = c.idcarro
                WHERE c.placa = :placa
                GROUP BY c.placa, c.fecha_ingreso
            ");
            $stmt->execute([':placa'=>'BBB456']);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 4: // 3.4
            $stmt = $pdo->query("
                SELECT * FROM carro
                WHERE idcarro NOT IN (SELECT DISTINCT idcarro FROM viaje)
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 5: // 3.5
            $stmt = $pdo->prepare("
                SELECT c.placa, cd_origen.nombre AS ciudad_origen, cd_dest.nombre AS ciudad_destino, v.fecha
                FROM viaje v
                INNER JOIN carro c ON v.idcarro = c.idcarro
                INNER JOIN ciudad cd_origen ON v.idciudad_origen = cd_origen.idciudad
                INNER JOIN ciudad cd_dest ON v.idciudad_destino = cd_dest.idciudad
                WHERE v.fecha BETWEEN :fecha1 AND :fecha2
            ");
            $stmt->execute([':fecha1'=>'2025-09-26', ':fecha2'=>'2025-10-26']);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 6: // 3.6
            $stmt = $pdo->prepare("
                SELECT c.placa, v.idviaje, cd.nombre AS ciudad, cd.activo
                FROM viaje v
                INNER JOIN carro c ON v.idcarro = c.idcarro
                INNER JOIN ciudad cd ON v.idciudad_origen = cd.idciudad OR v.idciudad_destino = cd.idciudad
                WHERE cd.activo = 0
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        default:
            $respuesta = ['codigo'=>400, 'msg'=>'Consulta no definida', 'data'=>[]];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withHeader('Content-Type','application/json');
    }

    $respuesta = ['codigo'=>200,'msg'=>'Consulta ejecutada','data'=>$data];
    $response->getBody()->write(json_encode($respuesta));
    return $response->withHeader('Content-Type','application/json');
});
});
