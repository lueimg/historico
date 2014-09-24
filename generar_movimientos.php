<?php
$PATH =  $_SERVER['DOCUMENT_ROOT']."/webpsi/";
require_once ($PATH."clases/class.Conexion.php");
require_once($PATH.'modulos/historico/clases/empresa.php');
require_once($PATH.'modulos/historico/clases/gestionMovimientos.php');
session_start();

$fecha=date("d/m/Y");
$hora = date("h:i:s");
$hora = substr($hora,0,2)."_".substr($hora,3,2)."_".substr($hora,6,2);
$filename = $fecha."-".$hora;
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_movimientos_$filename.xls");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>PSI - Web SMS - Clientes Críticos</title>
        <meta http-equiv="content-type" content="text/html; charset=utf8">
        <meta name="author" content="Sergio MC" />
<head>
<title>Movimientos</title>
</head>
<body>

<?php
$db = new Conexion();
$cnx = $db->conectarPDO();
$ob_empresa = new Empresa();

$empresas = $_POST["empresa_usuario"];
$ob_cliente = new gestionMovimientos();

$reporte = $_POST["reporte"];
if($reporte == "act"){
	$fecha_ini = $_POST["fecha_ini_act"];
	$fecha_fin = $_POST["fecha_fin_act"];
	if($fecha_ini!="" && $fecha_fin!=""){
		$fecha_ini = substr($fecha_ini, 6,4)."-".substr($fecha_ini, 3,2)."-".substr($fecha_ini, 0,2);
		$fecha_fin = substr($fecha_fin, 6,4)."-".substr($fecha_fin, 3,2)."-".substr($fecha_fin, 0,2);
	}else{
		echo "La Fecha Registro de Actuación de inicio y fin estan vacias o falta colocar una de ellas";
		exit();
	}
}else{
	$fecha_ini = $_POST["fecha_ini_atc"];
	$fecha_fin = $_POST["fecha_fin_atc"];
	if($fecha_ini!="" && $fecha_fin!=""){
		$fecha_ini = substr($fecha_ini, 6,4)."-".substr($fecha_ini, 3,2)."-".substr($fecha_ini, 0,2);
		$fecha_fin = substr($fecha_fin, 6,4)."-".substr($fecha_fin, 3,2)."-".substr($fecha_fin, 0,2);
	}else{
		echo "La Fecha Registro de ATC de inicio y fin estan vacias o falta colocar una de ellas";
		exit();
	}
}

$cliente = $ob_cliente->getCriticoMovimiento($cnx,$empresas,$reporte,$fecha_ini,$fecha_fin);

$table = "<table><tr><td>Id_atc</td><td>Tipo Actuacion</td><td>Averia</td><td>Quiebre</td><td>nombre_contacto</td><td>telefono_contacto</td><td>celular_contacto</td>
<td>observacion</td><td>Fecha Agenda</td><td>horario</td><td>dia</td><td>Empresa</td>
				 <td>Zonal</td><td>Motivo</td><td>Submotivo</td><td>Estado</td><td>Tecnico</td><td>Fecha Regsitro Actuacion</td>
				 <td>Fecha Creacion ATC</td><td>Fecha Creacion Movimiento</td><td>Fecha Consolidacion</td><td>Ultimo Movimiento</td><td>Tipo Averia</td>
				 <td>Averia_m1</td><td>Penalizable</td><td>Desc. Penalizable</td><td>Usuario</td><td>codmotivo_req_catv</td>
				 <td>Fecha_Cambio</td>
</tr>"; 
		
		foreach($cliente as $row){
			$fecha_agenda = (($row["e_id"]==1 || $row["e_id"]==8 || $row["e_id"]==9 || $row["e_id"]==10 || $row["e_id"]==20) && ($row["m_id"]!=5))? $row["fecha_agenda"]:'';
			$horario = (($row["e_id"]==1 || $row["e_id"]==8 || $row["e_id"]==9 || $row["e_id"]==10 || $row["e_id"]==20) && ($row["m_id"]!=5))? $row["horario"]:'';
			$dia = (($row["e_id"]==1 || $row["e_id"]==8 || $row["e_id"]==9 || $row["e_id"]==10 || $row["e_id"]==20) && ($row["m_id"]!=5))? $row["dias"]:'';
			$fecha_consolidacion = ($row["e_id"]==3 || $row["e_id"]==19)? $row["fecha_consolidacion"]:'';

			if($fecha_agenda!=""){
				$fecha_agenda = substr($fecha_agenda, 8,2)."-".substr($fecha_agenda, 5,2)."-".substr($fecha_agenda, 0,4);
			}

			if($fecha_consolidacion!=""){
				$fecha_consolidacion = substr($fecha_consolidacion, 8,2)."-".substr($fecha_consolidacion, 5,2)."-".substr($fecha_consolidacion, 0,4);
			}

			if($row["e_id"]=="3" || $row["e_id"]=="19"){
				$penalizable = ($row["penalizable"]=="")? 'no':'si';
				$desc_penalizable = $row["penalizable"];
			}else{
				$penalizable = "";
				$desc_penalizable = "";
			}

			$fecha_mov = substr($row["fecha_movimiento"], 8,2)."-".substr($row["fecha_movimiento"], 5,2)."-".substr($row["fecha_movimiento"], 0,4)." ".substr($row["fecha_movimiento"], 11,8);
			$fecha_registro = substr($row["fecha_registro"], 8,2)."-".substr($row["fecha_registro"], 5,2)."-".substr($row["fecha_registro"], 0,4);
			$fecha_creacion = substr($row["fecha_creacion"], 8,2)."-".substr($row["fecha_creacion"], 5,2)."-".substr($row["fecha_creacion"], 0,4);

            $table .= "<tr><td>".$row["id_atc"]."</td><td>".$row["tipo_actividad"]."</td><td>".$row["averia"]."</td><td>".$row["quiebre"]."</td><td>".$row["nombre_cliente_critico"]."</td><td>".$row["telefono_cliente_critico"]."</td><td>".$row["celular_cliente_critico"]."</td><td>".$row["observacion"]."</td><td>".
            $fecha_agenda."</td><td>".$horario."</td><td>".$dia."</td><td>".$row["nombre"]."</td><td>".$row["zonal"]."</td><td>".$row["motivo"]."</td><td>".$row["submotivo"]."</td><td>".$row["estado"]."</td><td>".$row["tecnico"]."</td><td>".$fecha_registro."</td><td>".$fecha_creacion.
            "</td><td>".$fecha_mov."</td><td>".$fecha_consolidacion."</td><td>".$row["ultimo_movimiento"]."</td><td>".$row["tipo_averia"]."</td><td>".$row["averia_m1"].
            "</td><td>".$penalizable."</td><td>".$desc_penalizable."</td><td>".$row["usuario"]."</td><td>".$row["codmotivo_req_catv"]."</td><td>".$row["fecha_cambio"]."</td></tr>"; 
        }

		$table .= "</table>";
		echo $table;

?>

</body>
</html>