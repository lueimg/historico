<?php 
require_once("../../clases/class.OfficeTrack.php");

$objOT = new OfficeTrack();

$idTarea = $_GET['id'];
$arrTramas = $objOT->getTramasTarea($idTarea);

//var_dump($arrTramas);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Fechas</title>


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

.celdatit {
	font-family:tahoma, arial, sans-serif;
	font-size: 11px;
}

.celda {
	font-family:tahoma, arial, sans-serif;
	font-size: 11px;
	color: #000000;
}

</style>

<script type="text/javascript">

function verDetalle(id, paso) {

	//alert(paso);
	$( "#resDetalle" ).load( "sergio_detalle.php", 
		{ id: id, paso: paso }, 
		function() {
			//alert( "The last 25 entries in the feed have been loaded" );
	});
	
}
	
</script>
</head>

<body>

<div id="div_bus" class="divBusqueda" style="width: 500px;" >

	<table class="tablaBusqueda" style="width: 100%">
		<thead>
			<th colspan='4' style='text-align: center; '>Envios</th>
		</thead>
		
		<tr class="tr_busqueda">
			<td class="celdatit" style="width: 25%; background-color:red; padding: 5px;">ATC:</td >
			<td class="celdatit" style="width: 25%; background-color:red; padding: 5px;">Tecnico:</td >
			<td class="celdatit" style="width: 25%; background-color:red; padding: 5px;">Paso:</td >
			<td class="celdatit" style="width: 25%; background-color:red; padding: 5px;">Fecha Recepcion:</td >
		</tr>
		
		<?php 
		foreach($arrTramas as $filaTramas) {
		
		?>
		
		<tr class="tr_busqueda">
			
			<td style="background-color:white; padding: 5px; width: 40%; font-weight: bold;"  class="celda" >
				<?php echo $filaTramas["task_id"] ;?>
			</td>
			
			<td style="background-color:white; padding: 5px; width: 40%; font-weight: bold;"  class="celda" >
				<?php echo $filaTramas["cod_tecnico"] ;?>
			</td>	
			
			<td style="background-color:white; padding: 5px; width: 40%; font-weight: bold;"  class="celda" >
				<a href="#" onclick="verDetalle('<?php echo $filaTramas["id"]?>', '<?php echo $filaTramas["paso"]?>');">
				<?php echo $filaTramas["id"]."-".$filaTramas["paso"] ;?></a>
			</td>				
			
			<td style="background-color:white; padding: 5px; width: 40%; font-weight: bold;"  class="celda" >
				<?php echo $filaTramas["fecha_recepcion"] ;?>
			</td>	
		
		</tr>	
		
		<?php
		}
		?>
	</table>
	<br/>
	
	
	<table class="tablaBusqueda" style="width: 100%">
	</table>
</div>

<div id="resDetalle" class="divBusqueda" style="width: 500px;"></div>

	
</body>
</html>
