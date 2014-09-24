<?php
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);


//echo date("Y-m-d H:i:s");

include ("../../clases/class.HistoricoCliente.php");

//var_dump($_REQUEST);

if (!isset($_REQUEST["telefonoCliente"])){
	$telefonoCliente = "12764764";
} else {
	$telefonoCliente = $_REQUEST["telefonoCliente"];
}

// Buscar el codigo de cliente CMS en tabla de maestro de abonados
if (!isset($_REQUEST["codigoServicioCMS"])){
	$codigoServicioCMS = "2773965";
} else {
	$codigoServicioCMS = $_REQUEST["codigoServicioCMS"];
}

$obj = new HistoricoCliente();

//$arrCatv = $obj->getAveriasCatvPendientes("cliente", "3237589");

?>

<head>

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" >
	function verDetalle(tipo, negocio, actuacion) {

		//alert("tipo = "+tipo+", negocio="+negocio+", actuacion="+actuacion);

		$("#resDetalle").load("listarAveriaDetalle.php", {
			tipo: tipo,
			negocio: negocio,
			actuacion: actuacion
		},
		function() {
			var cx = $("#txtCoordX").val();
			var cy = $("#txtCoordY").val();
			//alert("X = "+cx+"  Y = "+cy);

			var zonal =  $('#cmb_zonales').val();
			var mdf = $('#cmb_mdfs').val();
			var tipo_red =  $('#cmb_tipored').val();
			var cable_armario = $('#cmb_cable_armario').val();
			var caja_terminal =  $('#cmb_caja_terminal').val();			
		});


	}
</script>

<style >
	.divResInternoDerecho {

	/*position:relative; */
	float:left;
	width:33%; 
	padding: 20px;
   }
 </style>
</head>

<body>

<div id="resultado_historico" class="resultado_historico" style="display: table; width:98%">

<!--<div id="res1" name="res1" style="border: 1px solid blue; " class="divResInternoDerecho">-->
<div id="resListado" name="resListado" class="div_listado" style="border: 1px solid blue; float : left; width : 48%;">
<table width="100%">
<thead>
	<th>TIPO</th>
	<th>AVERIA</th>
	<th>FECHA REGISTRO</th>
	<th>ESTADO</th>
	<th>FECHA LIQUIDADA</th>
</thead>


<?php 

$arrTba = $obj->getAveriasTbaPendientes("fono", $telefonoCliente);
if (count($arrTba)>0) {
	foreach ($arrTba as $filaTba) {
		?>
		<tr>
		<td>TBA</td>
		<td><a href="#" onclick="verDetalle('<?php echo $filaTba["averia"]?>');"><?php echo $filaTba["averia"]?></a></td>
		<td><?php echo $filaTba["fecreg"]?></td>
		<td>Pendiente</td>
		<td>&nbsp;</td>
		</tr>
		<?php
	}
}

$arrAdslPen = $obj->getAveriasAdslPendientes("fono", $telefonoCliente);
if (count($arrAdslPen)>0) {
	foreach ($arrAdslPen as $filaAdslPen) {
		?>
		<tr>
		<td>ADSL</td>
		<td><a href="#" onclick="verDetalle('averia','adsl-pen', '<?php echo $filaAdslPen["averia"]?>');"><?php echo $filaAdslPen["averia"]?></a></td>	
		<td><?php echo $filaAdslPen["fecha_registro"]?></td>
		<td>Pendiente</td>
		<td><?php echo $filaAdslPen["fecha_de_liquidacion"]?></td>
		</tr>
		<?php
	}
}



if (substr($telefonoCliente,0,1)=="1") {   // LIMA
	$arrTbaLiq = $obj->getAveriasTbaLiquidadasLima("fono", $telefonoCliente);
	if (count($arrTbaLiq)>0) {
		foreach ($arrTbaLiq as $filaTbaLiq) {
			?>
			<tr>
			<td>TBA</td>
			<td><a href="#" onclick="verDetalle('averia','tba-liq','<?php echo $filaTbaLiq["averia"]?>');"><?php echo $filaTbaLiq["averia"]?></a></td>
			<td><?php echo $filaTbaLiq["fecha_registro"]?></td>
			<td>Liquidada</td>
			<td><?php echo $filaTbaLiq["fecha_de_liquidacion"]?></td>
			</tr>
			<?php
		}
	}
}
else {
	$arrTbaLiq = $obj->getAveriasTbaLiquidadasProvincia("fono", $telefonoCliente);
	if (count($arrTbaLiq)>0) {
		foreach ($arrTbaLiq as $filaTbaLiq) {
			?>
			<tr>
			<td>TBA</td>
			<td><a href="#" onclick="verDetalle('averia','tba-liqprovincia','<?php echo $filaTbaLiq["averia"]?>');"><?php echo $filaTbaLiq["averia"]?></a></td>
			<td><?php echo $filaTbaLiq["fecha_registro"]?></td>
			<td>Liquidada</td>
			<td><?php echo $filaTbaLiq["fecha_de_liquidacion"]?></td>
			</tr>
			<?php
		}
	}


}

$arrAdslLiq = $obj->getAveriasAdslLiquidadas("fono", $telefonoCliente);
if (count($arrAdslLiq)>0) {
	foreach ($arrAdslLiq as $filaAdslLiq) {
		?>
		<tr>
		<td>ADSL</td>
		<td><a href="#" onclick="verDetalle('averia','adsl-liq','<?php echo $filaAdslLiq["averia"]?>');"><?php echo $filaAdslLiq["averia"]?></a></td>
		<td><?php echo $filaAdslLiq["fecha_registro"]?></td>
		<td>Liquidada</td>
		<td><?php echo $filaAdslLiq["fecha_liquidacion"]?></td>
		</tr>
		<?php
	}
}

$arrCatvLiq = $obj->getAveriasCatvLiquidadas("codServicio", $codigoServicioCMS);
if (count($arrCatvLiq)>0) {
	foreach ($arrCatvLiq as $filaCatvLiq) {
		?>
		<tr>
		<td>CATV</td>
		<td><a href="#" onclick="verDetalle('averia','catv-liq','<?php echo $filaCatvLiq["averia"]?>');"><?php echo $filaCatvLiq["averia"]?></a></td>
		<td><?php echo $filaCatvLiq["fecharegistro"]?></td>
		<td>Liquidada</td>
		<td><?php echo $filaCatvLiq["fecha_liquidacion"]?></td>
		</tr>
		<?php
	}
}

?>
</table>
</div>

<div id="resDetalle" name="resDetalle" class="div_detalle" style="border: 1px solid red; float : right; width : 50%;">
<!--<div id="resDetalle" name="resDetalle"  >-->
</div>

</div>
</body>

</html>