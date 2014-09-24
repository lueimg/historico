<?php

/*
 * php que despliega el popup  GESTION HORARIOS
 * Creado en Telefonica San Felipe
 *
 * Revisiones y cambios
 * |------------------|-------------------|--------------------------------------------------|
 * |____Fecha_________|_Responsable_______|___Concepto/descripcion___________________________|
 * |  17/09/14          Luis Mori             Exportar en excel filtro de reiterados         |
 * |  18/09/14          luis mori               agragar filtros faltantes                    |
 * |------------------|---------------------|------------------------------------------------|
 *
 * */

include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/reporte.php');
require_once('clases/quiebre.php');
require_once('clases/empresa.php');
require_once('clases/cedula.php');
require_once('clases/tecnicos.php');





date_default_timezone_set("America/Lima");
$usuario = $_SESSION["exp_user"]["id"];
//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$ob_quiebre = new Quiebre();
$quiebre = $ob_quiebre->getQuiebre($cnx,$usuario);

$ob_reporte = new Reporte();
$horas=$ob_reporte->ListarHorarios($cnx);
$cantidadHoras=count($horas);

$finicio=date("Y-m-d",strtotime("-1 day",strtotime(date("Y-m-d"))));
$ffin=date("Y-m-d",strtotime("+1 day",strtotime(date("Y-m-d"))));

//filtros fechas
if($_POST['fecha_ini']!='' and $_POST['fecha_fin']!='')
{
    $finicio=$_POST['fecha_ini'];
    $ffin=$_POST['fecha_fin'];
}

//filtro quiebres
$filtro_quiebre='';
if(count($_POST['slct_quiebre'])>0)
{
    $filtro_quiebre=implode("','",$_POST['slct_quiebre']);
}




if(!empty($_POST["slct_tecnico"]))
{
    $ids_tecnico = implode(",",$_POST['slct_tecnico']);
    $js = "window.tecnicos = '$ids_tecnico'";
}


if(!empty($_POST["slct_celula"]))
{
    $idcedula = $_POST["slct_celula"];
    $tecnico = new Tecnicos();
    $options_tecnicos_html = $tecnico->getTecnicosAllxCedulaSelectOptions($cnx,$idcedula,$ids_tecnico);
}


//Si existe empresa , llena el select cedeula con los correspondintes
//si existe cedula , lo pondra como seleccionado
if(!empty($_POST["slct_empresa"]))
{
    $idempresa = $_POST["slct_empresa"];
    $cedula = new Cedula();
    $cedula->setCnx($cnx);
    $cedula->setCnx($cnx);
    $cedula->setIdempresa($idempresa);
    $options_cedula_html_options = $cedula->getCedulaAllByEmpresaSelectOptions($idcedula);
}


$horarios=$ob_reporte->ListarHorariosTecnicos($cnx,$finicio,$ffin,
    compact("filtro_quiebre","idempresa","idcedula","ids_tecnico"));


//LLENANDO FILTROS
//EMPRESA
$empresa = new Empresa();
$empresa->setCnx($cnx);
$empresa_options = $empresa->getEmpresaAllSelectOptions($idempresa);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Gestion de Horarios</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="author" content="Jorge Salcedo" />

    <?php include ("../../includes.php") ?>
    <script type="text/javascript" src="js/jquery.filter_input.js"></script>
    <script type="text/javascript" src="js/prettify.js"></script>
    <script type="text/javascript" src="js/jquery.multiselect.min.js"></script>
    <script type="text/javascript" src="js/jquery.multiselect.filter.js"></script>
    <script type="text/javascript" src="js/horarios_tecnicos.js"></script>
    <script type="text/javascript" src="js/reporte.js"></script>

    <link type="text/css" href='css/jquery.multiselect.css' rel="Stylesheet" />
    <link type="text/css" href='css/jquery.multiselect.filter.css' rel="Stylesheet" />
    <link type="text/css" href='css/estilo.css' rel="Stylesheet" />
    <link type="text/css" href='css/horarios_tecnicos.css' rel="Stylesheet" />


