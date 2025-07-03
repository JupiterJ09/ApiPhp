<?php

$rawUri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos = array_values(array_filter(explode('/', $rawUri)));
$method    = $_SERVER['REQUEST_METHOD'];
$count     = count($segmentos);          // Para no repetir

header('Content-Type: application/json');

// Ruta base mínima: /api/v1/___  → 3 segmentos
if ($count < 3) {
    http_response_code(404);
    echo json_encode(["detalle" => "no encontrado ejemplo"]);
    exit;
}

/**
 * Índices:
 *  0 => api
 *  1 => v1
 *  2 => cursos | registro | ...
 *  3 => {id?}
 */
$route = $segmentos[2];

switch ($route) {

    /* -------------------  CURSOS  ------------------- */
    case 'cursos':
        $controller = new CursosController();

        // /api/v1/cursos  (lista o crea)
        if ($count === 3) {
            if ($method === 'GET') {
                $controller->getCursos();          // listar
            } elseif ($method === 'POST') {

                   // 1. Intenta leer datos desde JSON
        $input = json_decode(file_get_contents("php://input"), true);

                if (!is_array($input)) {
                    $input = $_POST;
                }
            
                // 3. Construye $datos sin notices
                $datos = [
                    'titulo'      => $input['titulo']      ?? null,
                    'descripcion' => $input['descripcion'] ?? null,
                    'instructor'  => $input['instructor']  ?? null,
                    'imagen'      => $input['imagen']      ?? null,
                    'precio'      => $input['precio']      ?? null,
                ];

                /*echo "<pre>";
                print_r($datos);
                echo "</pre>";
                return;*/

                $controller->postCursos($datos);  
                
                // crear
            } else {
                http_response_code(405);
                echo json_encode(["detalle" => "Método no permitido en /cursos"]);
            }
            exit;
        }

        // /api/v1/cursos/{id}  (mostrar, actualizar, borrar)
        if ($count === 4 && is_numeric($segmentos[3])) {
            $id = (int) $segmentos[3];
            switch ($method) {
                case 'GET':   
                     $controller->show($id);   
                break;   // obtener 1
                case 'PUT':   

                    // 1. Leer input crudo
                    $raw = file_get_contents("php://input");
                    /*var_dump($raw);
                    exit;*/
                
                    // 2. Detectar formato
                    if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
                        $datos = json_decode($raw, true);
                        echo "codificando en json";
                        
                    } else {
                        // Soporte para x-www-form-urlencoded
                        parse_str($raw, $datos);
                    }
                
                    // 3. Validar
                    if (empty($datos)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Datos vacíos o mal formateados']);
                        return;
                    }
                
                    // 4. Llamar al controlador
                    $controller->putCursos($id, $datos);
                                 
                      break;   // actualizar
                case 'DELETE': $controller->deleteCursos($id);break;   // borrar
                default:
                    http_response_code(405);
                    echo json_encode(["detalle" => "Método no permitido en /cursos/{$id}"]);
            }
            exit;
        }

        break;

    /* -------------------  REGISTRO  ------------------- */
    case 'registro':
        $controller = new RegistroController();

        if ($count === 3) {
            if ($method === 'GET') {
                $controller->getRegistro();
            } elseif ($method === 'POST') {
                $datos = array("nombre"=> $_POST["nombre"],
                               "apellido"=> $_POST["apellido"],
                               "email"=> $_POST["email"]
            );
                $controller->postRegistro($datos);
            } else {
                http_response_code(405);
                echo json_encode(["detalle" => "Método no permitido en /registro"]);
            }
            exit;
        }
        break;

    /* -------------------  DEFAULT  ------------------- */
    default:
        http_response_code(404);
        echo json_encode(["detalle" => "ruta no reconocida"]);
        exit;
}

/* Si llegó aquí, la combinación ruta+ID es inválida */
http_response_code(404);
echo json_encode(["detalle" => "no coincide patrón esperado"]);
