<?php
include "conexion.php";
include "carreras.php";
include "usuarios.php";

$verb = $_SERVER['REQUEST_METHOD'];
$url = explode("/", $_SERVER["REQUEST_URI"]);
$url = strtolower($url[sizeof($url) - 1]);


switch($url){
    case "carreras":
        getCarreras();
        break;
    case "nuevousuario":
    	if($verb == "POST"){
    		nuevoUsuario();
    	}
        break;
    default:
        echo "LLamada incorrecta";
}


?>