</head>

<body>
<div class="modalPop"></div>


<div class="registro_clientes">
<form name="frm_horarios_tecnicos" id="frm_horarios_tecnicos" action="" method="POST">
<div class="content_datos">
    <br>
    <div class="tit">
        <label class="titulo">Empresa:</label>
        <select class="slct_empresa" id="slct_empresa" name="slct_empresa">
            <option value=''>-- Todos --</option>
            <?= $empresa_options ?>

        </select>
    </div>
    <div class="tit">
        <label class="titulo" >Celula:</label>
        <select class="slct_celula" id="slct_celula" name="slct_celula">
            <option value=''>-- Todos --</option>
            <?= $options_cedula_html_options ?>
        </select>
    </div>

    <div class="tit">
        <label class="titulo">Tecnico:</label>
        <select class="slct_tecnico" id="slct_tecnico" name="slct_tecnico[]" multiple="multiple">
            <?= $options_tecnicos_html ?>
        </select>
    </div>
    <div class="tit">
        <label class="titulo">Quiebre:</label>
        <select class="slct_quiebre" id="slct_quiebre" multiple="multiple" name="slct_quiebre[]">
            <?php
            foreach ($quiebre as $quiebre_1):
                $selected="";
                if(in_array($quiebre_1['id'], $_POST['slct_quiebre'])){
                    $selected="selected";

                }
                ?>
                <option value="<?php echo $quiebre_1["id"];?>" <?=$selected;?> > <?php echo $quiebre_1["nombre"];?></option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
    <div class="tit">
        <label class="titulo">Fecha Reg</label>
        <input type="hidden" id="fecha_i" value="<?=$finicio;?>">
        <input type="hidden" id="fecha_f" value="<?=$ffin;?>">
        <input type="text" maxlength="17" class="buscarFecha" name="fecha_ini" id="fecha_ini" readonly />
        <input type="text" maxlength="17" class="buscarFecha" name="fecha_fin" id="fecha_fin" readonly />
    </div>
</div>
<div class="filtroxcampo">
    <input type='submit' class="btn_buscar_gestion_horarios" id="btn_buscar_gestion_horarios" name="btn_buscar_gestion_horarios" value=".::Buscar::.">
            <span style="margin-left: 20px" class="btn_buscar exportar_excel">
                <img src="img/excel2007.png" alt="excel" title="Generar Excel" style="vertical-align:middle;"
                     width="3%" >
                <a href="#">Exportar en excel</a>
            </span>
</div>
<br>
<table>
    <?php
    $fila1=array();
    $fila2=array();
    foreach ($horas as $r) {
        $fila1[]="<td>".$r['horario']."</td>";
        $fila2[]="<td class='td'>T".$r['id']."</td>";
    }
    echo "<tr>";
    echo "<th>Descripcicion</th>";
    echo implode('',$fila1);
    echo "</tr>";
    echo "<tr>";
    echo "<th>Codigo</th>";
    echo implode('',$fila2);
    echo "</tr>";
    ?>
