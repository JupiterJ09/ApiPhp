<?php

require_once "controller/cursos.controller.php";
require_once "controller/rutas.controller.php";
require_once "controller/registro.controller.php";
require_once "Modelos/cliente.modelo.php";
require_once "Modelos/cursos.modelo.php";

$rutas = new controlladorRutas();
$rutas->index();
?>