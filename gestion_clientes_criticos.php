<?php
//include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/gestionCriticos.php');
require_once('clases/motivos.php');
require_once('clases/capacidadHorarios.php');
require_once('clases/liquidados.php');
require_once('clases/liquidado_feedback.php');
require_once('clases/tecnicos.php');
require_once('clases/cedula.php');
require_once('clases/solucionesComerciales.php');
require_once('clases/zonales.php');
require_once('agenda/agendaController.php');
//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$id = $_GET['id'];
$indice = $_GET['indice'];
$actividad = $_GET['actividad'];

if($actividad=='Provision'){
	$ob_provision = new gestionCriticos($cnx);
	$cliente_critico = $ob_provision->getGestionCriticoProvision($cnx,$id);
}else if($actividad=='Manual'){
	$ob_manual = new gestionCriticos($cnx);
	$cliente_critico = $ob_manual->getGestionCriticoManual($cnx,$id);
}else if($actividad=='Manual_Provision'){
	$ob_manual = new gestionCriticos($cnx);
	$cliente_critico = $ob_manual->getGestionCriticoManualProvision($cnx,$id);
}else if($actividad=='Averias'){
	$ob_critico = new gestionCriticos($cnx);
	$cliente_critico = $ob_critico->getGestionCritico($cnx,$id);
}

$ob_mov = new gestionMovimientos($cnx);
$gestion_movimiento = $ob_mov->getGestionMovimiento($cnx,$id);

$ob_mot = new Motivos($cnx);
$motivos = $ob_mot->getMotivos($cnx);

$ob_empresa = new Empresa();
$id_empresa = $ob_empresa->getIdEmpresa($cnx,$cliente_critico["eecc_final"]);

$zonales = new Zonales();
$id_zonal = $zonales->getIdZonal($cnx, $cliente_critico["zonal"]);

//Llenando combo de feedback
$ob_feedback = new LiquidadoFeedback();
$feedback = $ob_feedback->getFeedbackAll($cnx);

//tecnico movimiento
$tecult = $ob_mov->getTecnico_UltimoMovimiento($cnx,$id);
$tecnico_movimiento=$tecult['tecnico'];
$idtecnico_movimiento=$tecult['id'];


$ob_cedula = new Cedula();
$cedula = $ob_cedula->getCedulaAll($cnx,$id_empresa);

//Lista de soluciones comerciales
$ob_solcom = new SolucionesComerciales();
$solcomArray = $ob_solcom->getSolucionesAll($cnx);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>       

		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
		<link type="text/css" href='css/horarios.css' rel="Stylesheet" />

		<script type="text/javascript" src="js/jquery.filter_input.js"></script>
        <script type="text/javascript" src="js/criticos.js"></script>
        <script type="text/javascript" src="js/select.js"></script>
        <script type="text/javascript" src="js/jquery.multiselect.min.js"></script>
</head>

<body>
<!--<div id="loading"></div>-->
<div class="modalPop"></div>

<!--<div id="page-wrap">-->

