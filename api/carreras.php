<?php
include "conexion.php";

function getCarreras(){

	$pdo = new Conexion();
	$result = $pdo->executeSql( 'Select * from carreras');
	echo json_encode($result,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}
/*
function getCarreras(){

	$config = parse_ini_file('config.ini');


	$conn = mysqli_connect($config['servername'],  $config['username'],  $config['password'], $config["dbname"]);
	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "Select * from carreras";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while($row = mysqli_fetch_assoc($result)) {
	        echo  $row["idcarrera"]. " - " . $row["nombre"]."<br>";
	    }
	} else {
	    echo "0 results";
	}
	mysqli_close($conn);
}
*/

?>