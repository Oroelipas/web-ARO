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

	// Obtenemos los usuarios almacenados en la BD
	$query = "SELECT * from usuarios";
	$users = $db->executeSql($query);

	$numPushSent = 0;
	$numErrors = 0;

	if(count($users) > 0){

		foreach ($users as $indice => $user) {

			$user = (array) $user;

			// Obtenemos las reservas que tiene programadas hoy el usuario en la próxima hora (se optimizará en un futuro)
			$query = "SELECT * from reservas WHERE idusuario = ? and fecha = ? and hora >= ? and hora < ?";
			$reservas = $db->executeSql($query, [$user["idusuario"], $fechaActual, $horaActualMas1, $horaActualMas2]);
			if(count($reservas) > 0){

				// Si hay alguna que vaya a tener lugar en la próxima hora
				foreach ($reservas as $indice => $reserva) {

					$reserva = (array) $reserva;

					$query = "SELECT * from actividades WHERE idactividad = ?";
					$actividad_reservada = $db->executeSql($query, [$reserva["idactividad"]]);
					if(count($actividad_reservada) > 0){

						$actividad_reservada = (array) $actividad_reservada;

						echo var_dump($actividad_reservada)."\n";

						foreach ($actividad_reservada as $indice => $actividad) {

							// Notificamos al usuario a través de Firebase Cloud Messaging
							if($firebaseCM->notifyUser($user["tokenFCM"], $actividad["nombre"], $actividad["hora"]) == -1) {
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

		$tokenPrueba = 'cXSkRubcTCGjyK2kSUvJju:APA91bErFJ3je9mRRg_ZXQIcMPwKhdaau67cotW9Gz7qXVfiv0cVfQVn7lAEzY24XIT9dQxAP-hOjzcUU4gz-IFDXlA_MKhycirYeQfUXSlNRIQx6H6vOKkFRCYrTY4N-XdrGOlM3Gfr';

		if($firebaseCM->notifyUser($tokenPrueba, "Prueba", "XX:XX:XX") == -1) {
			// No se ha podido enviar correctamente la notificación
			$numErrors = $numErrors + 1;
		} else {
			// Se ha podido enviar correctamente la notificación
			$numPushSent = $numPushSent + 1;
		}

		$result = ["date" => $fechaActual, "interval" => "from $horaActualMas1 to $horaActualMas2 (not included)","numPushSent" => $numPushSent, "numErrors" => $numErrors];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

	} else {

		$result = ["date" => $fechaActual, "interval" => "from $horaActualMas1 to $horaActualMas2 (not included)", "numPushSent" => $numPushSent, "numErrors" => $numErrors];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

	}
}

?>