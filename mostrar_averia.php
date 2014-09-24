<?php

//include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/gestionAveria.php');
require_once('clases/gestionProvision.php');
require_once('clases/gestionManual.php');

require_once('clases/averias.php');
require_once($PATH.'modulos/historico/clases/provision.php');

//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();
$actividad = $_GET['actividad'];
$averia = $_GET['id'];

if($actividad=="Provision"){
	if(isset($_GET['tipo']) && $_GET['tipo']=='raiz'){
		$averia = $_GET['id'];
		$ob_averia = new Provision();
		$averias = $ob_averia->getProvision($cnx,$averia);
	}else{
		$id = $_GET['id'];
		$ob_averia = new gestionProvision();
		$averias = $ob_averia->getGestionProvisionId($cnx,$id);
	}
}else if($actividad=="Manual"){
		$id = $_GET['id'];
		//print_r($_GET);
		$ob_manual = new GestionManual();
		$averias = $ob_manual->getGestionManualId($cnx,$id);
	
}else if($actividad=="Averias"){
	if(isset($_GET['tipo']) && $_GET['tipo']=='raiz'){
		$averia = $_GET['id'];
		$ob_averia = new Averias();
		$averias = $ob_averia->getAverias($cnx,$averia);
	}else{
		$id = $_GET['id'];
		$ob_averia = new gestionAveria();
		$averias = $ob_averia->getGestionAveriaId($cnx,$id);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>                
        
		<!--<script type="text/javascript" src="js/criticos.js"></script>  
		-->	

		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
		<link type="text/css" href='css/horarios.css' rel="Stylesheet" />
		
<style>

.caja1 {
	border:1px solid #6297BC;
	padding: 1px;
	font-family:tahoma, arial, sans-serif;
	font-size: 10px;
	width:400px;height:100px;
	
}

img { cursor: pointer; cursor: hand; }

</style>		
		
		
<script type="text/javascript">
$(document).ready(function(){
	$('input[type="text"], textarea').attr('readonly','readonly');
	
	var dlgSms=$("#dialogsms").dialog({
		title: 'Envio SMS',
		position: 'center' ,
		autoOpen:false,
		modal: true,
		draggable: false,
		width:480,
		height:400
	});	
	
	$('#enviosms1').click(function(e) {
		var sms1=$("#sms1").val();
		
		sms1 = encodeURIComponent(sms1);
		var pag="sms_libre.php?mensaje="+sms1;

		e.preventDefault();
		dlgSms.load(pag, function(){
			dlgSms.dialog('open');
		});
	});
	
	$('#enviosms2').click(function(e) {
		var sms2=$("#sms2").val();
		
		sms2 = encodeURIComponent(sms2);
		var pag="sms_libre.php?mensaje="+sms2;

		e.preventDefault();
		dlgSms.load(pag, function(){
			dlgSms.dialog('open');
		});
	});	

});



</script>
		
</head>

<body>

<!--<div id="loading"></div>-->
<div class="modalPop"></div>
<div id="dialogsms" ></div>

<!--<div id="page-wrap">-->

<div class="averias_listado">
<form name="frm_listado_averias" id="frm_gestion_critico" action="" method="POST">
	<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
		<div class="datos">
			<div class="caja_mini">
				<label class="atc">Tipo Averia:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["tipo_averia"]);?>" name="tipo_averia" id="tipo_averia">
			</div>
			<div class="caja_mini">
				<label>Horas Averia:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["horas_averia"]);?>" 
					name="horas_averia" id="horas_averia">
			</div>
			<div class="caja_mini">
				<label class="atc">Fecha Reporte:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["fecha_reporte"]);?>" name="fecha_reporte" id="fecha_reporte">
			</div>
			<div class="caja_mini">
				<label class="atc">Fecha_registro:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["fecha_registro"]);?>" name="fecha_registro" id="fecha_registro">
			</div>
		</div>

		<div class="datos">
			
			<div class="caja_mini">
				<label>Ciudad:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["ciudad"]);?>" 
					name="ciudad" id="ciudad">
			</div>
			<div class="caja_mini">
				<label class="atc">Avería:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["averia"]);?>" name="averia" id="averia">
			</div>
			<div class="caja_mini">
				<label class="atc">MDF:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["mdf"]);?>" name="mdf" id="mdf">
			</div>
			<div class="caja_mini">
				<label>Segmento:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["segmento"]);?>" 
					name="segmento" id="segmento">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label class="atc">Inscripcion:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["inscripcion"]);?>" name="inscripcion" id="inscripcion">
			</div>
			<div class="caja_mini">
				<label>Fono1:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["fono1"]);?>" 
					name="fono1" id="fono1">
			</div>
			<div class="caja_mini">
				<label class="atc">Teléfono:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["telefono"]);?>" name="telefono" id="telefono">
			</div>
			<div class="caja_mini">
				<label class="atc">Area_:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["area_"]);?>" name="area_" id="area_">
			</div>
		</div>
		<div class="datos_large">
			<div class="caja_medium">
				<label>Nombre Cliente:</label>
				<textarea class="border big" id="nombre_cliente" name="nombre_cliente" ><?php echo utf8_encode(trim($averias["nombre_cliente"]));?></textarea>
			</div>
			<div class="caja_medium">
				<label>Dirección Instalación:</label>
				<textarea class="border" id="direccion_instalacion" name="direccion_instalacion" ><?php echo htmlspecialchars(trim($averias["direccion_instalacion"]));?></textarea>
			</div>
			<div class="caja_medium">
				<label>Wu_fecha_ult_agenda:</label>
				<textarea class="border" id="wu_fecha_ult_agenda" name="wu_fecha_ult_agenda" ><?php echo (trim($averias["wu_fecha_ult_agenda"])=="" || trim($averias["wu_fecha_ult_agenda"])=="0")? "": htmlspecialchars(trim($averias["wu_fecha_ult_agenda"]));?></textarea>
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Codigo Distrito:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["codigo_distrito"]);?>" name="codigo_distrito" id="codigo_distrito">
			</div>
			<div class="caja_mini">
				<label>Orden Trabajo:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["orden_trabajo"]);?>" 
					name="orden_trabajo" id="orden_trabajo">
			</div>
			<div class="caja_mini">
				<label>Veloc_Adsl:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["veloc_adsl"]);?>" name="veloc_adsl" id="veloc_adsl">
			</div>
			<div class="caja_mini">
				<label>Zonal:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["zonal"]);?>" name="zonal" id="zonal">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Clase_Serv_Catv:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["clase_servicio_catv"]);?>" name="clase_servicio_catv" id="clase_servicio_catv">
			</div>
			<div class="caja_mini">
				<label>Codmotivo_req_catv:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["codmotivo_req_catv"]);?>" 
					name="Codmotivo_req_catv" id="Codmotivo_req_catv">
			</div>
			<div class="caja_mini">
				<label>Llave:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["llave"]);?>" name="llave" id="llave">
			</div>
			<div class="caja_mini">
				<label>Quiebre:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["quiebre"]);?>" name="quiebre" id="quiebre">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Tot Averias Cable:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["total_averias_cable"]);?>" name="total_averias_cable" id="total_averias_cable">
			</div>
			<div class="caja_mini">
				<label>Tot Averias Cobre:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["total_averias_cobre"]);?>" 
					name="total_averias_cobre" id="total_averias_cobre">
			</div>
			<div class="caja_mini">
				<label>Tot Averias:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["total_averias"]);?>" name="total_averias" id="total_averias">
			</div>
			<div class="caja_mini">
				<label>Distrito:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["distrito"]);?>" name="distrito" id="distrito">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Fonos Contacto:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["fonos_contacto"]);?>" 
					name="fonos_contacto" id="fonos_contacto">
			</div>
			<div class="caja_mini">
				<label>Contrata:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["eecc_final"]);?>" name="contrata" id="contrata">
			</div>
			<div class="caja_mini">
				<label>Lejano:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["lejano"]);?>" 
					name="lejano" id="lejano">
			</div>
			<div class="caja_mini">
				<label>EECC_Zona:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["eecc_final"]);?>" name="eecc_zona" id="eecc_zona">
			</div>
		</div>
		<div class="datos_large">
			<div class="caja_medium">
				<label>fftt:</label>
				<textarea class="border" id="fftt" name="fftt" ><?php echo trim($averias["fftt"]);?></textarea>
			</div>
			<div class="caja_medium">
				<label>Dir. Terminal:</label>
				<textarea class="border" id="dir_terminal" name="dir_terminal" ><?php echo trim($averias["dir_terminal"]);?></textarea>
			</div>
			<div class="caja_medium">
				<label>Paquete:</label>
				<textarea class="border" id="paquete" name="paquete" ><?php echo trim($averias["paquete"]);?></textarea>
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Zona Movistar 1:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["zona_movistar_uno"]);?>" 
					name="zona_movistar_uno" id="zona_movistar_uno">
			</div>
			<div class="caja_mini">
				<label>Data Multiproducto:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["data_multiproducto"]);?>" name="data_multiproducto" id="data_multiproducto">
			</div>
			<div class="caja_mini">
				<label>Averia m1:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["averia_m1"]);?>" 
					name="averia_m1" id="averia_m1">
			</div>
			<div class="caja_mini">
				<label>Fec Data Fuente:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["fecha_data_fuente"]);?>" name="fecha_data_fuente" id="fecha_data_fuente">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Tel. Codclientecms:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["telefono_codclientecms"]);?>" 
					name="telefono_codclientecms" id="telefono_codclientecms">
			</div>
			<div class="caja_mini">
				<label>Rango de Días:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["rango_dias"]);?>" name="rango_dias" id="rango_dias">
			</div>
			<div class="caja_mini">
				<label>Area2:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["area2"]);?>" 
					name="area2" id="area2">
			</div>
			<div class="caja_mini">
				<label>Tot_Llam_Tecnicas:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["total_llamadas_tecnicas"]);?>" name="total_llamadas_tecnicas" id="total_llamadas_tecnicas">
			</div>
		</div>
		<div class="datos">
			<div class="caja_mini">
				<label>Tot_Llam_Seguimiento:</label>
				<input class="border" type="text"  value="<?php echo trim($averias["total_llamadas_seguimiento"]);?>" name="total_llamadas_seguimiento" id="total_llamadas_seguimiento">
			</div>
		</div>
		<div class="datos_large">
			<div class="caja_medium" >
				<label>SMS 1:</label><img src="img/sms.png" alt="sms" title="sms" width="25" height="25" id="enviosms1" />
				<textarea class="caja1" id="sms1" name="sms1"  ><?php echo trim($averias["sms1"]);?></textarea>
			</div>
			<div class="caja_medium">
				<label>Sms2:</label><img src="img/sms.png" alt="sms" title="sms" width="25" height="25" id="enviosms2" />
				<textarea class="caja1" id="sms2" name="sms2" ><?php echo utf8_encode(trim($averias["sms2"]));?></textarea>
			</div>
			<div class="caja_medium">
				<label class="atc">Observacion_102:</label>
				<textarea class="caja1" id="observacion_102" name="observacion_102" ><?php echo trim($averias["observacion_102"]);?></textarea>
			</div>
		</div>
	</form>
</div>


