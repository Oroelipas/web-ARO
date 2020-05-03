<?php


function nuevoUsuario(){

	$db = new Conexion();

	$nombre = $_POST["nombre"];
	$email = $_POST["email"];
	$idCarrera = $_POST["idCarrera"];
	$password = $_POST["password"];
	$fNacimiento = $_POST["fNacimiento"];
	$sexo = $_POST["sexo"];

	$hash = password_hash($password, PASSWORD_DEFAULT);

	// Checkear que el email no esta usado
	$query = "SELECT * from usuarios WHERE email = ?";
	$result = $db->executeSql($query, [$email]);
	if(count($result) > 0){
		$result = ["idusuario" => null, "error" => 409];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "INSERT INTO `usuarios` (email, nombre, hash, nacimiento, idcarrera, sexo) VALUES ('".$email."', '".$nombre."','".$hash."','".$fNacimiento."',".$idCarrera.",'".$sexo."');";


	$db->executeSql($query);

	$result = ["idusuario" =>  $db->lastInsertId(), "error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}

function actualizarTokenFB(){

	$db = new Conexion();

	$idusuario = $_POST["idUsuario"];
	$tokenFB = $_POST["tokenFB"];

	$query = "UPDATE usuarios SET tokenFB = ? WHERE idusuario = ?";
	$result = $db->executeSql($query, [$tokenFB, $idusuario]);

	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function login(){

	$db = new Conexion();

	$email = $_POST["email"];
	$password = $_POST["password"];

	$query = "SELECT u.idusuario, u.email, u.nombre, u.hash, u.nacimiento, c.nombre as carrera, u.sexo
				FROM usuarios u, carreras c
				WHERE u.idcarrera = c.idcarrera AND 
					  email = ?";

	$result = $db->executeSql($query, [$email]);
	if(count($result) > 0){
		if(password_verify($password, $result[0]->hash)){
			$result = (array)$result[0];
			unset($result["hash"]); // no enviamos el hash de vuelta
			$result["error"] = null;
			echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			return;
		}
	}

	$result = ["idusuario" => null, "error" => 404];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

?>