</table>
<br>
<div>

    <div id="tabla_horario_tecnico">
        <table>
            <tr>
                <th rowspan="2" class="input-xlarge">Tecnicos</th>
                <?php
                $fini=$finicio;
                while($fini<=$ffin){
                    echo "<th colspan='5'>".$fini."</th>";
                    $fini=date("Y-m-d",strtotime("+1 day",strtotime($fini)));
                }
                ?>
            </tr>
            <tr>
                <?php
                $fini=$finicio;
                $rangofechas=0;
                while($fini<=$ffin){
                    $rangofechas++;
                    echo "<th>Tot</th>";
                    foreach ($horas as $r) {
                        echo "<th>T".$r['id']."</th>";
                    }
                    $fini=date("Y-m-d",strtotime("+1 day",strtotime($fini)));
                }
                ?>
            </tr>
            <?php
            if(isset($_POST['btn_buscar_gestion_horarios']) and count($horarios)>0){
                $tec="";
                foreach ($horarios as $r) {
                    if($r['tecnico']!=$tec){	// cuando se registra al tecnico por primera vez y no repetir su registro

                        if($tec!=''){ // cuando cierra la vuelta y el tecnico ya existe- llenar los campos vacios despues de su ultima fecha
                            while($fultimo<=$ffin){
                                echo "	<td class='td'>0</td>";
                                echo "	<td class='td'>0</td>";
                                echo "	<td class='td'>0</td>";
                                echo "	<td class='td'>0</td>";
                                echo "	<td class='td'>0</td>";
                                $fultimo=date("Y-m-d",strtotime("+1 day",strtotime($fultimo)));
                            }
                            echo "</tr>";
                        }

                        echo "<tr>";
                        echo "	<td style='text-aling:left' class='input-xlarge'>".$r['tecnico']."</td>";

                        $salir="";
                        //echo "	<td>".$r['fecha_agenda']."</td>";
                        $fini=$finicio;
                        while($fini<=$ffin){
                            if($r['fecha_agenda']==$fini){
                                echo "	<td class='td'>".$r['total']."</td>";

                                $buscar=array('Tecnico Asignado','Tecnico Entregado',' ');
                                $reemplazar=array('A','E','');
                                $estados=explode("|",str_replace($buscar, $reemplazar, $r['estados']));

                                foreach ($horas as $i => $h) {
                                    # code...
                                    $detalle="0";
                                    $canthorasa=array();
                                    $canthorase=array();
                                    $canthorasl=array();
                                    for($j=0;$j<count($estados);$j++){
                                        $d=explode("_",$estados[$j]);

                                        if($d[2]==$h['id'] and $d[1]=="A"){
                                            $canthorasa[$i]++;
                                        }
                                        elseif($d[2]==$h['id'] and $d[1]=="E"){
                                            $canthorase[$i]++;
                                        }
                                        elseif($d[2]==$h['id'] and $d[1]=="L"){
                                            $canthorasl[$i]++;
                                        }

                                    }

                                    if($canthorase[$i]*1>0 and $canthorasa[$i]*1>0){
                                        $detalle=$canthorase[$i]."E";
                                        $detalle.="<hr>".$canthorasa[$i]."A";
                                    }
                                    elseif($canthorase[$i]*1>0){
                                        $detalle=$canthorase[$i]."E";
                                    }
                                    elseif($canthorasa[$i]*1>0){
                                        $detalle=$canthorasa[$i]."A";
                                    }

                                    if($canthorasl[$i]*1>0){
                                        $detalle=$canthorasl[$i]."L<hr>".$detalle;
                                    }



                                    echo "	<td class='td'>".$detalle."</td>";
                                }


                                $fultimo=date("Y-m-d",strtotime("+1 day",strtotime($fini)));
                                break;
                            }
                            else{
                                echo "	<td class='td'>0</td>";
                                for($i=1;$i<=$cantidadHoras;$i++){
                                    echo "	<td class='td'>0</td>";
                                }
                            }
                            $fini=date("Y-m-d",strtotime("+1 day",strtotime($fini)));
                        }
                        $tec=$r['tecnico'];
                    }
                    else{ // cuando se repite el mismo tecnico pasar defrente a la validacion de sus fechas restantes desde la ultima fecha registrada.
                        while($fultimo<=$ffin){
                            if($r['fecha_agenda']==$fultimo){
                                echo "	<td class='td'>".$r['total']."</td>";

                                $buscar=array('Tecnico Asignado','Tecnico Entregado',' ');
                                $reemplazar=array('A','E','');
                                $estados=explode("|",str_replace($buscar, $reemplazar, $r['estados']));


                                foreach ($horas as $i => $h) {
                                    # code...
                                    $detalle="0";
                                    $canthorasa=array();
                                    $canthorase=array();
                                    $canthorasl=array();
                                    for($j=0;$j<count($estados);$j++){
                                        $d=explode("_",$estados[$j]);

                                        if($d[2]==$h['id'] and $d[1]=="A"){
                                            $canthorasa[$i]++;
                                        }
                                        elseif($d[2]==$h['id'] and $d[1]=="E"){
                                            $canthorase[$i]++;
                                        }
                                        elseif($d[2]==$h['id'] and $d[1]=="L"){
                                            $canthorasl[$i]++;
                                        }

                                    }

                                    if($canthorase[$i]*1>0 and $canthorasa[$i]*1>0){
                                        $detalle=$canthorase[$i]."E";
                                        $detalle.="<hr>".$canthorasa[$i]."A";
                                    }
                                    elseif($canthorase[$i]*1>0){
                                        $detalle=$canthorase[$i]."E";
                                    }
                                    elseif($canthorasa[$i]*1>0){
                                        $detalle=$canthorasa[$i]."A";
                                    }

                                    if($canthorasl[$i]*1>0){
                                        $detalle=$canthorasl[$i]."L<hr>".$detalle;
                                    }


                                    echo "	<td class='td'>".$detalle."</td>";
                                }
                                $fultimo=date("Y-m-d",strtotime("+1 day",strtotime($fultimo)));
                                break;
                            }
                            else{
                                echo "	<td class='td'>0</td>";
                                for($i=1;$i<=$cantidadHoras;$i++){
                                    echo "	<td class='td'>0</td>";
                                }
                            }
                            $fultimo=date("Y-m-d",strtotime("+1 day",strtotime($fultimo)));
                        }
                    }// fin de la validacion por Tecnico - Fecha

                } // fin del FOR
                while($fultimo<=$ffin){ // registrando los campos restantes
                    echo "	<td class='td'>0</td>"; //impresion del total
                    for($i=1;$i<=$cantidadHoras;$i++){
                        echo "	<td class='td'>0</td>";
                    }
                    $fultimo=date("Y-m-d",strtotime("+1 day",strtotime($fultimo)));
                }
                echo "</tr>";
            }
            else{
                echo "<tr><td class='td' colspan='".(($rangofechas*($cantidadHoras+1))+1)."'><font size='+1'><b>No se encontraron registros</b></font></td></tr>";
            }
            ?>
        </table>


    </div>
