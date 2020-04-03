<?php


function diaSemana($dia){
	switch ($dia) {
		case '1':
			return 'L';
			break;
		case '2':
			return 'M';
			break;
		case '3':
			return 'X';
			break;
		case '4':
			return 'J';
			break;
		case '5':
			return 'V';
			break;
		default:
			echo "ERROR: actividad en diaSemana=". $dia;
			break;
	}
}

function reservar(){

	$db = new Conexion();

	$idActividad = $_POST["IdActividad"];
	$fecha = $_POST["fecha"];
	$hora = $_POST["hora"];
	$idUser = $_POST["idUsuario"];

	$diaSemana =  diaSemana(date("w", strtotime($fecha)));
	$fecha =  date("Y-m-d", strtotime($fecha));

	// Checkear que la reserva no esta hecha
	$query = "SELECT * from reservas WHERE idusuario = ? and idactividad = ?  and fecha = ? and hora = ? and diasemana = ?";
	$result = $db->executeSql($query, [$idUser, $idActividad, $fecha, $hora, $diaSemana]);
	if(count($result) > 0){
		$result = ["idReserva"=> null, "error" => 409];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	// Checkear que la actividad existe
	$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
	$result = $db->executeSql($query, [$idActividad, $hora, $diaSemana]);
	if(count($result) == 0){
		$result = ["idReserva"=> null, "error" => 4041];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	// Checkear que el usuario existe
	$query = "SELECT * from usuarios WHERE idusuario = ?";
	$result = $db->executeSql($query, [$idUser]);
	if(count($result) == 0){
		$result = ["idReserva"=> null, "error" => 4042];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "INSERT INTO `reservas` (idusuario, idactividad, fecha, hora, diasemana) VALUES (".$idUser.", ".$idActividad.",'".$fecha."','".$hora."','".$diaSemana."');";


	$db->executeSql($query);

	$idReserva = $db->lastInsertId();

	$result = ["idReserva" => $idReserva, "error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}



function anular(){

	$db = new Conexion();

	$IdReserva = $_POST["IdReserva"];
	$idUser = $_POST["idUsuario"];


	// Checkear que la reserva exista y sea de ese usuario
	$query = "SELECT * from reservas WHERE idReserva = ? and idUsuario = ?";
	$result = $db->executeSql($query, [$IdReserva, $idUser]);
	if(count($result) == 0){
		$result = ["error" => 404];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "DELETE FROM reservas WHERE idReserva = ? and idUsuario = ?";
	$db->executeSql($query, [$IdReserva, $idUser]);
	$result = ["error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

function misReservas(){

	$db = new Conexion();

	$idUser = $_POST["idUsuario"];

	$query = "SELECT r.idReserva, a.idactividad, a.hora, a.diasemana, r.fecha, a.nombre  
			  FROM reservas r, actividades a 
			  WHERE r.idusuario = ? and 
			  		r.idactividad = a.idactividad and
			  		r.hora = a.hora and
			  		r.diasemana = a.diasemana and 
			  		NOW() < CONCAT(r.fecha, ' ', r.hora)";


	/// APAÑO PARA QUE SIMULE ESTAR SIEMPRE EN LA UNICA SEMANA EN LA QUE TENEMOS DATOS Y RESERVAS: 03/02/2020 - 07-02-2020


	$diaSemana = date("w");
	// $diaFake = dia de febrero en el que simularemos estar
	switch ($diaSemana) {
		case '1':
			$diaFake =  '2020-02-03';
			break;
		case '2':
			$diaFake = '2020-02-04';
			break;
		case '3':
			$diaFake = '2020-02-05';
			break;
		case '4':
			$diaFake = '2020-02-06';
			break;
		case '5':
			$diaFake = '2020-02-07';
			break;
		case '6':
		case '7':
			$diaFake = '2020-02-02'; // para que si estamos en fin de semana nos devuelva todas las de la semana siguiente
			break;
	}

	$dateFake = $diaFake ." ". date("H:i:s");

	$query = "SELECT r.idReserva, a.idactividad, a.hora, a.diasemana, r.fecha, a.nombre  
			  FROM reservas r, actividades a 
			  WHERE r.idusuario = ? and 
			  		r.idactividad = a.idactividad and
			  		r.hora = a.hora and
			  		r.diasemana = a.diasemana and 
			  		'".$dateFake."' < CONCAT(r.fecha, ' ', r.hora)";

	///////////////////////////////////////////////////////////////////////////////////////////////////////


	$result = $db->executeSql($query, [$idUser]);
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	return;

}

?>