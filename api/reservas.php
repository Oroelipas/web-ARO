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

	$idActividad = $_POST["idActividad"];
	$fecha = $_POST["fecha"];
	$hora = $_POST["hora"];
	$idUser = $_POST["idUsuario"];

	$diaSemana =  diaSemana(date("w", strtotime($fecha)));

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

	$IdReserva = $_POST["idReserva"];
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

	$monday = date('Y-m-d', strtotime('monday this week'));

	$query = "SELECT r.idReserva, a.idactividad, a.hora, a.horaFin, a.diasemana, r.fecha, a.nombre,  m.nombre as monitor
			  FROM reservas r, actividades a, monitores m
			  WHERE r.idusuario = ? and 
			  		r.idactividad = a.idactividad and
			  		r.hora = a.hora and
			  		r.diasemana = a.diasemana and 
			  		m.idmonitor = a.idmonitor and
			  		? < CONCAT(r.fecha, ' ', r.hora)";

	$result = $db->executeSql($query, [$idUser, $monday]);
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	return;

}


function nuevaReservaSemanal(){

	$db = new Conexion();

	$idActividad = $_POST["idActividad"];
	$diaSemana = $_POST["diaSemana"];
	$hora = $_POST["hora"];
	$idUser = $_POST["idUsuario"];

	// Checkear que la reserva no esta hecha
	$query = "SELECT * from reservas_programadas WHERE idusuario = ? and idactividad = ? and hora = ? and diasemana = ?";
	$result = $db->executeSql($query, [$idUser, $idActividad, $hora, $diaSemana]);
	if(count($result) > 0){
		$result = ["idReservaSemanal"=> null, "error" => 409];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	// Checkear que la actividad existe
	$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
	$result = $db->executeSql($query, [$idActividad, $hora, $diaSemana]);
	if(count($result) == 0){
		$result = ["idReservaSemanal"=> null, "error" => 4041];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	// Checkear que el usuario existe
	$query = "SELECT * from usuarios WHERE idusuario = ?";
	$result = $db->executeSql($query, [$idUser]);
	if(count($result) == 0){
		$result = ["idReservaSemanal"=> null, "error" => 4042];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "INSERT INTO `reservas_programadas` (idusuario, idactividad, hora, diasemana) VALUES (".$idUser.", ".$idActividad.",'".$hora."','".$diaSemana."');";

	$db->executeSql($query);

	$idReserva = $db->lastInsertId();

	$result = ["idReservaSemanal" => $idReserva, "error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}


function anularReservaSemanal(){

	$db = new Conexion();

	$IdReserva = $_POST["idReservaSemanal"];
	$idUser = $_POST["idUsuario"];

	// Checkear que la reserva exista y sea de ese usuario
	$query = "SELECT * from reservas_programadas WHERE idreservaprogramada = ? and idusuario = ?";
	$result = $db->executeSql($query, [$IdReserva, $idUser]);
	if(count($result) == 0){
		$result = ["error" => 404];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}

	$query = "DELETE FROM reservas_programadas WHERE idreservaprogramada = ? and idusuario = ?";
	$db->executeSql($query, [$IdReserva, $idUser]);
	$result = ["error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}




function misReservasSemanales(){

	$db = new Conexion();

	$idUser = $_POST["idUsuario"];

	$query = "SELECT r.idreservaprogramada, a.idactividad, a.hora, a.horaFin, a.diasemana, a.nombre,  m.nombre as monitor
			  FROM reservas_programadas r, actividades a, monitores m
			  WHERE r.idusuario = ? and 
			  		r.idactividad = a.idactividad and
			  		r.hora = a.hora and
			  		r.diasemana = a.diasemana and 
			  		m.idmonitor = a.idmonitor";

	$result = $db->executeSql($query, [$idUser]);
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	return;

}

function confirmarRecordatorioSemanal(){

	$db = new Conexion();

	$idReservaSemanal = $_POST["idReservaSemanal"]; // id de la reserva semanal (programada o recurrente) que queremos convertir en reserva

	$idReserva = null;
	$nombreAct = null;
	
	$timeZone = new DateTimeZone('Europe/Madrid');
	// Obtenemos la fecha de mañana en el formato de la BD
	$fecha = date_format(date_create(NULL, $timeZone)->add(new DateInterval('P1D')), "Y-m-d");

	// Checkear que existe la reserva programada, obtener su información
	$query = "SELECT * from reservas_programadas WHERE idreservaprogramada = ?";
	$result = $db->executeSql($query, [$idReservaSemanal]);
	if(count($result) == 0){
		// No existe esa reserva programada
		$result = ["error" => 404, "idReserva" => $idReserva, "nombreAct" => $nombreAct, "fecha" => $fecha];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;		
	}

	foreach ($result as $indice => $recordatorio) {

		$recordatorio = (array) $recordatorio;

		$idUser = $recordatorio["idusuario"];
		$idActividad = $recordatorio["idactividad"];
		$hora = $recordatorio["hora"];
		$diaSemanaR = $recordatorio["diasemana"];

		$timeZone = new DateTimeZone('Europe/Madrid');
		// Obtenemos la fecha de MAÑANA en el formato de la BD
		$fechaNoFormat = date_create(NULL, $timeZone)->add(new DateInterval('P1D'));
		$fecha = date_format($fechaNoFormat, "Y-m-d");

		// Obtenemos el día de la semana de MAÑANA
		$numDiaSemana = date_format($fechaNoFormat, "N");

		switch($numDiaSemana){
	    	case 1:
	        	$diaSemana = "L";
	        	break;
	    	case 2:
	        	$diaSemana = "M";
	        	break;
	    	case 3:
	       	 	$diaSemana = "X";
	        	break;
	        case 4:
	       	 	$diaSemana = "J";
	        	break;
	        case 5:
	       	 	$diaSemana = "V";
	        	break;
	        case 6:
	       	 	$diaSemana = "S";
	        	break;
	        case 7:
	       	 	$diaSemana = "D";
	        	break;
		}

		if ($diaSemana != $diaSemanaR) {
			// Significa que, a pesar de haber recibido la notificación con un día de antelación, el usuario está reservando el propio día que tiene lugar la actividad semanal
			$fecha = date_format(date_create(NULL, $timeZone)->sub(new DateInterval('P1D')), "Y-m-d"); // Debemos corregir el valor de la variable $fecha
		}

		// Obtener los datos de la actividad a reservar
		$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
		$actividad = $db->executeSql($query, [$idActividad, $hora, $diaSemanaR]);
		foreach ($actividad as $indice => $datos_actividad) {
			$datos_actividad = (array) $datos_actividad;
			$nombreAct = $datos_actividad["nombre"];
		}

		// Checkear que la reserva no está hecha (pura prevención, no debería ocurrir si las notificaciones se eliminan correctamente en el dispositivo una vez se clicka en RESERVAR)
		$query = "SELECT * from reservas WHERE idusuario = ? and idactividad = ?  and fecha = ? and hora = ? and diasemana = ?";
		$result = $db->executeSql($query, [$idUser, $idActividad, $fecha, $hora, $diaSemanaR]);
		if(count($result) > 0){
			// La reserva ya existe
			$result = ["error" => 409, "idReserva" => $idReserva, "nombreAct" => $nombreAct, "fecha" => $fecha];
			echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			return;
		}

		/* Realizamos la reserva */
		$query = "INSERT INTO `reservas` (idusuario, idactividad, fecha, hora, diasemana) VALUES (".$idUser.", ".$idActividad.",'".$fecha."','".$hora."','".$diaSemanaR."');";
		$db->executeSql($query);
		$idReserva = $db->lastInsertId();

	}

	$result = ["error" => null, "idReserva" => $idReserva, "nombreAct" => $nombreAct, "fecha" => $fecha];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

?>