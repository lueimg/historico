<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include ("../../cabecera.php");

//include ($RUTA."clases/class.Usuario.php");
include ("../../clases/class.Usuario.php");
$usuario = new Usuario();

$accion = $_POST["accion"];

if (isset($_POST["deshabilitar_usuario"])) {
	$idUsuario = $_POST["idusuario"];

	$usuario = new Usuario();
	if ( $usuario->Deshabilitar($idUsuario)=="1" ) 
		echo "ok";
	else
		echo "error";
}

if (isset($_POST["habilitar_usuario"])) {
	$idUsuario = $_POST["idusuario"];
	$usuario = new Usuario();
	if ( $usuario->Habilitar($idUsuario)=="1" ) 
		echo "ok";
	else
		echo "error";
}elseif($accion == "crearUsuario")
{

    if($_POST[id] == "")
    {
        $json = $usuario->crearUsuario($_POST);

    }else{
        $json = $usuario->editarUsurio($_POST);
    }

    print $json;
    exit();


}elseif($accion == "getSubmodulosJSON")
{
    $json = $usuario->getSubmodulosJSON();
    print $json;
    exit();

}elseif($accion == "getDataUsuarioAll")
{
    $id = $_POST["idusuario"];
    $json = $usuario->getDataUsuarioAll($id);
    print $json;
    exit();

}elseif($accion == "registrarFiltros")
{   extract($_POST);
    $_SESSION["MantUsuFilter"]= array("filter"=>$filter, "value"=>$value);
    exit(1);

}elseif($accion == "reiniciarFiltros")
{
    unset($_SESSION["MantUsuFilter"]);
    exit(1);

}

?>
