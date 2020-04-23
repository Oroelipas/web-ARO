<?php

function sendPushNotifications(){

	$db = new Conexion();
	$firebaseCM = new FirebaseCM();

	// Creamos un objeto DateTime en la zona horaria adecuada, el parametro NULL nos da la fecha actual junto con la hora
	$timeZone = new DateTimeZone('Europe/Madrid');
	$fecha = date_create(NULL, $timeZone);
	
	// Obtenemos el número del día de la semana en el que nos encontramos, nos devuelve un entero en el rango 1-7
	$numDiaSemanaActual = date_format($fecha, "N");

	// Obtenemos la letra del día de la semana que corresponde con hoy
	switch ($numDiaSemanaActual) {
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

	// Obtenemos la letra del día de la semana que corresponde con mañana
	$numDiaSemanaActualMas1 = $numDiaSemanaActual + 1;

	switch ($numDiaSemanaActualMas1) {
    	case 2:
        	$diaSemanaMas1 = "M";
        	break;
    	case 3:
       	 	$diaSemanaMas1 = "X";
        	break;
        case 4:
       	 	$diaSemanaMas1 = "J";
        	break;
        case 5:
       	 	$diaSemanaMas1 = "V";
        	break;
        case 6:
       	 	$diaSemanaMas1 = "S";
        	break;
        case 7:
       	 	$diaSemanaMas1 = "D";
        	break;
        case 8:
        	$diaSemanaMas1 = "L";
        	break;
	}

	// Obtenemos la fecha actual en el formato de la BD
	$fechaActual = date_format($fecha, "Y-m-d");

	// Tenemos que asegurar el intervalo XX+1:XX:00 <= reserva < XX+2:XX:00, siendo XX:XX:00 la hora actual
	$hActual = date_format($fecha, "H");
	$mActual = date_format($fecha, "i");
	$horaActual = date_format($fecha->setTime($hActual, $mActual, 0), "H:i:s");		// Aseguramos que los segundos están a 0
	$horaActualMas1 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s"); 	// actual + 1h
	$horaActualMas2 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s"); 	// actual + 2h

	// Obtenemos la fecha de mañana en el formato de la BD
	$fechaActualMas1 = date_format(date_create(NULL, $timeZone)->add(new DateInterval('P1D')), "Y-m-d");

	$numPushSent = 0;
	$numErrors = 0;

	/* Notificaciones de aviso de comienzo de actividad */

	$tipoNotificacion = 1; // De ahora en adelante enviamos notificaciones de tipo 1 con FCM

	// Obtenemos las reservas no recurrentes en menos de 2 horas
	$query = "SELECT * from reservas WHERE fecha = ? and hora >= ? and hora < ?";
	$reservas = $db->executeSql($query, [$fechaActual, $horaActualMas1, $horaActualMas2]);
	if(count($reservas) > 0){

		// Si hay alguna reserva no recurrente que vaya a tener lugar en ese intervalo
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

	/* Notificaciones de recordatorio de reserva semanal (o recurrente) */

	$tipoNotificacion = 2; // De ahora en adelante enviamos notificaciones de tipo 2 con FCM

	// Obtenemos las reservas recurrentes que van a tener lugar en >= 24 horas y < 25 horas
	$query = "SELECT * from reservas_programadas WHERE diasemana = ? and hora >= ? and hora < ?";
	$reservas = $db->executeSql($query, [$diaSemanaMas1, $horaActual, $horaActualMas1]);
	if(count($reservas) > 0){

		// Si hay alguna reserva recurrente que vaya a tener lugar en ese intervalo
		foreach ($reservas as $indice => $reserva) {

			$reserva = (array) $reserva;

			// Obtenemos los datos de la actividad asociada a la reserva recurrente
			$query = "SELECT * from actividades WHERE idactividad = ? and hora = ? and diasemana = ?";
			$actividad_reservada = $db->executeSql($query, [$reserva["idactividad"], $reserva["hora"], $reserva["diasemana"]]);
			if(count($actividad_reservada) > 0){

				$actividad_reservada = (array) $actividad_reservada;

				foreach ($actividad_reservada as $indice => $actividad) {

					$actividad = (array) $actividad;

					// Obtenemos los datos del usuario asociado a la reserva recurrente
					$query = "SELECT * from usuarios WHERE idusuario = ?";
					$user_reserva = $db->executeSql($query, [$reserva["idusuario"]]);

					$user_reserva = (array) $user_reserva;

					foreach ($user_reserva as $indice => $user) {

						$user = (array) $user;
						
						// Comprobamos que no exista una reserva de esa actividad recurrente MAÑANA para ese usuario (ya que avisamos con 1 día de antelación)
						$query = "SELECT * from reservas WHERE idactividad = ? and idusuario = ? and fecha = ? and hora = ?";
						$existe_reserva = $db->executeSql($query, [$actividad["idactividad"], $user["idusuario"], $fechaActualMas1, $actividad["hora"]]);
						if(count($existe_reserva) == 0){

							// Notificamos al usuario a través de Firebase Cloud Messaging en caso de que no exista una reserva para MAÑANA de la actividad
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
	}

	$result = ["date" => $fechaActual, "time" => $horaActual, "interval" => "from $horaActualMas1 to $horaActualMas2 (not included)","numPushSent" => $numPushSent, "numErrors" => $numErrors];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

?>