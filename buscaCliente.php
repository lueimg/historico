<?php
header('Content-type: application/json');
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);
include ("../../clases/class.HistoricoCliente.php");
$telefono=$_POST["telefonoCliente"];
$codcliatis=$_POST["codigoClienteATIS"];
$codsercms=$_POST["codigoServicioCMS"];
$codclicms=$_POST["codigoClienteCMS"];
$obj = new HistoricoCliente();
$arrcliente = $obj->getCliente($telefono, $codcliatis, $codsercms, $codclicms);
$deb = 1;
if (!is_array($arrcliente))  {   // No hay clientes 
	$arrcliente[0]["encontrado"] = 0;
	echo json_encode($arrcliente);
	exit;
	
}

$posibleCritico = $obj->esPosibleCritico("fono", $telefono) ;
$arrPosibleCritico = array("posibleCritico", $posibleCritico);

$arrcliente[0]["posibleCritico"] = $posibleCritico;
$arrcliente[0]["encontrado"] = 1;

//array_push($arrcliente, $arrPosibleCritico);
//echo $posibleCritico; die();

//echo '{"cantidad":"'.count($arrcliente).'",{"clientes":'.json_encode($arrcliente) .'}}';
echo json_encode($arrcliente);

?>