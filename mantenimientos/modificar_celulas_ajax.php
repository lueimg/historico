<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


require_once("../../../cabecera.php");
require("../clases/class.TecnicosCriticos.php");
require("../clases/class.CelulasCriticos.php");

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
}

if($action == "CrearCelula")
{
    $celula = new CelulasCriticos();
    //obtenemos los datos enviados , idempresa ,nombre , estado
    extract($_POST);
    $save = $celula->CrearCelula($idempresa,$nombre,$estado,$quiebres);
    if($save == 1)
        print "Guardado Correctamente";
    else
        print $save;


}elseif($action == "EditarCelula")
{
    $celula = new CelulasCriticos();
    //obtenemos los datos enviados , idempresa ,nombre , estado
    extract($_POST);
//     die(json_encode($_POST));
    $save = $celula->EditarCelula( $idcelula , $idempresa,$nombre, $estado,$quiebres );
    if($save == 1)
        print "Actualizado Correctamente";
    else
        print $save;
}elseif($action == "filtro_empresa")
{
    extract($_POST);
    $_SESSION["filtro"] = array("tipo"=>"filtro_empresa","idempresa"=> $idempresa );
    print "ok";
}elseif($action == "filtro_nombre")
{
    extract($_POST);
    $_SESSION["filtro"] = array("tipo"=>"filtro_nombre","nombre"=> $nombre );
    print "ok";
}elseif($action == "filtro_principal")
{
    extract($_POST);
    $_SESSION["filtro"] = array("tipo"=>$filtro );
    print "ok";
}elseif($action == "reiniciar_filtros")
{
    unset($_SESSION["filtro"]);
    print "ok";
}

?>
