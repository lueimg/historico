<?php 
require_once("../../cabecera.php");
set_time_limit(0);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Web PSI - Historico Clientes</title>

<?php include ("../../includes.php") ?>    



<!--<link type="text/css" href="../../../../librerias/jquery-ui-1.10.4.webpsi/css/webpsi/jquery-ui-1.10.4.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="../../../../librerias/jquery-ui-1.10.4.webpsi/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../../../../librerias/jquery-ui-1.10.4.webpsi/js/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="js/cronometro.js"></script>-->
<script type="text/javascript" src="js/historico.js"></script>
<link type="text/css" href='css/estilo.css' rel="Stylesheet" />

	<script type="text/javascript">

	$(document).ready(function(){

        //variables globales js
        window.page = "visorHistorico";



		$("#telefonoCliente").keydown(function(event) {
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
				(event.keyCode == 67 && event.ctrlKey === true) || 
				(event.keyCode == 86 && event.ctrlKey === true) || 
				(event.keyCode == 88 && event.ctrlKey === true) || 
				(event.keyCode >= 35 && event.keyCode <= 39)) {
					 return;
			}
			else {
				if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault(); 
				}   
			}
		});
		
		$("#codigoClienteATIS").keydown(function(event) {
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
				(event.keyCode == 67 && event.ctrlKey === true) || 
				(event.keyCode == 86 && event.ctrlKey === true) || 
				(event.keyCode == 88 && event.ctrlKey === true) || 
				(event.keyCode >= 35 && event.keyCode <= 39)) {
					 return;
			}
			else {
				if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault(); 
				}   
			}
		});

		$("#codigoServicioCMS").keydown(function(event) {
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
				(event.keyCode == 67 && event.ctrlKey === true) || 
				(event.keyCode == 86 && event.ctrlKey === true) || 
				(event.keyCode == 88 && event.ctrlKey === true) || 
				(event.keyCode >= 35 && event.keyCode <= 39)) {
					 return;
			}
			else {
				if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault(); 
				}   
			}
		});

		$("#codigoClienteCMS").keydown(function(event) {
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
				(event.keyCode == 67 && event.ctrlKey === true) || 
				(event.keyCode == 86 && event.ctrlKey === true) || 
				(event.keyCode == 88 && event.ctrlKey === true) || 
				(event.keyCode >= 35 && event.keyCode <= 39)) {
					 return;
			}
			else {
				if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
					event.preventDefault(); 
				}   
			}
		});

		$("#btn_historico").button();
		$("#btn_limpiar").button({
		        icons: { primary: 'limpiar' }
		    });
		$( "#tabs" ).tabs({ fxAutoHeight: true });
                
	
	});
	
	</script>

</head>
<body>
<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<?php echo pintar_cabecera(); ?>

<br/>
<!-- Div del formulario de busqueda -->
<div id="div_bus" class="div_busqueda" style="width: 1050px">
	<div id="foo">

	</div>
	<table>
	<tr>
	<td>
	<fieldset>
		<legend>Speedy / Básica</legend>
		<table>
		<tr>
		<td style="width: 160px; padding: 10px">
			<label style="padding: 10px;">Telefono : </label>
			<input type="text" name="telefonoCliente" id="telefonoCliente" maxlength="8" style="width:80px"/>
		</td>
		<td style="width: 230px; padding: 10px">
			<label style="padding: 10px;">Cod. Cliente (ATIS) : </label>
			<input type="text" name="codigoClienteATIS" id="codigoClienteATIS" maxlength="12" style="width:90px"/>
		</td>
		</tr>
		</table>
	</fieldset>
	</td>
	<td>
	<fieldset>
		<legend>CATV</legend>
		<table>
		<tr>
		<td style="width: 200px; padding: 10px">
			<label style="padding: 10px;">Cod. Servicio : </label>
			<input type="text" name="codigoServicioCMS" id="codigoServicioCMS" maxlength="10" style="width:90px"/>
		</td>
		<td style="width: 220px; padding: 10px">
			<label style="padding: 10px;">Cod. Cliente (CMS) : </label>
			<input type="text" name="codigoClienteCMS" id="codigoClienteCMS" maxlength="10" style="width:80px"/>
		</td>
		</tr>
		</table>
	</fieldset>
	</td>
	<td>
	<input type="button" id="btn_historico" name="btn_historico" value="Consultar Historico" />
	<button id="btn_limpiar" name="btn_limpiar">&nbsp;</button>
	<!--
	<div id='agendarVisita' name='agendarVisita' style="display: none">
	<button type="button" id="btn_cliente_critico"
	style="margin-top:5px;background:red;border:0;border-radius:4px;color:#fff;padding:3px">Agendar Visita</button>
	</div>
        
	-->
	</td>
	</tr>
	</table>
