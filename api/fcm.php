<?php

class FirebaseCM {

	public function notifyUser($tokenFCM, $nombreActividad, $horaActividad) {
		
		define('FIREBASE_API_KEY', 'AAAAqkSH2XU:APA91bHWe0wVzfNK6b95MsbgrtTGZbgFfBpm4PrKqUYKgw_yZd6o-iFX5wV43LpIZEZdcmD55V_kMKk9of7JVsCHmfyQxASYb4jd2RnbhNRZiTU9WMNT-RhEXUisTvWVpWqQ2ISNmVMS');

		$url = 'https://fcm.googleapis.com/fcm/send';

		$headers = array(
			'Authorization: key='.FIREBASE_API_KEY,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$push = array(
			'title' => "Recordatorio",
			'message' => "La actividad ".$nombreActividad." comienza a las ".$horaActividad,
			null
		);


		$fields = array(
			'registration_ids' => array (
				$tokenFCM
			),
			'data' => $push
		);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		$result = curl_exec($ch);

		if ($result == FALSE) {
			echo "Error enviando notificación push\n";
			return -1;
		} else {
			curl_close($ch);
			return $result;
		}
	}
}
	
?>