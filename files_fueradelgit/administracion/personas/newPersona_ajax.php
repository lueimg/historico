<?php
include_once "../../../clases/class.Conexion.php";

$nombres = addslashes(htmlspecialchars($_POST["nombres"]));
$apellidoP = addslashes(htmlspecialchars($_POST["apellidoP"]));
$apellidoM = addslashes(htmlspecialchars($_POST["apellidoM"]));
$ideecc = addslashes(htmlspecialchars($_POST["empresa"]));
$dni = addslashes(htmlspecialchars($_POST["dni"]));
$celular = addslashes(htmlspecialchars($_POST["celular"]));
$telefono1 = addslashes(htmlspecialchars($_POST["telefono1"]));
$telefono2 = addslashes(htmlspecialchars($_POST["telefono2"]));

$rpm = addslashes(htmlspecialchars($_POST["rpm"]));
$email = addslashes(htmlspecialchars($_POST["email"]));
$categoria = addslashes(htmlspecialchars($_POST["categoria"]));

//$idusuario = $_POST["txt_idusuario"];
$estado = 1;

try {
	$db = new Conexion();
	$cnx = $db->conectarPDO();
	$cnx->beginTransaction();

	$cad1 = "INSERT INTO webpsi.tb_persona (nombre, apellido_p, apellido_m, dni, id_eecc, idcategoria, estado)
		VALUES (?,?,?,?,?,?,?) ";
	$res1 = $cnx->prepare($cad1);

	$res1->bindParam(1, $nombres);
	$res1->bindParam(2, $apellidoP);
	$res1->bindParam(3, $apellidoM);
	$res1->bindParam(4, $dni);
	$res1->bindParam(5, $ideecc);			
	$res1->bindParam(6, $categoria);			
	$res1->bindParam(7, $estado);
	
	$res1->execute();

	$idPersona = $cnx->lastInsertId();	
	$fechaMov = date("Y-m-d H:i:s");
	
	//tipo contacto
	// celular = 1
	// rpm = 2
	// correo = 3
	// fono fijo = 4
	$tipoContactoCelular = "1";
	$tipoContactoRpm = "2";
	$tipoContactoEmail = "3";
	$tipoContactoFijo = "4";
	$porDefecto = "1";
	
	// Insertamos celular
	$cad2 = "INSERT INTO webpsi.tb_persona_contacto (id_persona, id_tipo_contacto, contacto, defecto)
		VALUES (?,?,?,?) ";
	$res2 = $cnx->prepare($cad2);

	$res2->bindParam(1, $idPersona);
	$res2->bindParam(2, $tipoContactoCelular);
	$res2->bindParam(3, $celular);
	$res2->bindParam(4, $porDefecto);
	$res2->execute();
	
	
	$porDefecto = "0";
	
	if (strlen($rpm)>0) {
		// Insertamos rpm
		$cad22 = "INSERT INTO webpsi.tb_persona_contacto (id_persona, id_tipo_contacto, contacto, defecto)
			VALUES (?,?,?,?) ";
		$res22 = $cnx->prepare($cad22);

		$res22->bindParam(1, $idPersona);
		$res22->bindParam(2, $tipoContactoRpm);
		$res22->bindParam(3, $rpm);
		$res22->bindParam(4, $porDefecto);
		$res22->execute();	
	}	
	
	if (strlen($email)>0) {
		// Insertamos rpm
		$cad33 = "INSERT INTO webpsi.tb_persona_contacto (id_persona, id_tipo_contacto, contacto, defecto)
			VALUES (?,?,?,?) ";
		$res33 = $cnx->prepare($cad33);

		$res33->bindParam(1, $idPersona);
		$res33->bindParam(2, $tipoContactoEmail);
		$res33->bindParam(3, $email);
		$res33->bindParam(4, $porDefecto);
		$res33->execute();	
	}	
		
	
	if (strlen($telefono1)>0) {
		// Insertamos telefono1
		$porDefecto = "0";
		$cad2 = "INSERT INTO webpsi.tb_persona_contacto (id_persona, id_tipo_contacto, contacto, defecto)
			VALUES (?,?,?,?) ";
		$res2 = $cnx->prepare($cad2);

		$res2->bindParam(1, $idPersona);
		$res2->bindParam(2, $tipoContactoFijo);
		$res2->bindParam(3, $telefono1);
		$res2->bindParam(4, $porDefecto);
		$res2->execute();
	}

	if (strlen($telefono2)>0) {
		// Insertamos telefono2
		$cad2 = "INSERT INTO webpsi.tb_persona_contacto (id_persona, id_tipo_contacto, contacto, defecto)
			VALUES (?,?,?,?) ";
		$res2 = $cnx->prepare($cad2);

		$res2->bindParam(1, $idPersona);
		$res2->bindParam(2, $tipoContactoFijo);
		$res2->bindParam(3, $telefono2);
		$res2->bindParam(4, $porDefecto);
		$res2->execute();
	}
	
	$cnx->commit();	
	
	echo "1";
}
catch (PDOException $e)
{
	$cnx->rollback();                   # failure
	echo "0|".$e->getMessage() ;
}
$cnx = null;
$db = null;


echo $a;