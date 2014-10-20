<?php

require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");

$accion = $_REQUEST["accion"];

$tecnico = new TecnicosCriticos();
$mostrarEnExcel = $_GET["excel"];

if($mostrarEnExcel == 1 ) {

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
    <table border="1">
        <tr><th colspan="2">Empresa:</th><td colspan="4"><?=$_GET["empresa"]?></td></tr>
        <tr><th colspan="2">Celula:</th><td colspan="4"><?=$_GET["celula"]?></td></tr>
        <tr><th colspan="2">Fechas:</th><td colspan="4"><?=$_GET["fecha"] . $ffin ?></td></tr>
    </table>
<?php


}

if($accion == "MostrarAsistencia")
{   $deb = 1;
    $ids_tecnicos = $_REQUEST["ids_tecnicos"];
    $fecha = $_REQUEST["fecha"];
    $data = $tecnico->asistenciaTecnicosCompacto($fecha, $ids_tecnicos , $mostrarEnExcel);
    $deb  = 1;
    print $data;
    exit();

}elseif($accion == "MostrarAsistenciaRangoFechas")
{
    $ids_tecnicos = $_REQUEST["ids_tecnicos"];
    $fecha = $_REQUEST["fecha"];
    $fechaFin = $_REQUEST["fechaFin"];
    $data = $tecnico->MostrarAsistenciaRangoFechas($fecha, $fechaFin, $ids_tecnicos , $mostrarEnExcel);

    echo $data;
    exit();
}