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
		$("#txtBus").val('');
		$("#txtBus").focus();
	
 
		$("#btnBuscar").click(function(){
			if ($("#txtBus").val()=='') {
				alert("Debe ingresar un dato para buscar.");
				return false;
			}
			
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

		});

	});
	


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
				<select name='cmbTipo' id='cmbTipo' >
					<option value='0' selected>-- Seleccionar -- </option>
					<option value='1'>&nbsp;Telefono</option>
					<option value='2'>&nbsp;Averia</option>
					<option value='3'>&nbsp;ATC</option>
					<option value='4'>&nbsp;Cod Cliente CMS</option>
				</select>
			</td >
			<td style="width: 75%; background-color:white; padding: 3px;"><input type='text' name='txtBus' id='txtBus' />
			<input type='button' name='btnBuscar' id='btnBuscar' value='Buscar' />
			</td >			
		</tr>
	</table>
</div>

<br/>

<div id="register"></div>
	
<div id="resultado1"></div>

</body>
</html>