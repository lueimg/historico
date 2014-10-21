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

<style>

.celda0 {
	background-color: #6E9CC8; padding: 3px;
	color: #FFFFFF;
	border: 1px solid #17688B;
	font-size: 11px;
}


.celda1 {
	background-color: white; padding: 3px;
	color: #2A4266;
	border: 1px solid #6297BC;
	font-size: 11px;
}

.celda2 {
	background-color: white; padding: 3px;
	color: #2A4266;
	border: 1px solid #6297BC;
	font-size: 10px;
	font: Verdana,Arial,Helvetica,sans-serif;
}

</style>

<script type="text/javascript">
	
	$(document).ready(function(){

        $("#cmbTipo").val(1);
        validarTexto(1);
		$("#txtBus").val('');
		$("#txtBus").focus();
	
 
		$("#btnBuscar").click(function(){

			$("#resultado1").html("");

			if ($("#txtBus").val()=='') {
				alert("Debe ingresar un dato para buscar.");
				return false;
			}
			
			var pagina="controladorHistorico/historicoController.php";
			$.ajax({
				type: "POST",
				url: pagina,
				dataType: "Json",
				data: {
					filtro: 'filtro_personalizado',
					tipo: $("#cmbTipo").val(),
					valor_filtro: $("#txtBus").val()
				},
				success: function(obj){
					listarResultado(obj);
				}
			});			

		});

		$(".detalle").click(cargarDetalle);

	});

listarResultado=function(datos){
	var htm="";
	$("#div_listado").css("display","");
	$("#tabla_listado .elimina").remove();
	if(datos.length>0){
		$.each(datos,function(index,data){
			htm='';
			htm='<tr class="elimina">'+
					'<td class="celda2" onClick="cargarDetalle('+data.id+');"><img title="Mostrar Detalle" alt="Mostrar Detalle" src="img/mov.jpg"></td>'+
			    	'<td class="celda2">'+data.averia+'</td>'+
			    	'<td class="celda2">'+data.id_atc+'</td>'+
			    	'<td class="celda2">'+data.tipo_actividad+'</td>'+
			    	'<td class="celda2">'+data.fecha_reg+'</td>'+
			    	'<td class="celda2">'+data.quiebres+'</td>'+
			    	'<td class="celda2">'+data.empresa+'</td>'+
			    	'<td class="celda2">'+data.estado+'</td>'+
			    	'<td class="celda2">'+data.fecha_agenda+' '+data.horario+'</td>'+
			    	'<td class="celda2">'+data.tecnico+'</td>'+
			    '</tr>';
			$("#tabla_listado").append(htm);
		});
	}	
}

cargarDetalle=function(idg){
var pagina="consultaCritico_ajax.php";
	$.ajax({
		type: "POST",
		url: pagina,
		data: {
			action: 'buscar',
			id:idg
		},
		success: function(html){
			$("#resultado1").html(html);
		}
	});
}

validarTexto=function(valor){
	$("#txtBus").val('');
	if(valor=="telefono"){		
		$("#txtBus").attr("onKeyPress","return validaNumeros(event);");		
	}
	else if(valor=="averia" || valor=="atc"){
		$("#txtBus").attr("onKeyPress","return validaAlfanumerico(event);");
	}
	/*else if(valor=="4"){
		$("#txtBus").attr("onKeyPress","return validaLetras(event);");
	}*/
	else if(valor=='id_gestion'){
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
					<option value='telefono'>&nbsp;Telefono</option>
					<option value='averia'>&nbsp;Averia</option>
					<option value='atc'>&nbsp;ATC</option>
					<option value='id_gestion'>&nbsp;Id Gestion</option>
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

<div id="div_listado" class="divBusqueda" style="width: 1000px">
	<table id="tabla_listado" class="tablaBusqueda" cellpadding="1" cellspacing="0" border="1">
		<thead>
			<th colspan='10'>RESULTADOS DE LA BUSQUEDA</th>
		</thead>
		<tr class="tr_busqueda">
			<th style='text-align:center' width="50px">[ ]</th >
			<th style='text-align:center' width="80px">Averia</th >
			<th style='text-align:center' width="80px">Cod Atencion</th >
			<th style='text-align:center' width="80px">Tipo Actividad</th >
			<th style='text-align:center' width="130px">Fecha Reg</th >
			<th style='text-align:center' width="80px">Quiebre</th >
			<th style='text-align:center' width="80px">Empresa</th >
			<th style='text-align:center' width="70px">Estado</th >
			<th style='text-align:center' width="130px">Fecha Agenda</th >	
			<th style='text-align:center' width="230px">Tecnico</th >			
		</tr>		
		
	</table>
</div>
	<br>
	<br>
<div id="resultado1"></div>

</body>
</html>