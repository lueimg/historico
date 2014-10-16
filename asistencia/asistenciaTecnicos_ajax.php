<?php

require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");

$accion = $_POST["accion"];

$tecnico = new TecnicosCriticos();

if($accion == "MostrarAsistencia")
{
    $ids_tecnicos = $_POST["ids_tecnicos"];
    $fecha = $_POST["fecha"];
//    print $tecnico->asistenciaTecnicosOfficetrack($fecha, $ids_tecnicos);
    $data = $tecnico->asistenciaTecnicosCompacto($fecha, $ids_tecnicos);
    $deb  = 1;
    print $data;
    exit();

}