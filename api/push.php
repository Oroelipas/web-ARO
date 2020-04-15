<?php

function sendPushNotifications(){

	$db = new Conexion();
	$firebaseCM = new FirebaseCM();

	$timeZone = new DateTimeZone('Europe/Madrid');
	$fecha = date_create(NULL, $timeZone);
	$fechaActual = date_format($fecha, "Y-m-d");

	$diaSemanaActual = date_format($fecha, "N");

	switch ($diaSemanaActual) {
    	case 1:
        	$diaSemanaActual = "L";
        	break;
    	case 2:
        	$diaSemanaActual = "M";
        	break;
    	case 3:
       	 	$diaSemanaActual = "X";
        	break;
        case 4:
       	 	$diaSemanaActual = "J";
        	break;
        case 5:
       	 	$diaSemanaActual = "V";
        	break;
        case 6:
       	 	$diaSemanaActual = "S";
        	break;
        case 7:
       	 	$diaSemanaActual = "D";
        	break;
	}

	$horaActual = date_format($fecha->sub(new DateInterval('PT1S')), "H:i:s"); 		// Necesario quitar 1s para calibrar las horas en punto
	$horaActualMas1 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s"); 	// actual + 1h
	$horaActualMas2 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s"); 	// actual + 2h

	$numPushSent = 0;
	$numErrors = 0;

	$tipoNotificacion = 1; // De ahora en adelante enviamos notificaciones de tipo 1 con FCM

	// Obtenemos las reservas no recurrentes en menos de 2 horas
	$query = "SELECT * from reservas WHERE fecha = ? and hora >= ? and hora < ?";
	$reservas = $db->executeSql($query, [$fechaActual, $horaActualMas1, $horaActualMas2]);
	if(count($reservas) > 0){

		// Si hay alguna reserva no recurrente que vaya a tener lugar en menos de 2 horas
		foreach ($reservas as $indice => $reserva) {

			$reserva = (array) $reserva;

			// Obtenemos los datos de la actividad asociada a la reserva
			$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
			$actividad_reservada = $db->executeSql($query, [$reserva["idactividad"], $reserva["hora"], $reserva["diasemana"]]);
			if(count($actividad_reservada) > 0){

				$actividad_reservada = (array) $actividad_reservada;

				foreach ($actividad_reservada as $indice => $actividad) {

					$actividad = (array) $actividad;

					// Obtenemos los datos del usuario asociado a la reserva
					$query = "SELECT * from usuarios WHERE idusuario = ?";
					$user_reserva = $db->executeSql($query, [$reserva["idusuario"]]);

					$user_reserva = (array) $user_reserva;

					foreach ($user_reserva as $indice => $user) {

						$user = (array) $user;

						// Notificamos al usuario a través de Firebase Cloud Messaging
						if($firebaseCM->notifyUser($tipoNotificacion, $user["tokenFB"], $actividad["nombre"], $actividad["hora"], -1) == -1) {
							// No se ha podido enviar correctamente la notificación
							$numErrors = $numErrors + 1;
						} else {
							// Se ha podido enviar correctamente la notificación
							$numPushSent = $numPushSent + 1;
						}
					}
				}
			}
		}
	}

	$tipoNotificacion = 2; // De ahora en adelante enviamos notificaciones de tipo 2 con FCM

	// Obtenemos las reservas recurrentes en menos de 2 horas
	$query = "SELECT * from reservas_programadas WHERE diasemana = ? and hora >= ? and hora < ?";
	$reservas = $db->executeSql($query, [$diaSemanaActual, $horaActualMas1, $horaActualMas2]);
	if(count($reservas) > 0){

		// Si hay alguna reserva recurrente que vaya a tener lugar en menos de 2 horas
		foreach ($reservas as $indice => $reserva) {

			$reserva = (array) $reserva;

			// Obtenemos los datos de la actividad asociada a la reserva
			$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
			$actividad_reservada = $db->executeSql($query, [$reserva["idactividad"], $reserva["hora"], $diaSemanaActual]);
			if(count($actividad_reservada) > 0){

				$actividad_reservada = (array) $actividad_reservada;

				foreach ($actividad_reservada as $indice => $actividad) {

					$actividad = (array) $actividad;

					// Obtenemos los datos del usuario asociado a la reserva
					$query = "SELECT * from usuarios WHERE idusuario = ?";
					$user_reserva = $db->executeSql($query, [$reserva["idusuario"]]);

					$user_reserva = (array) $user_reserva;

					foreach ($user_reserva as $indice => $user) {

						$user = (array) $user;

						// Notificamos al usuario a través de Firebase Cloud Messaging
						if($firebaseCM->notifyUser($tipoNotificacion, $user["tokenFB"], $actividad["nombre"], $actividad["hora"], $reserva["idreservaprogramada"]) == -1) {
							// No se ha podido enviar correctamente la notificación
							$numErrors = $numErrors + 1;
						} else {
							// Se ha podido enviar correctamente la notificación
							$numPushSent = $numPushSent + 1;
						}
					}
				}
			}
		}
	}

	/*
	$token = "fQLLh7sETIiroKuPU7Szbv:APA91bFBgkSozhMgC8NKSTRTG_wOr4_gDClqvPzcP8cfPvRb-qexPFf7MXRs813PuiMYhwmWAP-Tp4dEGWP2MTpZaBeu8HW7jLTDC4Rl_oRp3RucJTm7oOZltrVsObv0gx6gGWWI9HSv";
	
	$firebaseCM->notifyUser($tipoNotificacion, $token, "Prueba", "00:00:00", 20);

	$tipoNotificacion = 1;
	$firebaseCM->notifyUser($tipoNotificacion, $token, "Prueba", "00:00:00", -1);
	*/

	$result = ["date" => $fechaActual, "time" => $horaActual, "interval" => "from $horaActualMas1 to $horaActualMas2 (not included)","numPushSent" => $numPushSent, "numErrors" => $numErrors];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

?>