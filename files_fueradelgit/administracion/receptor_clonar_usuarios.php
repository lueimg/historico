<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//include_once("../conexion_db.php");

include ("../../clases/class.Usuario.php");


if ($_POST['action']=='clonar_usuario')
{
	$idusuario_modelo = $_POST["idusuario_modelo"];
	$nombre = $_POST["nombre"];
	$apellidos = $_POST["apellidos"];
	$login = $_POST["login"];
	$password = $_POST["password"];
	$dni = $_POST["dni"];
	
	$Usuario = new Usuario();
	if ( $Usuario->ClonarPerfil($idusuario_modelo, $nombre, $apellidos, $login, $password, $dni)=="1" ) 
		echo "ok";
	else
		echo "error";
    
}



?>
