<?php
//include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/gestionCriticos.php');
require_once('clases/motivos.php');
require_once('clases/capacidadHorarios.php');
require_once('clases/liquidados.php');
require_once('clases/liquidado_feedback.php');
//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();
$id = $_GET['id'];
$indice = $_GET['indice'];

$ob_critico = new gestionCriticos($cnx);
$cliente_critico = $ob_critico->getGestionCritico($cnx,$id);
$ob_mov = new gestionMovimientos($cnx);
$gestion_movimiento = $ob_mov->getGestionMovimiento($cnx,$id);
$ob_mot = new Motivos($cnx);
$motivos = $ob_mot->getMotivos($cnx);

$ob_empresa = new Empresa();
$id_empresa = $ob_empresa->getIdEmpresa($cnx,$cliente_critico["eecc_zona"]);

//Llenando combo de feedback
$ob_feedback = new LiquidadoFeedback();
$feedback = $ob_feedback->getFeedbackAll($cnx);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>       
        <script type="text/javascript" src="js/jquery.filter_input.js"></script>
        <script type="text/javascript" src="js/criticos.js"></script>

		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
		<link type="text/css" href='css/horarios.css' rel="Stylesheet" />

		<link type="text/css" href='css/demo_page.css' rel="Stylesheet" />
		<link type="text/css" href='css/demo_table.css' rel="Stylesheet" />
</head>

<body>
<!--<div id="loading"></div>-->
<div class="modalPop"></div>

<!--<div id="page-wrap">-->

<div class="registro_clientes">
<form name="frm_gestion_critico" id="frm_gestion_critico" action="" method="POST">
	<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
		<div class="datos">
			<div class="content_datos">
				<div class="caja_medium movimientos">
				<div class="tmovimientos" style="height:auto !important;overflow:hidden;">
						<table id="movimientos" style="width:780px">
							<thead>
							<tr><th>Fecha Movimiento</th><th>Actividad</th><th>Zonal</th><th>Nombre Contacto</th>
							<th>Telefono Contacto</th><th>Celular Contacto</th><th>Fecha Agenda</th>
							<th>Motivo</th><th>Submotivo</th><th>Estado</th><th>Técnico</th><th>Observacion</th>
							<th>Usuario</th></tr>
							</thead>
							<tbody>
							<?php
								$c=0;
								foreach ($gestion_movimiento as $mov):

									if($mov["fecha_agenda"]!=''){
										$fecha_age = substr($mov["fecha_agenda"], 8,2)."-".substr($mov["fecha_agenda"], 5,2)."-".substr($mov["fecha_agenda"], 0,4);
									}else{
										$fecha_age = "";
									}
									if($mov["fecha_movimiento"]!=''){
										$fecha = substr($mov["fecha_movimiento"], 8,2)."-".substr($mov["fecha_movimiento"], 5,2)."-".substr($mov["fecha_movimiento"], 0,4)." ";
										$fecha .= substr($mov["fecha_movimiento"],11);
									}else{
										$fecha = "";
									}
									if($c==0):
										$empresa = $mov["nombre"];
										$zonal = $mov["abreviatura"];
										$id_horario = $mov["id_horario"];
										$fecha_age = $mov["fecha_agenda"];
										$fecha_age = substr($mov["fecha_agenda"], 8,2)."-".substr($mov["fecha_agenda"], 5,2)."-".substr($mov["fecha_agenda"], 0,4);
										$id_dia = $mov["id_dia"];
									endif;
							?>
							<tr><td><?php echo $fecha;?></td>
							<td><?php echo $mov["tipo_actividad"]?></td>
							<td><?php echo $mov["zonal"]?></td>
							<td><?php echo $mov["nombre_cliente_critico"]?></td>
							<td><?php echo $mov["telefono_cliente_critico"]?></td>
							<td><?php echo $mov["celular_cliente_critico"]?></td>
							<td><?php echo ($mov["id_estado"]=="1" || $mov["id_estado"]=="9" || $mov["id_estado"]=="10"
							 || $mov["id_estado"]=="20" || $mov["id_estado"]=="8" )? $fecha_age."/".$mov["horario"]:'';?></td>
							<td><?php echo $mov["motivo"]?></td>
							<td><?php echo $mov["submotivo"]?></td><td><?php echo $mov["estado"]?></td>
							<td><?php echo $mov["tecnico"]?></td>
							<td><?php echo $mov["observacion"]?></td>
							<td><?php echo $mov["usuario"]?></td></tr>
							<?php
								$c++;
								endforeach;
							?>
							</tbody>
						</table>
				</div>
				</div>
			</div>


		</div>
	</form>
</div>
