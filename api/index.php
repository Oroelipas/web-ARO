<?php
include "conexion.php";
include "carreras.php";
include "usuarios.php";
include "reservas.php";
include "actividades.php";
include "fcm.php";
include "push.php";

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
        if($verb == "POST"){
            getActividades();
        }        
        break;
    case "nuevareservasemanal":
        if($verb == "POST"){
            nuevaReservaSemanal();
        }        
        break;   
    case "anularreservasemanal":
        if($verb == "POST"){
            anularReservaSemanal();
        }        
        break;
    case "misreservassemanales":
        if($verb == "POST"){
            misReservasSemanales();
        }        
        break;
    case "sendpushnotifications":
        if($verb == "GET"){
        	sendPushNotifications();
        }
        break;
    case "actualizartokenfb":
        if($verb == "POST"){
        	actualizarTokenFB();
        }
        break;
    default:
        echo "LLamada incorrecta";
}

?>