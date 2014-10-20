<?php 
require_once("../../cabecera.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>WEBPSI - Criticos - Consulta de Seguimiento</title>

<?php include ("../../includes.php") ?>    

<script type="text/javascript" src="../../js2/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../js2/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<link type="text/css" href="../../js2/jquery-ui-1.10.3.custom/css/redmond/jquery-ui-1.10.3.custom.css" rel="Stylesheet" />

<link type="text/css" href="css/reporteador.css" rel="Stylesheet" />
<link type="text/css" href='css/botones.css' rel="Stylesheet" />

<style type="text/css">
input[type="text"], select {
	/*border: 1px solid #000000;	*/
	border:1px solid #6297BC;
	padding: 1px;
	font-family:tahoma, arial, sans-serif;
	font-size: 11px;
}
</style>

<script type="text/javascript">
	
	$(document).ready(function(){

        $("#cmbTipo").val(1);
        validarTexto(1);
		$("#txtBus").val('');
		$("#txtBus").focus();
	
 
		$("#btnBuscar").click(function(){
			if ($("#txtBus").val()=='') {
				alert("Debe ingresar un dato para buscar.");
				return false;
			}
			
			var pagina="controladorHistorico/historicoController.php";
			$.ajax({
				type: "POST",
				url: pagina,
				data: {
					filtro: 'filtro_personalizado',
					cmbTipo: $("#cmbTipo").val(),
					txtBus: $("#txtBus").val()
				},
				success: function(html){
					$("#resultado1").html(html);
				}
			});			

		});

		$(".detalle").click(cargarDetalle);

	});

cargarDetalle=function(){
var pagina="consultaCritico_ajax.php";
	$.ajax({
		type: "POST",
		url: pagina,
		data: {
			action: 'buscar',
			cmbTipo: $("#cmbTipo").val(),
			txtBus: $("#txtBus").val()
		},
		success: function(html){
			$("#resultado1").html(html);
		}
	});
}

validarTexto=function(valor){
	$("#txtBus").val('');
	if(valor=="1"){		
		$("#txtBus").attr("onKeyPress","return validaNumeros(event);");		
	}
	else if(valor=="2" || valor=="3"){
		$("#txtBus").attr("onKeyPress","return validaAlfanumerico(event);");
	}
	/*else if(valor=="4"){
		$("#txtBus").attr("onKeyPress","return validaLetras(event);");
	}*/
	else if(valor=='5'){
		$("#txtBus").attr("onKeyPress","return validaNumerosIn(event);");
	}
	$("#txtBus").focus();
}

validaEnter=function(e){ 
	tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==13){
    	$("#btnBuscar").click();
    }
}

validaNumerosIn=function(e) { // 1
        tecla = (document.all) ? e.keyCode : e.which; // 2
        if (tecla==8 || tecla==0 || tecla==46 || tecla==44) return true;//8 barra, 0 flechas desplaz
        patron = /\d/; // Solo acepta números
        te = String.fromCharCode(tecla); // 5
        return patron.test(te); // 6
}

validaNumeros=function(e) { // 1
        tecla = (document.all) ? e.keyCode : e.which; // 2
        if (tecla==8 || tecla==0 || tecla==46) return true;//8 barra, 0 flechas desplaz
        patron = /\d/; // Solo acepta números
        te = String.fromCharCode(tecla); // 5
        return patron.test(te); // 6
}

validaLetras=function(e) { // 1
        tecla = (document.all) ? e.keyCode : e.which; // 2
        if (tecla==8 || tecla==0 || tecla==32) return true;//8 barra, 0 flechas desplaz
        patron =/[A-Za-zñÑáéíóúÁÉÍÓÚ\s]/; // 4 ,\s espacio en blanco, patron = /\d/; // Solo acepta números, patron = /\w/; // Acepta números y letras, patron = /\D/; // No acepta números, patron =/[A-Za-z\s]/; //sin ñÑ
        te = String.fromCharCode(tecla); // 5
        return patron.test(te); // 6
}

validaAlfanumerico=function(e) { // 1
        tecla = (document.all) ? e.keyCode : e.which; // 2
        if (tecla==8 || tecla==0 || tecla==46 || tecla==45 || tecla==95) return true;//8 barra, 0 flechas desplaz
        patron =/[A-Za-zñÑáéíóúÁÉÍÓÚ@.,_\-\s\d]/; // 4 ,\s espacio en blanco, patron = /\d/; // Solo acepta números, patron = /\w/; // Acepta números y letras, patron = /\D/; // No acepta números, patron =/[A-Za-z\s]/; //sin ñÑ
        te = String.fromCharCode(tecla); // 5
        return patron.test(te); // 6
}
	


</script>



</head>

<body>

<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<?php echo pintar_cabecera(); ?>

<br/>

<div id="div_bus" class="divBusqueda" style="width: 750px" >

	<table class="tablaBusqueda" style='width: 80%'>
		<thead>
			<th colspan='3'>Consulta de seguimiento - Casos Criticos</th>
		</thead>	
	
		<tr class="tr_busqueda">
			<td style="width: 15%">Tipo Busqueda:</td >
			<td style="width: 10%; background-color:white; padding: 3px;" >
				<select name='cmbTipo' id='cmbTipo' onchange="validarTexto(this.value);" >					
					<option value='1'>&nbsp;Telefono</option>
					<option value='2'>&nbsp;Averia</option>
					<option value='3'>&nbsp;ATC</option>
					<option value='5'>&nbsp;Id Gestion</option>
				</select>
			</td >
			<td style="width: 75%; background-color:white; padding: 3px;"><input type='text' name='txtBus' id='txtBus' onkeyup="return validaEnter(event);" />
			<input type='button' name='btnBuscar' id='btnBuscar' value='Buscar' />
			</td >			
		</tr>
	</table>
</div>

<br/>

<div id="register"></div>

<div id="div_listado" class="divBusqueda" style="width: 750px" >
	<table class="tablaBusqueda" style='width: 90%'>
		<thead>
			<th colspan='7'>RESULTADOS DE LA BUSQUEDA</th>
		</thead>
		<tr class="tr_busqueda">
			<th class="celda0" style='text-align:center'>Fecha Movimiento</th >
			<th class="celda0" style='text-align:center'>Usuario</th >
			<th class="celda0" style='text-align:center'>Estado</th >
			<th class="celda0" style='text-align:center'>Observaciones</th >
			<th class="celda0" style='text-align:center'>Motivo</th >
			<th class="celda0" style='text-align:center'>Submotivo</th >	
			<th class="celda0" style='text-align:center'>Fecha Agenda / Turno</th >			
		</tr>		
		
	</table>
</div>
	
<div id="resultado1"></div>

</body>
</html>