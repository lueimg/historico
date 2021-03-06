<?php 
require_once("../../cabecera.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>WEBPSI - Criticos - Reportes</title>

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

        $("#txt_fecha_ini").datepicker({
            yearRange:'0:+1',
            maxDate:'0D',
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });
		
        $("#txt_fecha_fin").datepicker({
            yearRange:'0:+1',
			maxDate:'0D',
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });

        $("#txt_fecha_ini_prov").datepicker({
            yearRange:'0:+1',
            maxDate:'0D',
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });
		
        $("#txt_fecha_fin_prov").datepicker({
            yearRange:'0:+1',
			maxDate:'0D',
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });
		
		
		var dlg=$('#register').dialog({
			title: 'Actualizacion',
			resizable: false,
			autoOpen:false,
			modal: true,
			hide: 'fade',
			width:450,
			height:350
		});
		
		$('#reg_link').click(function(e) {
			//alert("Hola");
			var pagina = "fechaUpdate.php"; 
			e.preventDefault();
			dlg.load(pagina, function(){
				dlg.dialog('open');
			});
			
			//$("#register").dialog("open");
		});			
	
		
	});
	

function descargaAverias(tipo) {
	//alert(tipo);
	window.open("reporte_analisis_criticos_excel_averias.php?tipo="+tipo);

}

function descargaProvision() {
	
	window.open("reporte_criticos_excel_provision.php");

}

function reporteHistorico() {
	var fecIni = $("#txt_fecha_ini").val();
	var fecFin = $("#txt_fecha_fin").val();
	var pag = "reporte_criticos_excel_averias_historico.php?fecIni="+fecIni+"&fecFin="+fecFin;

	window.open(pag);
}

function reporteHistoricoProv() {
	var fecIni = $("#txt_fecha_ini_prov").val();
	var fecFin = $("#txt_fecha_fin_prov").val();
	var pag = "reporte_criticos_excel_provision_historico.php?fecIni="+fecIni+"&fecFin="+fecFin;

	window.open(pag);
}


function verFechaUpdate() {

}

</script>
</head>

<body>

<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<?php echo pintar_cabecera(); ?>

<br/>

<div id="div_bus" class="divBusqueda" style="width: 750px" >

	<table class="tablaBusqueda">
		<thead>
			<th colspan='3'>Reporte de Analisis Criticos</th>
		</thead>	
	
	<tr class="tr_busqueda">
		<td style="width: 50px">Averias STB:</td >
		<td style="background-color:white; padding: 5px;" ><a href="#" onclick="descargaAverias('stb');">[ Descargar ]</a></td >
	</tr>
	<tr class="tr_busqueda">
		<td style="width: 50px">Averias ADSL:</td >
		<td style="background-color:white; padding: 5px;" ><a href="#" onclick="descargaAverias('adsl');">[ Descargar ]</a></td >
	</tr>	
	<tr class="tr_busqueda">
		<td style="width: 50px">Averias CATV:</td >
		<td style="background-color:white; padding: 5px;" ><a href="#" onclick="descargaAverias('catv');">[ Descargar ]</a></td >
	</tr>	
	</table>
</div>

<div id="register"></div>
	
</body>
</html>