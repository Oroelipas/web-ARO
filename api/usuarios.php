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

	$_SESSION["user"] = $db->lastInsertId();

	$result = ["IdUser" => $_SESSION["user"], "error" => null];
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}


function login(){

	$db = new Conexion();

	$email = $_POST["email"];
	$hash = $_POST["hash"];

	$query = "SELECT idusuario from usuarios WHERE email = ? and hash = ?";
	$result = $db->executeSql($query, [$email, $hash]);
	if(count($result) > 0){
		$_SESSION["user"] = $result[0]->idusuario;
		$result = ["IdUser" => $result[0]->idusuario, "error" => null];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}else{
		$result = ["IdUser" => null, "error" => 404];
		echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
}

function logout(){
	unset($_SESSION["user"]);
}


?>