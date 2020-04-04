<?php

function getActividades(){

	$fechaInicio = $_POST["fechaInicio"];


	$date = strtotime($fechaInicio);


	$dayofweek = date('w', $date);

	$dayofweek = $dayofweek==6 ? -1 : $dayofweek; // es para que a partir del sabado muestre la siguiente semana

	$json = file_get_contents("jsonActividades.json");

	$json = str_replace("##DIA1##", date('Y-m-d', strtotime((1 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA2##", date('Y-m-d', strtotime((2 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA3##", date('Y-m-d', strtotime((3 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA4##", date('Y-m-d', strtotime((4 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA5##", date('Y-m-d', strtotime((5 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA6##", date('Y-m-d', strtotime((6 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##DIA7##", date('Y-m-d', strtotime((7 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##FECHAANTERIOR##", date('Y-m-d', strtotime((-6 - $dayofweek).' day', $date)), $json);
	$json = str_replace("##FECHASIGUIENTE##", date('Y-m-d', strtotime((8 - $dayofweek).' day', $date)), $json);


	echo $json;
}


?>