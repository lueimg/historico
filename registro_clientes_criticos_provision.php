<?php
include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/averias.php');
require_once('clases/gestionCriticos.php');
require_once('clases/capacidadHorarios.php');
require_once('clases/tecnicos.php');
require_once('clases/cedula.php');
require_once('clases/motivos.php');
require_once('clases/zonales.php');
require_once('agenda/agendaController.php');
//$fonoBus = $_REQUEST["fonoBus"];
$averia_ini = $_REQUEST["averia_ini"];
$actividad = $_REQUEST["actividad"];
$indice = $_REQUEST['indice'];

//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$ob_averia = new Provision($cnx);
$averia = $ob_averia->getProvision($cnx, $averia_ini);


if($averia["eecc_final"]!=""){
    $ob_empresa = new Empresa();
    $id_empresa = $ob_empresa->getIdEmpresa($cnx,$averia["eecc_final"]);
    $ob_cedula = new Cedula();
    $cedula = $ob_cedula->getCedulaAll($cnx,$id_empresa);
    $zonales = new Zonales();
    $id_zonal = $zonales->getIdZonal($cnx, $averia["zonal"]);

    $agenda = new agendaController();
    $agenda->setCnx($cnx);
    $agenda->setVempresa($id_empresa);
    $agenda->setVzona($id_zonal);

    $agendar_html = $agenda->AgendarAveriaShow();

}else{
	echo "La averia seleccionada no tiene una contrata asociada.Cierre la ventana y seleccione otra averia";
	exit();
}

$ob_mot = new Motivos($cnx);
$motivos = $ob_mot->getMotivos($cnx);




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
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
<div class="modalPop"></div>


