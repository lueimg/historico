<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");

$action = $_POST["action"];

if (isset($_POST["deshabilitar_tecnico"])) {
	$idTecnico = $_POST["idtecnico"];

	$tecnico = new TecnicosCriticos();
	if ( $tecnico->Deshabilitar($idTecnico)=="1" ) 
		echo "ok";
	else
		echo "error";
}

if (isset($_POST["habilitar_tecnico"])) {
	$idTecnico = $_POST["idtecnico"];
	$tecnico = new TecnicosCriticos();
	if ( $tecnico->Habilitar($idTecnico)=="1" ) 
		echo "ok";
	else
		echo "error";


}elseif($action == "filtro_empresa")
{
    extract($_POST);
    $_SESSION["filtro_tec"] = array("tipo"=>"filtro_empresa","idempresa"=> $idempresa );
    print "ok";
}elseif($action == "filtro_celula")
{
    extract($_POST);
    $_SESSION["filtro_tec"] = array("tipo"=>"filtro_empresa","idempresa"=> $idempresa , "idcelula"=>$idcelula );
    print "ok";
}elseif($action == "filtro_busqueda")
{
    extract($_POST);
    $_SESSION["filtro_tec"] = array("tipo"=>$tipo,"busqueda"=> trim($busqueda) );
    print "ok";
}elseif($action == "filtro_principal")
{
    extract($_POST);
    $_SESSION["filtro_tec"] = array("tipo"=>$filtro );
    print "ok";
}elseif($action == "reiniciar_filtros")
{
    unset($_SESSION["filtro_tec"]);
    print "ok";
}

?>
