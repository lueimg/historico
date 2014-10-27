<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include ("../../cabecera.php");

//include ($RUTA."clases/class.Usuario.php");
include ("../../clases/class.Persona.php");
$persona = new Persona();

$accion = $_POST["accion"];

if ($accion == "cambiarEstado")
{
    $idpersona = $_POST["idpersona"];
    $valor = $_POST["valor"];

  $result=  $persona->cambiarEstado($idpersona,$valor);
    print $result;
    exit();

}elseif($accion == "crearPersona")
{
    if($_POST["id"] == ""){
        $json = $persona->crearPersona($_POST);
    }else{
        $json = $persona->editarPersona($_POST);
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
    $json = $persona->getDataUsuarioAll($id);
    print $json;
    exit();

}elseif($accion == "registrarFiltros")
{   extract($_POST);
    $_SESSION["MantPerFilter"]= array("filter"=>$filter, "value"=>$value);
    exit(1);

}elseif($accion == "reiniciarFiltros")
{
    unset($_SESSION["MantPerFilter"]);
    exit(1);

}

?>