<div class="registro_clientes">
<form name="frm_gestion_critico" id="frm_gestion_critico" action="" method="POST">
	<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
		<div class="datos">
			<div class="content_datos" style="margin:4px 0;">
				<div class="caja_text" style="margin-right:4px;">
					<label class="atc">Código de Atención:</label>
					<input class="border" type="text" disabled value="<?php echo trim($cliente_critico["id_atc"]);?>" maxlength="11" name="h_id_atc" id="h_id_atc">
					<input type="hidden" value="<?php echo trim($cliente_critico["id_atc"]);?>" name="id_atc">
				</div>
				<div class="caja_text" style="margin-right:10px">
					<label>Estado:</label>
					<input class="border" type="text" disabled value="<?php echo trim($cliente_critico["estado"]);?>" 
					name="estado_ini" id="estado_ini">
				</div>
				<div class="caja_text" style="margin-right:10px">
					<label>EECC:</label>
					<input class="border" type="text" disabled value="<?php echo trim($cliente_critico["eecc_final"]);?>" 
					name="contrata" id="contrata">
					<input type="hidden" value="<?php echo $id_empresa;?>" 
					name="id_empresa" id="id_empresa">
				</div>
			</div>
			<div class="content_datos datos_contacto ocultar">
				<div class="caja_large">
					<label class="nombre">Nombre Contacto:</label>
					<textarea class="border" maxlength="255" id="cr_nombre" name="cr_nombre"><?php echo trim($cliente_critico["nombre"]);?></textarea>
				</div>
			</div>
			<div class="content_datos datos_contacto ocultar">
				<div class="caja_medium">
					<label>Telefono:</label>
					<input class="border" type="text" value="<?php echo trim($cliente_critico["telefono_cliente_critico"]);?>" maxlength="11" name="cr_telefono" id="cr_telefono">
				</div>
				<div class="caja_medium">
					<label>Celular:</label>
					<input class="border" type="text" value="<?php echo trim($cliente_critico["celular_cliente_critico"]);?>" maxlength="11" name="cr_celular" id="cr_celular">
				</div>
			</div>
			<div class="content_datos">
				
			</div>
			<div class="content_datos">
				<div class="caja_text" style="margin-right:10px">
					<label>Motivo:</label>
				</div>
				<div class="caja_text">
					<select name="motivo" id="motivo">
						<option value="">Seleccione motivo</option>
						<?php
							foreach ($motivos as $mot):
								if($gestion_movimiento[0]["m_id"]==3){
									if($mot["id"]==2){
						?>
										<option value="<?php echo $mot["id"];?>"><?php echo $mot["motivo"];?></option>
						<?php
									}else{
										//
									}
								}else{
									if($gestion_movimiento[0]["m_id"]!=7 && $gestion_movimiento[0]["m_id"]!=2
									&& $gestion_movimiento[0]["m_id"]!=4 && $gestion_movimiento[0]["m_id"]!=6
									&& $gestion_movimiento[0]["m_id"]!=8){
										//este if es por si el motivo es 5 = Observacion el cual mantiene el estado
										if($gestion_movimiento[0]["id_estado"]==9 || $gestion_movimiento[0]["id_estado"]==10 ||
										   $gestion_movimiento[0]["id_estado"]==20){
						?>
											<option value="<?php echo $mot["id"];?>"><?php echo $mot["motivo"];?></option>
						<?php
										}else{
											if($mot["id"]==3){
												//
											}else{
						?>
											<option value="<?php echo $mot["id"];?>"><?php echo $mot["motivo"];?></option>
						<?php
											}
										}

									}else{
						?>
									<option value="<?php echo $mot["id"];?>"><?php echo $mot["motivo"];?></option>
						<?php
									}
								}
							endforeach;
						?>
					</select>

				</div>
				<div class="caja_text" style="margin-left:10px;margin-right:10px">
					<label>Submotivo:</label>
					<span class="submotivo"></span>
					<input type="hidden" value="" id="h_submotivo" name="h_submotivo">
				</div>
				<div class="caja_text" style="margin-right:10px">
					<label>Estado:</label>
					<span class="estado"></span>
				</div>
			</div>

			<div class="content_datos">
				<div class="caja_text">
					<label>Observacion:</label>
				</div>
				<div class="caja_text">
					<textarea class="border" maxlength="255" id="cr_observacion" name="cr_observacion"></textarea>
				</div>
			</div>
			<div class="caja_text ocultar" style="margin-right:10px" id="mostrar_tecnicos">
				<div class="filtroxcampo">
				  	<label>Celula:*</label>
				  	<select class="slct_cedula" id="slct_cedula" name="slct_cedula" onchange="cargarTecnico('tecnico','<?php echo $idtecnico_movimiento; ?>','slct_cedula','<?php echo $id_empresa; ?>','<?php echo $cliente_critico["quiebre"]; ?>');">
				  	<option value=''>-- Seleccione --</option>
					<?php
						foreach ($cedula as $r):
					?>
						<option value="<?php echo $r["id"];?>"><?php echo $r["nombre"];?></option>
					<?php
						endforeach;
					?>
					</select>
			  	</div><br>
				<label>Técnico:*</label>
				<input type='hidden' id='TecnicoDefault' name='TecnicoDefault' value='<?php echo $tecnico_movimiento; ?>'>
				<input type='hidden' id='IdTecnicoDefault' name='IdTecnicoDefault' value='<?php echo $idtecnico_movimiento; ?>'>
				<input type='hidden' name="nombretecnico" id="nombretecnico">
				<input type='hidden' name="tec_officetrack" id="tec_officetrack">
				<input type='hidden' name="nombretecnico_movimiento" id="nombretecnico_movimiento">
				<select name="tecnico" id="tecnico">								
				<option value=''>-- Seleccione --</option>			
				</select>
				<span class="checkbox">
					<input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico">Tecnico Entregado
				</span>
                <div><span class="labeltecoffice"></span></div>
			</div>

			<div id="liquidaciones" class="liquidado">
				<div class="content_datos">
				<h3>Liquidaciones:</h3>
					<div class="caja_text" style="margin-right:10px">
						<label>Contacto:</label>
					</div>
					<div class="caja_text">
						<input type="radio" name="contacto" value="si" id="contacto1">Con Contacto<br>
						<input type="radio" name="contacto" value="no" id="contacto2">Sin Contacto
					</div>

					<div class="caja_text" style="margin:0 10px 0 10px">
						<label>Pruebas:</label>
					</div>
					<div class="caja_text">
						<input type="radio" name="pruebas" value="si" id="pruebas1">Con Pruebas<br>
						<input type="radio" name="pruebas" value="no" id="pruebas2">Sin Pruebas
					</div>

					<!--<div class="caja_text" style="margin:0 10px 0 10px">
						<label>Crítico:</label>
					</div>
					<div class="caja_text">
						<input type="radio" name="critico" value="si" id="critico1" checked>Crítico<br>
						<input type="radio" name="critico" value="no" id="critico2">No Crítico
					</div>-->
					<div class="caja_text" style="margin:0 10px 0 10px">
						<label>Cumplimiento de Agenda:</label>
					</div>
					
					<div class="caja_text">
						<input type="checkbox" name="h_cump_agenda" value="si" id="h_cump_agenda" disabled 
						<?php echo ($gestion_movimiento[0]["s_id"]==9 && $gestion_movimiento[0]["id_estado"]==9)? 'checked':'';?>> Cumplimiento de Agenda<br>
						<input type="hidden" name="cump_agenda" value="<?php echo ($gestion_movimiento[0]["s_id"]==9 && $gestion_movimiento[0]["id_estado"]==9)? 'si':'no';?>" id="cump_agenda" >
					</div>
				</div>
				<div class="content_datos">
					<div class="caja_text" style="margin:0 10px 0 10px">
						<label>Fecha de Consolidacion:</label>
					</div>
					<div class="caja_text">
						<input type="text" maxlength="12" class="fecha_liquidacion" name="fecha_liquidacion" id="fecha_liquidacion" disabled />
			  			<input type="hidden" name="h_fecha_liquidacion" id="h_fecha_liquidacion" />
					</div>
					<div class="caja_text" style="margin:0 10px 0 10px">
						<label>Feedback:</label>
					</div>
					<div class="caja_text">
						<select name="feedback" id="feedback">
							<?php
								foreach ($feedback as $feed):
									if($feed["id"]=="10"){
							?>
								<option value="<?php echo $feed["id"];?>" selected><?php echo $feed["feedback"];?></option>
							<?php
									}else{
							?>
								<option value="<?php echo $feed["id"];?>"><?php echo $feed["feedback"];?></option>
							<?php		
									}
								endforeach;
							?>
						</select>

					</div>
				</div>
				<div class="content_datos">
					<div class="caja_text">
						<label>Solución</br>Comercial:</label>
					</div>
					<div class="caja_text">
						<!--<textarea class="border" maxlength="255" id="solucion_comercial" name="solucion_comercial"></textarea>-->
                                            <select name="solucion_comercial" id="solucion_comercial">
                                                <?php
                                                foreach ( $solcomArray as $key=>$val ) {
                                                	if($val["nombre"]=="Ninguno"){
                                                    	echo "<option value=\"{$val['nombre']}\" selected>{$val['nombre']}</option>";
                                                	}else{
                                                		echo "<option value=\"{$val['nombre']}\">{$val['nombre']}</option>";
                                                	}
                                                }
                                                ?>
                                            </select>
					</div>
					<div class="filtroxcampo">
					  	<label>Celula:</label>
					  	<select class="slct_cedula2" id="slct_cedula2" name="slct_cedula2" onchange="cargarTecnico('tecnico_movimiento','<?php echo $idtecnico_movimiento; ?>','slct_cedula2','<?php echo $id_empresa; ?>','<?php echo $cliente_critico["quiebre"]; ?>');">
					  	<option value=''>-- Seleccione --</option>
						<?php
							foreach ($cedula as $r):
						?>
							<option value="<?php echo $r["id"];?>"><?php echo $r["nombre"];?></option>
						<?php
							endforeach;
						?>
						</select>
			  		</div><br>
					<div class="caja_text" style="margin-right:10px">
						<label>Técnico:</label>
						<select name="tecnico_movimiento" id="tecnico_movimiento">
						<option value=''>-- Seleccione --</option>
						</select>
					</div>
					<div><span class="labeltecoffice2"></span></div>
				</div>
				<div class="content_datos">
					<div class="caja_text" style="display:block">
						<label>Penalizable:</label><input type="checkbox" name="ck_penalizable" value="si" id="ck_penalizable" style="margin-left:5px"/> Penalizable		
					</div>
						<div class="caja_text">
							<textarea class="border" maxlength="255" id="penalizable" name="penalizable" disabled></textarea>
					</div>
				</div>
			</div>			
			
			<div class="content_datos">
				<div class="caja_medium movimientos">
				<input type="submit" value="Registrar" name="btn_gestion_critico" id="btn_gestion_critico"/>
				<div class="tmovimientos">
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
										$horario_desc = $mov["horario"];
										$fecha_age = $mov["fecha_agenda"];
										$fecha_agenda = $mov["fecha_agenda"];//esta es para el movimiento no la borres
										$fecha_age = substr($mov["fecha_agenda"], 8,2)."-".substr($mov["fecha_agenda"], 5,2)."-".substr($mov["fecha_agenda"], 0,4);
										$id_dia = $mov["id_dia"];
										$ultimo_estado = $cliente_critico["id_estado"];
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

			<div class="content_datos tabla_horarios" style="display:none">
						<div class="caja_medium">
							
							<div class="horarios">
							<label>Seleccione el Horario:</label><span class="fecha_error"></span>
							<?php
                            $agenda = new agendaController();
                            $agenda->setCnx($cnx);
                            $agenda->setVempresa($id_empresa);
                            $agenda->setVzona($id_zonal);
                            $degb = 1;
                            $agendar_html = $agenda->AgendarAveriaShow();
                            print $agendar_html;
