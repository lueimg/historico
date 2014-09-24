<?php
/**
 * Created by PhpStorm.
 * User: lmori
 * Date: 18/09/14
 * Time: 03:35 PM
 */

include_once "../../clases/class.Conexion.php";
require_once('clases/cedula.php');
require_once('clases/tecnicos.php');

date_default_timezone_set("America/Lima");
//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();


//PROCESOS AJAX

//procesa la peticion de carga de cedulas
if(  !empty($_POST["idempresa"]) )
{

    $idempresa = $_POST["idempresa"];
    $cedula = new Cedula();
    $cedula->setCnx($cnx);
    $cedula->setIdempresa($idempresa);


    $data = $cedula->getCedulaAllByEmpresaSelectOptions();
    print json_encode(array("html"=>$data, "id"=>$idempresa));

}elseif(  !empty($_POST["idcedula"]) )
{

    $idcedula = $_POST["idcedula"];
    $tecnico = new Tecnicos();
    $data = $tecnico->getTecnicosAllxCedulaSelectOptions($cnx,$idcedula);

    if(!empty($data))
        print json_encode(array("html"=>$data));
    else
        print json_encode(array("html"=>""));


}