<?php
include "conexion.php";
include "carreras.php";
include "usuarios.php";
include "reservar.php";

session_start();

$verb = $_SERVER['REQUEST_METHOD'];
$url = explode("/", $_SERVER["REQUEST_URI"]);
$url = strtolower($url[sizeof($url) - 1]);


switch($url){
    case "carreras":
        if($verb == "GET"){
            getCarreras();
        }
        break;
    case "nuevousuario":
    	if($verb == "POST"){
    		nuevoUsuario();
    	}
        break;
    case "login":
        if($verb == "POST"){
            login();
        }
        break;
    case "logout":
        if($verb == "POST"){
            logout();
        }
        break;
    case "reservar":
        if($verb == "POST"){
            reservar();
        }
        break;
    case "actividades":
        echo file_get_contents("jsonActividades.json");
        break;
    default:
        echo "LLamada incorrecta";
}


?>