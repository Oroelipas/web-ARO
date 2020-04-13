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
		$result = ["IdUser" => null, "error" => 409];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		return;
	}


	$query = "INSERT INTO `usuarios` (email, nombre, hash, nacimiento, idcarrera, sexo) VALUES ('".$email."', '".$nombre."','".$hash."','".$fNacimiento."',".$idCarrera.",'".$sexo."');";


	$db->executeSql($query);

	$_SESSION["user"] = $db->lastInsertId();

	$result = ["IdUser" => $_SESSION["user"], "error" => null];
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

	$query = "SELECT idusuario, hash from usuarios WHERE email = ?";
	$result = $db->executeSql($query, [$email]);
	if(count($result) > 0){
		if(password_verify($password, $result[0]->hash)){
			$result = ["IdUser" => $result[0]->idusuario, "error" => null];
			echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			return;
		}
	}

	$result = ["IdUser" => null, "error" => 404];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

?>