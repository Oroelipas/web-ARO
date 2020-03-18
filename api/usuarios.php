<?php


function nuevoUsuario(){

	$db = new Conexion();

	$nombre = $_POST["nombre"];
	$email = $_POST["email"];
	$idCarrera = $_POST["idCarrera"];
	$hash = $_POST["hash"];
	$fNacimiento = $_POST["fNacimiento"];
	$sexo = $_POST["sexo"];


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

	$result = ["IdUser" => $db->lastInsertId(), "error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}


?>