//                            $ob_horario = new capacidadHorarios();
//							$ob_horario->getHorarios($cnx,$empresa,$zonal,$actividad);
							?>
							</div>
						</div>
			</div>
			
			<!--<input type="hidden" value="" id="fecha_agenda_observacion" name="fecha_agenda_observacion">
			
				para el estado observac
			-->
			<input type="hidden" value="<?php echo $cliente_critico['paso_final'];?>" name="paso_final">
			<input type="hidden" value="<?php echo trim($cliente_critico["quiebre"]);?>" name="quiebre" id="quiebre">
			<input type="hidden" value="<?php echo trim($cliente_critico["n_evento"]);?>" name="n_evento" id="n_evento">
			<input type="hidden" value="<?php echo $actividad; ?>" id="actividad" name="actividad">
			<input type="hidden" value="" id="datosfinal" name="datosfinal"> <?php /*indicador si realizará un evento*/ ?>
			<!--<input type="hidden" value="<?php echo $tecnico_movimiento;?>" id="tecnico_movimiento" name="tecnico_movimiento">-->
			<input type="hidden" value="gestion_critico" id="gestion_critico" name="gestion_critico">
			<input type="hidden" value="<?php echo trim($cliente_critico["id"]);?>" name="id_gestion" id="id_gestion">
			<input type="hidden" value="<?php echo $empresa;?>" name="id_empresa">
			<input type="hidden" value="<?php echo $zonal;?>" name="id_zonal">
			<!--Este horario agenda es el inicial lo uso ya que la tabla tiene llaves foraneas en esos campos-->
			<input type="hidden" value="<?php echo $fecha_agenda;?>" id="fecha_agenda_ini" name="fecha_agenda_ini">
			<input type="hidden" value="<?php echo $id_horario;?>" id="horario_agenda_ini" name="horario_agenda_ini">
			<input type="hidden" value="<?php echo $horario_desc;?>" id="horario_desc" name="horario_desc">

			<input type="hidden" value="<?php echo $id_dia;?>" id="dia_agenda_ini" name="dia_agenda_ini">
			<input type="hidden" value="<?php echo $id;?>" id="idfila" name="idfila">
			<input type="hidden" value="<?php echo $indice;?>" id="indice" name="indice">
			<input type="hidden" value="<?php echo $ultimo_estado;?>" id="ultimo_estado" name="ultimo_estado">
			<input type="hidden" name="nombre_critico" value="<?php echo trim($cliente_critico["nombre"]);?>" id="nombre_critico">
			<input type="hidden" name="telefono_critico" value="<?php echo trim($cliente_critico["telefono_cliente_critico"]);?>" id="telefono_critico">
			<input type="hidden" name="celular_critico" value="<?php echo trim($cliente_critico["celular_cliente_critico"]);?>" id="celular_critico">
			<input type="hidden" value="<?php echo trim($cliente_critico["flag_tecnico"]);?>" id="ult_flag_tecnico" name="ult_flag_tecnico">

		</div>
	</form>
</div>
</body>
</html>
