<?php

function sendPushNotifications(){

	$db = new Conexion();
	$firebaseCM = new FirebaseCM();

	$timeZone = new DateTimeZone('Europe/Madrid');
	$fecha = date_create(NULL, $timeZone);
	$fechaActual = date_format($fecha, "Y-m-d");
	$horaActual = date_format($fecha, "H:i:s");
	$horaActualMas1 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s");
	$horaActualMas2 = date_format($fecha->add(new DateInterval('PT1H')), "H:i:s");

	$numPushSent = 0;
	$numErrors = 0;

	// Obtenemos las reservas programadas en menos de 2 horas
	$query = "SELECT * from reservas WHERE fecha = ? and hora >= ? and hora < ?";
	$reservas = $db->executeSql($query, [$fechaActual, $horaActualMas1, $horaActualMas2]);
	if(count($reservas) > 0){

		// Si hay alguna reserva que vaya a tener lugar en menos de 2 horas
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
						if($firebaseCM->notifyUser($user["tokenFB"], $actividad["nombre"], $actividad["hora"]) == -1) {
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

	$result = ["date" => $fechaActual, "interval" => "from $horaActualMas1 to $horaActualMas2 (not included)","numPushSent" => $numPushSent, "numErrors" => $numErrors];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

?>