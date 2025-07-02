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

      

    public function putCursos(){

        
        echo json_encode(["detalle" => "estas en la vista put  cursos con controlador"]);
        exit;
    }
    
    public function deleteCursos(){
        echo json_encode(["detalle" => "estas en la vista  delete cursos con controlador"]);
        exit;
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
                    echo json_encode(["detalle" => $cursos]);
                    exit;
                }
            
            }
        }


    }
}


?>