</div>
<!-- Div que contiene la información del cliente -->
<div id="datos_cliente">
	<center><table border="0" width="95%" cellspacing="0" cellpadding="5">
		<tr>
			<td><span id="critico"></span><span id="nocritico"></span>
			<br/><span id="critico2"></span></td>
		</tr>
	</table></center>
	<center><table border="0" width="95%" cellspacing="0" cellpadding="5">
		<tr>
			<td id="etiqueta" width="65">Inscripci&oacute;n </td>
			<td id="campo" width="80"><span id="r_inscrip">&nbsp;</span></td>
			<td id="etiqueta" width="55">Tel&eacute;fono </td>
			<td id="campo" width="65"><span id="r_telefono">&nbsp;</span></td>
			<td id="etiqueta" width="80">Cod. Cli. ATIS</td>
			<td id="campo" width="75"><span id="r_codclie">&nbsp;</span></td>
			<td id="etiqueta" width="80">Cod. Cli. CMS</td>
			<td id="campo" width="55"><span id="r_codclicms">&nbsp;</span></td>
			<td id="etiqueta" width="80">Cod. Ser. CMS </td>
			<td id="campo" width="55"><span id="r_codsercms">&nbsp;</span></td>
			<td id="etiqueta" width="50">Paquete </td>
			<td id="campo"><span id="r_paquete">&nbsp;</span></td>
			<td id="etiqueta" width="60">Segmento </td>
			<td id="campo"><span id="r_segmento">&nbsp;</span></td>
		</tr>
	</table></center>
	<center><table border="0" width="95%" cellspacing="0" cellpadding="5">
		<tr>
			<td id="etiqueta" width="65">Direcci&oacute;n </td>
			<td id="campo"><span id="r_direcc">&nbsp;</span></td>
			<td id="etiqueta" width="50">Nombre </td>
			<td id="campo"><span id="r_nombre">&nbsp;</span></td>
			<td id="etiqueta" width="70">Ap. Paterno </td>
			<td id="campo"><span id="r_paterno">&nbsp;</span></td>
			<td id="etiqueta" width="70">Ap. Materno </td>
			<td id="campo"><span id="r_materno">&nbsp;</span></td>
		</tr>
	</table></center>
	<center><table border="0" width="95%" cellspacing="0" cellpadding="5">
		<tr>
			<td id="etiqueta" width="105">Modalidad Speedy </td>
			<td id="campo"><span id="r_modalidad">&nbsp;</span></td>
			<td id="etiqueta" width="55">Velocidad </td>
			<td id="campo"><span id="r_velocidad">&nbsp;</span></td>
			<td id="etiqueta" width="35">Tasa </td>
			<td id="campo"><span id="r_tasa">&nbsp;</span></td>
			<td id="etiqueta" width="65">Tecnolog&iacute;a </td>
			<td id="campo"><span id="r_tecno">&nbsp;</span></td>
		</tr>
	</table></center>
</div>
<!-- Tabs que muestran información de Provisión y Averías-->
<br>
<center>
<div id="tabs" style="width:95%;">
	<ul>
		<li><a href="#tabs-averias">Averías</a></li>
		<li><a href="#tabs-provision">Provisión</a></li>
		<li><a href="#tabs-llamadas">Llamadas</a></li>
		<li><a href="#tabs-criticos">Criticos</a></li>
	</ul>
	<div id="tabs-averias" style="display: table; width:100%">
		<p>Aqu&iacute; resultado de Averias </p>
	</div>
	<div id="tabs-provision">
		<p>Aqu&iacute; resultado de Provisión.</p>
	</div>
	<div id="tabs-llamadas">
		<p>Aqu&iacute; resultado de Llamadas.</p>
	</div>
	<div id="tabs-criticos">
		<p>Aqu&iacute; resultado de Criticos.</p>
	</div>	
</div>
</center>

<div id="dialog-modal" title="Servicios encontrados"></div>

<div id="dialog-criticos" title="Registro de clientes criticos"></div>

<div id="dialog-registro-manual" title="Registro Manual de Pendiente"></div>

</div>

</body>
</html>