<?php
include "conexion.php";
include "carreras.php";
include "usuarios.php";
include "reservas.php";

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
    case "reservar":
        if($verb == "POST"){
            reservar();
        }
        break;
    case "anular":
        if($verb == "POST"){
            anular();
        }
        break;
    case "misreservas":
        if($verb == "POST"){
            misReservas();
        }
        break;
    case "actividades":
        echo file_get_contents("jsonActividades.json");
        break;
    default:
        echo "LLamada incorrecta";
}


?>