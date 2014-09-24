<?php 
require_once("../../clases/class.HistoricoCliente.php");

$objHis = new HistoricoCliente();


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
	
</script>
</head>

<body>
<br/>

<div id="div_bus" class="divBusqueda" style="width: 400px" >


	<table class="tablaBusqueda" style="width: 100%">
		<thead>
			<th colspan='1'>&nbsp;</th>
			<th colspan='1' style="text-align: center;">Averias</th>
			<th colspan='1' style="text-align: center;">Provision</th>
		</thead>
		<tr class="tr_busqueda">
			<td class="celdatit" style="width: 25%; background-color:red; padding: 5px;">Data Criticos:</td >
			<td style="background-color:white; padding: 5px; width: 40%; font-weight: bold;"  class="celda" >
				<?php echo $objHis->getFechaUpdate_CriticosAverias() ;?>
			</td>
			<td style="background-color:white; padding: 5px; width: 35%; font-weight: bold;"  class="celda" >
				<?php echo $objHis->getFechaUpdate_CriticosAverias();?>
			</td>
		
		</tr>	
	</table>
	<br/>
	<table class="tablaBusqueda" style="width: 100%">
		<thead>
			<th colspan='1'>&nbsp;</th>
			<th colspan='1' style="text-align: center;">Fechas de actualizacion</th>
			<th colspan='1' style="text-align: center;">Ultima actuacion</th>
		</thead>	
	

	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">Averias Basica:</td >
		<td style="background-color:white; padding: 5px; width: 40%"  class="celda" >
		<?php echo $objHis->getFechaUpdate_AveriasBasicaPendiente() ;?>
		</td>
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getLastAveria_BasicaPendiente();?>
		</td>
		
		</td >
	</tr>
	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">Averias ADSL:</td >
		<td style="background-color:white; padding: 5px; width: 40%" class="celda" >
		<?php echo $objHis->getFechaUpdate_AveriasAdslPendiente(); ?>
		</td>
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getLastAveria_AdslPendiente(); ?>
		</td >
	</tr>
	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">Averias CATV:</td >
		<td style="background-color:white; padding: 5px; width: 40%" class="celda" >
		<?php echo $objHis->getFechaUpdate_AveriasCatvPendiente(); ?>
		</td >
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getLastAveria_CatvPendiente(); ?>
		</td>
	</tr>
	
	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">&nbsp;Llamadas:</td >
		<td style="background-color:white; padding: 5px; width: 40%" class="celda" >
		<?php echo $objHis->getFechaUpdate_Llamadas(); ?>
		</td >
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getFechaUpdate_Llamadas(); ?>
		</td>
	</tr>	

	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">&nbsp;Agendas WU Averias:</td >
		<td style="background-color:white; padding: 5px; width: 40%" class="celda" >
		<?php echo $objHis->getFechaUpdate_AgendasWU_Averias(); ?>
		</td >
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getFechaUpdate_AgendasWU_Averias(); ?>
		</td>
	</tr>	
	<tr class="tr_busqueda">
		<td style="width: 25%; padding: 5px;" class="celdatit">&nbsp;Agendas WU Provision:</td >
		<td style="background-color:white; padding: 5px; width: 40%" class="celda" >
		<?php echo $objHis->getFechaUpdate_AgendasWU_Provision(); ?>
		</td >
		<td style="background-color:white; padding: 5px; width: 35%"  class="celda" >
		<?php echo $objHis->getFechaUpdate_AgendasWU_Provision(); ?>
		</td>
	</tr>	
	<!--
	<tr class="tr_busqueda">
		<td style="width: 35%" class="celdatit">Provision Basica:</td >
		<td style="background-color:white; padding: 5px;" class="celda" ></td >
	</tr>
	<tr class="tr_busqueda">
		<td style="width: 35%" class="celdatit">Provision ADSL:</td >
		<td style="background-color:white; padding: 5px;" class="celda" ></td >
	</tr>
	<tr class="tr_busqueda">
		<td style="width: 35%" class="celdatit">Provision CATV:</td >
		<td style="background-color:white; padding: 5px;" class="celda" ></td >
	</tr>
	-->

	</table>
</div>
	
</body>
</html>