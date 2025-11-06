<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy as routing;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/config/db.php';
/* $app = AppFactory::create(); */

$app->group('/usuarios', function (routing $group) use ($pdo) {


    //crear usuario
    $group->post('/crear', function(Request $request, Response $response) use ($pdo) {
        // Leer JSON del body
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();

        $password_encript=password_hash($datos['password'],PASSWORD_BCRYPT,['cost' => 12,]);

        // Validar campos requeridos
        $camposObligatorios = ['usuario' , 'nombre', 'password'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
                $response->getBody()->write(json_encode($respuesta));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        // Validar existencia del usuario
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $datos['usuario']]);
        $usuarioExiste = $stmt->fetchColumn();

        if ($usuarioExiste) {
            $respuesta = ['codigo' => 400, 'msg' => 'El usuario ya existe'];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }


        $insert_usuario = $pdo->prepare("
            INSERT INTO usuarios (usuario, nombre, password, fecha_registro_usuario)
            VALUES (:usuario,:nombre, :password, now())
        ");
        $insert_usuario->execute([
            ':usuario' => $datos['usuario'],
            ':nombre' => $datos['nombre'],
            ':password' => $password_encript
        ]);

        $usuario_id = $pdo->lastInsertId();

        if ($usuario_id) {
            $respuesta = ['codigo' => 201, 'msg' => 'usuario creado correctamente, regresa al login', 'usuario_id' => $usuario_id];
            $status = 201;
        } else {
            $respuesta = ['codigo' => 500, 'msg' => 'Error al crear el usuario'];
            $status = 500;
        }

        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');


    });


    $group->get('/{usuario}', function(Request $request, Response $response, $args) use ($pdo) {
    $usuario = $args['usuario'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);
    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioData) {
        $respuesta = ['codigo'=>200, 'msg'=>'Usuario encontrado', 'usuario'=>$usuarioData];
        $status = 200;
    } else {
        $respuesta = ['codigo'=>404, 'msg'=>'Usuario no encontrado'];
        $status = 404;
    }

    $response->getBody()->write(json_encode($respuesta));
    return $response->withHeader('Content-Type','application/json')->withStatus($status);
    });

    /////////// Actualizar usuario //////
    $group->put('/actualizar/{id_usuario}', function(Request $request, Response $response, $args) use ($pdo) {
        $id_usuario = $args['id_usuario'];
  /*       $datos = json_decode(file_get_contents('php://input'), true); */
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true) ?? $request->getParsedBody();


        if (empty($datos['nombre']) || empty($datos['password'])) {
            $response->getBody()->write(json_encode(['codigo'=>400,'msg'=>'Campos obligatorios']));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre=:nombre, password=:password WHERE id_usuario=:id_usuario");
        $stmt->execute([
            ':nombre'=>$datos['nombre'],
            ':password'=>$password_hash,
            ':id_usuario'=>$id_usuario
        ]);

        $response->getBody()->write(json_encode(['codigo'=>200,'msg'=>'Usuario actualizado correctamente, regresa al login']));
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    });

    //login
    $group->post('/login', function(Request $request, Response $response) use ($pdo) {
    // Leer JSON del body
    $input = file_get_contents('php://input');
    $datos = json_decode($input, true) ?? $request->getParsedBody();

	// Validar campos requeridos
	$camposObligatorios = ['usuario', 'password'];
    foreach ($camposObligatorios as $campo) {
        if (empty($datos[$campo])) {
            $respuesta = ['codigo' => 400, 'msg' => "El campo '$campo' es obligatorio"];
            $response->getBody()->write(json_encode($respuesta));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    // Validar existencia del usuario
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $datos['usuario']]);
    $usuarioExiste = $stmt->fetchColumn();

    if (!$usuarioExiste) {
        $respuesta = ['codigo' => 400, 'msg' => 'El usuario no existe'];
        $response->getBody()->write(json_encode($respuesta));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }


 	$login_usuario = $pdo->prepare("
	SELECT * FROM usuarios WHERE usuario=:usuario

    ");
    $login_usuario->execute([
        ':usuario' => $datos['usuario']
    ]);

	$user= $login_usuario->fetch(PDO::FETCH_ASSOC);
	$valida =$login_usuario->rowCount();

	if($valida>0){
		if(password_verify( $datos['password'],$user['password'])){
		$respuesta = ['codigo' => 200, 'msg' => 'Ha iniciado sesion', 'usuario' => $user];
        $status = 201;
		}else{
		$respuesta = ['codigo' => 500, 'msg' => 'Usuario o contraseña incorrectos'];
        $status = 500;
	}

	}else{
		$respuesta = ['codigo' => 500, 'msg' => 'Usuario o contraseña incorrectos'];
        $status = 500;
	}


    $response->getBody()->write(json_encode($respuesta));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');


});

});
