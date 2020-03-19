<?php


function diaSemana($i){
	switch ($i) {
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
			echo "ERROR: actividad en diaSemana=". $i;
			break;
	}
}

function reservar(){

	$db = new Conexion();

	$idActividad = $_POST["IdActividad"];
	$fecha = $_POST["fecha"];
	$hora = $_POST["hora"];
	$idUser = $_SESSION["user"];

	$diaSemana =  diaSemana(date("w", strtotime($fecha)));
	$fecha =  date("Y-m-d", strtotime($fecha));

	// Checkear que la reserva no esta hecha
	$query = "SELECT * from reservas WHERE idusuario = ? and idactividad = ?  and fecha = ? and hora = ? and diasemana = ?";
	$result = $db->executeSql($query, [$idUser, $idActividad, $fecha, $hora, $diaSemana]);
	if(count($result) > 0){
		$result = ["error" => 409];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "INSERT INTO `reservas` (idusuario, idactividad, fecha, hora, diasemana) VALUES (".$idUser.", ".$idActividad.",'".$fecha."','".$hora."','".$diaSemana."');";


	$db->executeSql($query);

	$result = ["error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

?>