</div>
</form>
</div>
<script>
    $().ready(function(){



        //selectes en variables
        var slct_empresa = $("#slct_empresa");
        var slct_cedula = $("#slct_celula");


        if(slct_empresa.val() == ""){
            //PRIMERA CARGA DE LA PAGINA
            $("#slct_celula").attr("disabled","true");
        }
        //priemra carga de select tecnico
        $slct_tecnico = $("#slct_tecnico").multiselect().multiselectfilter();

        if(slct_cedula.val() == ""){
            //PRIMERA VEZ QUE CARGA
            $slct_tecnico.multiselect("disable");
        }else
        {
            //CARGA LUEGO DE UN SUBMIT
//            console.log(window.tecnicos);
        }


        $("#slct_empresa").change(function(){

            if($(this).val())
            {
                $("#slct_celula").removeAttr("disabled");
                $("#slct_tecnico .added").remove()
                $("#slct_tecnico").multiselect('refresh');
                $slct_tecnico.multiselect("disable");
            }else
            {
                //DESABILITAR TODAS LAS OPCIONES
                $("#slct_celula").attr("disabled","true");
                $("#slct_tecnico .added").remove()
                $("#slct_tecnico").multiselect('refresh');
                $slct_tecnico.multiselect("disable");


            }

            $("#slct_celula").multiselect('refresh');

        });




        $("#slct_celula").change(function(){
            console.log($(this).val());
            console.log("cambio cedula");
            if($(this).val())
            {
                $slct_tecnico.multiselect("enable");
            }else
            {
                $("#slct_tecnico .added").remove()
                $("#slct_tecnico").multiselect('refresh');
                $slct_tecnico.multiselect("disable");
            }

            $("#slct_tecnico").multiselect('refresh');
            $("#slct_tecnico").multiselect("checkAll");
        });






    });
</script>
<style>
    select,
    .ui-multiselect{
        width: 300px !important;
    }
</style>
</body>
</html>