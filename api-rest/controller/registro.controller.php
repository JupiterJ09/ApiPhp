<?php
date_default_timezone_set('America/Mexico_City');


class RegistroController{
   
     /* ---------- POST /registro ---------- */
     public function postRegistro(array $datos)
     {
         header('Content-Type: application/json');
 
         /* 1. Validaciones */
         if (!isset($datos['nombre']) ||
             !preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/', $datos['nombre'])) {
             http_response_code(422);
             echo json_encode(["detalle" => "Nombre inválido. Solo letras y espacios."]);
             exit;
         }
 
         if (!isset($datos['apellido']) ||
             !preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/', $datos['apellido'])) {
             http_response_code(422);
             echo json_encode(["detalle" => "Apellido inválido. Solo letras y espacios."]);
             exit;
         }
 
         if (!isset($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
             http_response_code(422);
             echo json_encode(["detalle" => "Email inválido o ausente."]);
             exit;
         }
 
         /* 2. Email único */
         $clientes = ModeloClientes::getClientes('clientes');
         foreach ($clientes as $c) {
             if ($c->email === $datos['email']) {          // si cambias a FETCH_ASSOC → $c['email']
                 http_response_code(409);
                 echo json_encode(["detalle" => "El email ya está registrado."]);
                 exit;
             }
         }
 
         /* 3. Preparar datos */
         $id  = str_replace('$','c', crypt($datos['nombre'].$datos['apellido'].$datos['email'], '$324134gfh$fgsdgdsfterbgdfg$'));
         $key = str_replace('$','a', crypt($datos['nombre'].$datos['apellido'].$datos['email'], '$324134gfh$fgsdgdsfterbgdfg$'));
 
         $now = date('Y-m-d H:i:s');
         $personal = [
             'nombre'        => $datos['nombre'],
             'apellido'      => $datos['apellido'],
             'email'         => $datos['email'],
             'id_cliente'    => $id,
             'llave_secreta' => $key,
             'created_at'    => $now,
             'updated_at'    => $now
         ];
 
         /* 4. Insertar */
         $creado = ModeloClientes::createCliente('clientes', $personal);
 
         if ($creado) {
             http_response_code(201);
             echo json_encode([
                 "detalle"      => "Cliente registrado correctamente.",
                 "id_cliente"   => $id,
                 "llave_secreta"=> $key
             ]);
         } else {
             http_response_code(500);
             echo json_encode(["detalle" => "Error al registrar el cliente."]);
         }
         exit;
     }
 


    public function getRegistro(){  
        $getClient = ModeloClientes::getClientes('clientes');
        echo json_encode(["detalle" => $getClient]);
        exit;
    }

    public function putRegistro(){
        echo json_encode(["detalle" => "estas en la vista de put registro con controlador"]);
        exit;
        
    }

    public function deleteRegistro(){
        echo json_encode(["detalle" => "estas en la vista de delete registro con controlador"]);
        exit;
    }
}



?>