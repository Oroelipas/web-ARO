<?php

function sendPushNotifications(){

	$db = new Conexion();
	$firebaseCM = new FirebaseCM();

	$fecha = date_create(NULL);
	$fechaActual = date_format($fecha, "Y-m-d");
	$horaActual = date_format($fecha, "h:i:s");
	$horaActualMas1 = date_format($fecha->add(new DateInterval('PT1H')), "h:i:s");
	$horaActualMas2 = date_format($fecha->add(new DateInterval('PT2H')), "h:i:s");

	// Obtenemos los usuarios almacenados en la BD
	$query = "SELECT * from usuarios";
	$users = $db->executeSql($query);

	$numPushSent = 0;
	$numErrors = 0;

	if(count($users) > 0){

		while ($user = $users->fetch(PDO::FETCH_ASSOC)) {

			// Obtenemos las reservas que tiene programadas hoy el usuario en la próxima hora (se optimizará en un futuro)
			$query = "SELECT * from reservas_programadas WHERE idusuario = ? and fecha = ? and hora >= ? and hora < ?";
			$reservas = $db->executeSql($query, [$user["idUsuario"], $fechaActual, $horaActualMas1, $horaActualMas2]);
			if(count($reservas) > 0){

				// Si hay alguna que vaya a tener lugar en la próxima hora
				while ($reserva = $reservas->fetch(PDO::FETCH_ASSOC)) {
					$query = "SELECT * from actividades WHERE idactividad = ?";
					$actividad = $db->executeSql($query, [$reserva["idactividad"]]);
					if(count($actividad) > 0){

						/*
						// Notificamos al usuario a través de Firebase Cloud Messaging
						if(firebase->notifyUser($user["tokenFCM"], $actividad["nombre"], $actividad["hora"]) == -1) {
							// No se ha podido enviar correctamente la notificación
							$numErrors = $numErrors + 1;
						} else {
							// Se ha podido enviar correctamente la notificación
							$numPushSent = $numPushSent + 1;
						}
						*/
						
						$numPushSent = $numPushSent + 1;
					}
				}
			}
		}

		$result = ["date" => '$fechaActual', "interval" => "'$horaActualMas1' to '$horaActualMas2'","numPushSent" => '$numPushSent', "numErrors" => '$numErrors'];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

	} else {

		$result = ["date" => '$fechaActual', "interval" => "'$horaActualMas1' to '$horaActualMas2'", "numPushSent" => '$numPushSent', "numErrors" => '$numErrors'];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

	}
}

?>