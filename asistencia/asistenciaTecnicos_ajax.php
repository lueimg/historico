<?php

require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");

$accion = $_REQUEST["accion"];

$tecnico = new TecnicosCriticos();


if($_GET["excel"] == 1 ) {

    header("Content-Type:  application/x-msexcel");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Disposition: attachment; filename=AsistenciaTecnicos-".$accion . time() .".xls");

    //IMPRIMO CABECERA
    $ffin = "";
    $fechaFin = $_REQUEST["fechaFin"];
    if($fechaFin != ""){
        $ffin = " - ".$fechaFin;
    }
    ?>
    <table>
        <tr><th colspan="2">Empresa:</th><td><?=$_GET["empresa"]?></td></tr>
        <tr><th colspan="2">Celula:</th><td><?=$_GET["celula"]?></td></tr>
        <tr><th colspan="2">Fechas:</th><td><?=$_GET["fecha"] . $ffin ?></td></tr>
    </table>
<?php


}

if($accion == "MostrarAsistencia")
{   $deb = 1;
    $ids_tecnicos = $_REQUEST["ids_tecnicos"];
    $fecha = $_REQUEST["fecha"];
    $data = $tecnico->asistenciaTecnicosCompacto($fecha, $ids_tecnicos);
    $deb  = 1;
    print $data;
    exit();

}elseif($accion == "MostrarAsistenciaRangoFechas")
{
    $ids_tecnicos = $_REQUEST["ids_tecnicos"];
    $fecha = $_REQUEST["fecha"];
    $fechaFin = $_REQUEST["fechaFin"];
    $data = $tecnico->MostrarAsistenciaRangoFechas($fecha, $fechaFin, $ids_tecnicos);

    echo $data;
    exit();
}