<?php
include "carreras.php";

$verb = $_SERVER['REQUEST_METHOD'];
$url = explode("/", $_SERVER["REQUEST_URI"]);
$url = $url[sizeof($url) - 1];

switch($url){
    case "carreras":
        getCarreras();
        break;
    default:
        echo "LLamada incorrecta";
}


?>