<div class="registro_clientes">
<form name="frm_criticos" id="frm_criticos" action="" method="POST">
	<div class="datos">
		<div class="content_datos">
			<div class="caja">
				<label>Averia:</label>
				<input type="text" value="<?php echo trim($averia["averia"]);?>" name="h_averia" disabled>
				<input type="hidden" value="<?php echo trim($averia["averia"]);?>" name="averia">
			</div>
			<div class="caja">
				<label>Contrata:</label>
				<input type="text" value="<?php echo trim($averia["eecc_final"]);?>" name="h_eecc_zona" disabled style="width:120px">
				<input type="hidden" value="<?php echo trim($averia["eecc_zona"]);?>" name="eecc_zona" >
			</div>
			<div class="caja">
				<label>Actividad:</label>
				<input type="text" value="<?php echo trim($actividad);?>" name="h_actividad" disabled>
			</div>
		</div>
		<div class="content_datos">
			<div class="caja_large">
				<label>Direccion Instalación:</label>
				<input type="text" value="<?php echo htmlspecialchars(trim($averia["direccion_instalacion"]));?>" name="h_direccion_instalacion" disabled>
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["direccion_instalacion"]));?>" name="direccion_instalacion">
			</div>
			<div class="caja">
				<label>Distrito:</label>
				<input type="text" value="<?php echo trim($averia["distrito"]);?>" name="h_distrito" disabled>
				<input type="hidden" value="<?php echo trim($averia["distrito"]);?>" name="distrito">
			</div>
			<div class="caja">
				<label>MDF:</label>
				<input type="text" value="<?php echo trim($averia["mdf"]);?>" name="h_mdf" disabled>
				<input type="hidden" value="<?php echo trim($averia["mdf"]);?>" name="mdf">
			</div>
			<div class="caja">
				<label>Telefono:</label>
				<input type="text" value="<?php echo trim($averia["telefono"]);?>" name="h_telefono" disabled>
				<input type="hidden" value="<?php echo trim($averia["telefono"]);?>" name="telefono">
			</div>
			<div class="caja">
				<label>Zona:</label>
				<input type="text" value="<?php echo trim($averia["zonal"]);?>" name="h_zonal" disabled>
				<input type="hidden" value="<?php echo trim($averia["zonal"]);?>" name="zonal" >
			</div>
		</div>
	</div>
	<div class="datos">
		<div class="content_datos">
			<div class="caja_large">
				<label>Nombre de Cliente:</label>
				<input type="text" value="<?php echo trim($averia["nombre_cliente"]);?>" name="h_nombre_cliente" disabled>
				<input type="hidden" value="<?php echo trim($averia["nombre_cliente"]);?>" name="nombre_cliente">
			</div>
			
			<div class="caja_medium">
				<label>Teléfono de Contacto:</label>
				<input type="text" value="<?php echo trim($averia["fonos_contacto"]);?>" name="h_fonos_contacto" disabled>
				<input type="hidden" value="<?php echo trim($averia["fonos_contacto"]);?>" name="fonos_contacto">
			</div>
			<!--campos restantes-->
			<div class="caja_medium">
			  	<label>Motivos:</label>
			  	<select class="motivo_registro" id="motivo_registro" name="motivo_registro" >
				<?php
					foreach ($motivos as $mot):
						if($mot["id"]=="1" || $mot["id"]=="2"){
				?>
							<option value="<?php echo $mot["id"];?>"><?php echo $mot["motivo"];?></option>
				<?php
						}else{
							//
						}
					endforeach;
				?>
				</select>
			</div>
			<div class="caja_medium">
                <input type='hidden' name="tec_officetrack" id="tec_officetrack">
				<input type="hidden" value="0" name="n_evento" id="n_evento">
				<input type="hidden" value="<?php echo $indice; ?>" name="indice" id="indice">
				<input type="hidden" value="<?php echo $actividad; ?>" id="actividad" name="actividad">
				<input type="hidden" value="" id="datosfinal" name="datosfinal"> <?php /*indicador si realizará un evento*/ ?>	

				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["observacion_102"]));?>" name="observacion_102">
				<input type="hidden" value="<?php echo trim($averia["tipo_averia"]);?>" name="tipo_averia">
				<input type="hidden" value="<?php echo trim($averia["horas_averia"]);?>" name="horas_averia">
				<input type="hidden" value="<?php echo trim($averia["fecha_registro"]);?>" name="fecha_registro">
				<input type="hidden" value="<?php echo trim($averia["ciudad"]);?>" name="ciudad">
				<input type="hidden" value="<?php echo trim($averia["inscripcion"]);?>" name="inscripcion">
				<input type="hidden" value="<?php echo trim($averia["fono1"]);?>" name="fono1">
				<input type="hidden" value="<?php echo trim($averia["segmento"]);?>" name="segmento">
				<input type="hidden" value="<?php echo trim($averia["area_"]);?>" name="area_">
				<input type="hidden" value="<?php echo trim($averia["codigo_distrito"]);?>" name="codigo_distrito">
				<input type="hidden" value="<?php echo trim($averia["orden_trabajo"]);?>" name="orden_trabajo">
				<input type="hidden" value="<?php echo trim($averia["veloc_adsl"]);?>" name="veloc_adsl">
				<input type="hidden" value="<?php echo trim($averia["clase_servicio_catv"]);?>" name="clase_servicio_catv">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["codmotivo_req_catv"]));?>" name="codmotivo_req_catv">
				<input type="hidden" value="<?php echo trim($averia["total_averias_cable"]);?>" name="total_averias_cable">
				<input type="hidden" value="<?php echo trim($averia["total_averias_cobre"]);?>" name="total_averias_cobre">
				<input type="hidden" value="<?php echo trim($averia["total_averias"]);?>" name="total_averias">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["fftt"]));?>" name="fftt">
				<input type="hidden" value="<?php echo trim($averia["llave"]);?>" name="llave">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["dir_terminal"]));?>" name="dir_terminal">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["contrata"]));?>" name="contrata">

				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["wu_nagendas"]));?>" name="wu_nagendas">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["wu_nmovimientos"]));?>" name="wu_nmovimientos">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["wu_fecha_ult_agenda"]));?>" name="wu_fecha_ult_agenda">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["total_llamadas_tecnicas"]));?>" name="total_llamadas_tecnicas">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["total_llamadas_seguimiento"]));?>" name="total_llamadas_seguimiento">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["llamadastec15dias"]));?>" name="llamadastec15dias">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["llamadastec30dias"]));?>" name="llamadastec30dias">

				<input type="hidden" value="<?php echo trim($averia["quiebre"]);?>" name="quiebre" id="quiebre">
				<input type="hidden" value="<?php echo trim($averia["lejano"]);?>" name="lejano">
				<input type="hidden" value="<?php echo trim($averia["zona_movistar_uno"]);?>" name="zona_movistar_uno">
				<input type="hidden" value="<?php echo trim(htmlspecialchars($averia["paquete"]));?>" name="paquete">
				<input type="hidden" value="<?php echo trim($averia["data_multiproducto"]);?>" name="data_multiproducto">
				<input type="hidden" value="<?php echo trim($averia["averia_m1"]);?>" name="averia_m1">
				<input type="hidden" value="<?php echo trim($averia["fecha_data_fuente"]);?>" name="fecha_data_fuente">
				<input type="hidden" value="<?php echo trim($averia["telefono_codclientecms"]);?>" name="telefono_codclientecms">
				<input type="hidden" value="<?php echo trim($averia["rango_dias"]);?>" name="rango_dias">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["sms1"]));?>" name="sms1">
				<input type="hidden" value="<?php echo htmlspecialchars(trim($averia["sms2"]));?>" name="sms2">
				<input type="hidden" value="<?php echo trim($averia["area2"]);?>" name="area2">
				<input type="hidden" value="<?php echo trim($averia["tipo_actuacion"]);?>" name="tipo_actuacion">
				<input type="hidden" value="<?php echo trim($averia["eecc_final"]);?>" name="eecc_final">
				<input type="hidden" value="<?php echo trim($averia["microzona"]);?>" name="microzona">
				
				<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
				<input type="hidden" value="<?php echo $actividad?>" name="tipo_actividad">
				<input type="hidden" value="registro_critico" name="registro_critico"/>
			</div>
		</div>
	</div>

	
		<div id="segun_motivo" class="datos">

		<div class="segun_motivo">
			<div class="content_datos">
				<div class="caja_large">
					<label class="nombre">Nombre de Contacto:</label>
					<textarea class="border" maxlength="255" value="" id="cr_nombre" name="cr_nombre"></textarea>

				</div>
			</div>
			<div class="content_datos">
				<div class="caja_medium">
					<label>Telefono de Cotacto:</label>
					<input class="border" type="text" value="" maxlength="11" name="cr_telefono" id="cr_telefono">
				</div>
				<div class="caja_medium">
					<label>Celular de Contacto:</label>
					<input class="border" type="text" value="" maxlength="11" name="cr_celular" id="cr_celular">
				</div>
			</div>
		</div>
			<div class="content_datos">
				<div class="caja_text">
					<label>Observacion:</label>
				</div>
				<div class="caja_text">
					<textarea class="border" maxlength="255" value="" id="cr_observacion" name="cr_observacion"></textarea>
				</div>
			</div>
			<div class="content_datos agendarCelula">
				<div class="caja_medium">
					<label>Celula:</label>
				  	<select class="slct_cedula" id="slct_cedula" name="slct_cedula" onchange="cargarTecnico('tecnico','','slct_cedula','<?php echo $id_empresa; ?>','<?php echo $averia["quiebre"]; ?>');">
				  		<option value=''>-- Seleccione --</option>
					<?php
						foreach ($cedula as $r):
					?>
						<option value="<?php echo $r["id"];?>"><?php echo $r["nombre"];?></option>
					<?php
						endforeach;
					?>
					</select>
				</div>
			</div>
			<div class="content_datos agendarTecnico">
				<div class="caja_medium">
					<label>Tecnico:</label>
					<input type="hidden" id="nombretecnico" name="nombretecnico">
					<select class="tecnico" id="tecnico" name="tecnico" >
						<option value=''>-- Seleccione --</option>
					</select>
					<span class="checkbox">
						<input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico">Tecnico Entregado
					</span>
                    <div><span class="labeltecoffice"></span></div>
				</div>
			</div>
			<div class="content_datos">
						<div class="caja_text">
							<label class="horario segun_motivo">Seleccione el Horario:</label>
							<div class="horario">
							<?php
								//Creando el horario
//								$ob_horario = new capacidadHorarios($cnx);
//								$ob_horario->getHorarios($cnx,$averia["eecc_final"],$averia["zonal"],$actividad);
							?>
                            <?=$agendar_html; ?>
							</div>
							<input type="submit" value="Registrar" name="registrar" id="btn_registro"/>
						</div>
			</div>

		</div>

		<div class="registrar">
			
		</div>
	</form>
</div>
</body>
</html>