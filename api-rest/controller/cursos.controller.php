<?php

class CursosController{


   
    public function postCursos($datos){
        $clientes = ModeloClientes::getClientes("clientes");
    
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            foreach ($clientes as $key => $valueCliente) {
                if (
                    $_SERVER['PHP_AUTH_USER'] === $valueCliente->id_cliente &&
                    $_SERVER['PHP_AUTH_PW'] === $valueCliente->llave_secreta
                ) {
                    // Validar todos los campos requeridos
                    if (!isset($datos['titulo'], $datos['descripcion'], $datos['instructor'], $datos['imagen'], $datos['precio'])) {
                        echo json_encode([
                            "status" => 400,
                            "detalle" => "Faltan campos requeridos"
                        ]);
                        return;
                    }
    
                    foreach ($datos as $key => $valueDatos) {
                        if(isset($valueDatos) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $valueDatos)){
                            echo json_encode([
                                "status"=>404,
                                "detalle"=>"Error en el campo ".$key
                            ]);
                            return;
                        }
                    }
    
                    $cursos = ModeloCursos::getCursos("cursos");
    
                    foreach ($cursos as $key => $value) {
                        if($value->titulo == $datos["titulo"]){
                            echo json_encode([
                                "status"=>404,
                                "detalle"=>"El título ya existe en la base de datos"
                            ]);
                            return;
                        }
    
                        if($value->descripcion == $datos["descripcion"]){
                            echo json_encode([
                                "status"=>404,
                                "detalle"=>"La descripción ya existe en la base de datos"
                            ]);
                            return;
                        }
                    }
    
                    $datos = array(
                        "titulo"      => $datos["titulo"],
                        "descripcion" => $datos["descripcion"],
                        "instructor"  => $datos["instructor"],
                        "imagen"      => $datos["imagen"],
                        "precio"      => $datos["precio"],
                        "id_creador"  => $valueCliente->id,
                        "created_at"  => date('Y-m-d H:i:s'),
                        "updated_at"  => date('Y-m-d H:i:s')
                    );
    
                    $create = ModeloCursos::createCursos("cursos", $datos);
    
                    if($create == "ok"){
                        echo json_encode([
                            "status"=>200,
                            "detalle"=>"Registro exitoso, su curso ha sido guardado"
                        ]);
                        return;    	
                    }
                }
            }
        }
    }
    



    
    
    public function getCursos(){

        $clientes = ModeloClientes::getClientes("clientes");

        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){

            foreach ($clientes as $key => $value) {


                if (
                    $_SERVER['PHP_AUTH_USER'] === $value->id_cliente &&
                    $_SERVER['PHP_AUTH_PW'] === $value->llave_secreta
                ) {
        
                   

            $cursos = ModeloCursos::getCursos('cursos');
            echo json_encode(["detalle" => $cursos]);
            exit;
            }
        }
            }
}

      
public function putCursos($id, $datos) {

    

    /*=============================================
    Validar credenciales del cliente
    =============================================*/
    $clientes = ModeloClientes::getClientes("clientes");

    /*echo "<pre>Credenciales recibidas:\n";
    echo "Usuario: " . ($_SERVER['PHP_AUTH_USER'] ?? 'NO PROPORCIONADO') . "\n";
    echo "Contraseña: " . ($_SERVER['PHP_AUTH_PW'] ?? 'NO PROPORCIONADO') . "\n";
    echo "</pre>";
    exit; */ 
    // Termina la ejecución para solo mostrar el debug

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        
        /*echo "<pre>Credenciales recibidas:\n";
        echo "Usuario: " . ($_SERVER['PHP_AUTH_USER'] ?? 'NO PROPORCIONADO') . "\n";
        echo "Contraseña: " . ($_SERVER['PHP_AUTH_PW'] ?? 'NO PROPORCIONADO') . "\n";
        echo "</pre>";
        exit;*/

        foreach ($clientes as $valueCliente) {

            
            if (
                $_SERVER['PHP_AUTH_USER'] === $valueCliente->id_cliente &&
                $_SERVER['PHP_AUTH_PW'] === $valueCliente->llave_secreta
            ) {
                $clienteArray = (array)$valueCliente; // Conversión a array
                // Debug: Imprimir SOLO el cliente que coincide
               
                /*echo json_encode([
                "status" => "match_found",
                "cliente" => [
                "id" => $clienteArray["id_cliente"],
                "usarname" => $clienteArray["id"],
                "nombre" => $clienteArray["nombre"],
                "apellido" => $clienteArray["apellido"]
                ]
                ]);exit;
                 

                /*=============================================
                Validar datos
                =============================================*/
                foreach ($datos as $key => $valueDatos) {

                    if (
                        isset($valueDatos) &&
                        !preg_match('/^[()\=\&\$;\-_*"<>¿¡!,:.\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]*$/u', $valueDatos)
                    ) {
                        echo json_encode([
                            "status" => 404,
                            "detalle" => "Error en el campo " . $key
                        ], true);
                        return;
                    }
                }

                /*=============================================
                Validar ID del creador
                =============================================*/
                $curso = ModeloCursos::show("cursos", "clientes", $id);
                $arrayCurso = (array) $curso;
                /*var_dump($curso);
                exit;*/

                foreach ($curso as $valueCurso) {

                 /*echo json_encode(["id de cliente" => $clienteArray["id"],
                  "id de cursos"  => $valueCurso->id_creador
                 ]);*/

                 
                    //exit;  

                    if ($valueCurso->id_creador === $clienteArray["id"]) {

                        /*echo json_encode("Estoy dentro del if");
                        exit;*/
                        /*=============================================
                        Enviar datos al modelo
                        =============================================*/
                        $datosActualizados = array(
                            "id"          => $id,
                            "titulo"      => $datos["titulo"],
                            "descripcion" => $datos["descripcion"],
                            "instructor"  => $datos["instructor"],
                            "imagen"      => $datos["imagen"],
                            "precio"      => $datos["precio"],
                            "updated_at"  => date('Y-m-d H:i:s')
                        );

                        $update = ModeloCursos::update("cursos", $datosActualizados);

                        if ($update == "ok") {
                            echo json_encode([
                                "status" => 200,
                                "detalle" => "Registro exitoso, su curso ha sido actualizado"
                            ], true);
                            return;
                        } else {
                            echo json_encode([
                                "status" => 500,
                                "detalle" => "Error interno al actualizar el curso"
                            ], true);
                            return;
                        }
                    }
                }

                // Si encontró al cliente pero no es el creador
                echo json_encode([
                    "status" => 403,
                    "detalle" => "No está autorizado para modificar este curso"
                ], true);
                return;
            }
        }
    }

    // Si no hay coincidencia de credenciales
    echo json_encode([
        "status" => 403,
        "detalle" => "Credenciales incorrectas o no proporcionadas"
    ], true);
}

    
public function deleteCursos($id) {
    /*=============================================
    Validar credenciales del cliente
    =============================================*/
    $clientes = ModeloClientes::getClientes("clientes");

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        foreach ($clientes as $key => $valueCliente) {
            // Validar autenticación Basic
            if (
                $_SERVER['PHP_AUTH_USER'] === $valueCliente->id_cliente &&
                $_SERVER['PHP_AUTH_PW'] === $valueCliente->llave_secreta
            ) {
                /*=============================================
                Validar id creador del curso
                =============================================*/
                $curso = ModeloCursos::show("cursos", "clientes", $id);

                foreach ($curso as $key => $valueCurso) {
                    // Verificar si el cliente es el creador del curso
                    if ($valueCurso->id_creador == $valueCliente->id) {

                        /*
                        echo json_encode("Estoy dentro del campo eliminar");
                        exit;*/

                        /*=============================================
                        Eliminar el curso
                        =============================================*/
                        $delete = ModeloCursos::delete("cursos", $id);

                        if ($delete == "ok") {
                            $json = array(
                                "status" => 200,
                                "detalle" => "Se ha borrado el curso"
                            );
                            echo json_encode($json, true);
                            return; // Terminar la ejecución
                        }
                    }
                }
            }
        }
    }

    // Si no se cumplen las condiciones (no autorizado o no es el creador)
    $json = array(
        "status" => 400,
        "detalle" => "No tienes permisos para eliminar este curso"
    );
    echo json_encode($json, true);
}

    public function show(int $id){

        $clientes = ModeloClientes::getClientes("clientes");

        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            foreach ($clientes as $key => $valueCliente) {
                if (
                    $_SERVER['PHP_AUTH_USER'] === $valueCliente->id_cliente &&
                    $_SERVER['PHP_AUTH_PW'] === $valueCliente->llave_secreta
                ) {

                    //mostrar todos los cursos
                    $cursos = ModeloCursos::show("cursos","clientes",$id);

                    if (empty($cursos)) {
                        echo json_encode([
                            "status" => 404,
                            "detalle" => "No existe un curso con ese ID"
                        ]);
                        exit;
                    } else {
                        echo json_encode([
                            "status" => 200,
                            "detalle" => $cursos
                        ]);
                    }
                    
                }
            
            }
        }


    }
}


?>