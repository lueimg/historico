<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include ("../../cabecera.php");

//include ($RUTA."clases/class.Usuario.php");
include ("../../clases/class.Grupo.php");
$obj = new Grupo();

$accion = $_POST["accion"];

if ($accion == "cambiarEstado")
{
    $idExistente = $_POST["idExistente"];
    $valor = $_POST["valor"];

    $result=  $obj->cambiarEstado($idExistente,$valor);
    print $result;
    exit();

}elseif($accion == "InsertarRegistro")
{
    if($_POST["id"] == ""){
        $json = $obj->InsertarRegistro($_POST);
    }else{
        $json = $obj->EditarRegistro($_POST);
    }

    print $json;
    exit();

}elseif($accion == "getDataRegistro")
{
    $id = $_POST["idExistente"];
    $json = $obj->getDataRegistro($id);
    print $json;
    exit();

}elseif($accion == "registrarFiltros")
{   extract($_POST);
    $_SESSION["MantGruFilter"]= array("filter"=>$filter, "value"=>$value);
    exit(1);

}elseif($accion == "reiniciarFiltros")
{
    unset($_SESSION["MantGruFilter"]);
    exit(1);

}

?>
