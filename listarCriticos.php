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

// Buscar el codigo de servicio CMS en tabla de maestro de abonados
if (!isset($_REQUEST["codigoServicioCMS"])){
	$codigoServicioCMS = "2773965";
} else {
	$codigoServicioCMS = $_REQUEST["codigoServicioCMS"];
}


// Buscar el codigo de cliente CMS en tabla de maestro de abonados
if (!isset($_REQUEST["codigoClienteCMS"])){
	$codigoClienteCMS = "";
} else {
	$codigoClienteCMS = $_REQUEST["codigoClienteCMS"];
}


$obj = new HistoricoCliente();

//$arrCatv = $obj->getAveriasCatvPendientes("cliente", "3237589");

?>

<head>

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/jqueryui_1.8.2/js/jquery-ui-1.8.1.custom.min.js"></script>

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
<div id="resListado" name="resListado" class="div_listado" style="border: 1px solid white; float : left; width : 48%;">
<table width="100%">
<thead>
	<th>TIPO</th>
	<th>AVERIA</th>
	<th>FECHA INGRESO CRITICO</th>
	<th>QUIEBRE</th>
	<th>PENDIENTE</th>
</thead>


<?php 

$arrTba = $obj->getListadoCriticosCobre("fono", $telefonoCliente);
if (count($arrTba)>0) {
	foreach ($arrTba as $filaTba) {
		?>
		<tr>
		<td style='text-align: center'><?php echo $filaTba["tipo_actuacion"]?></td></td>
		<!--<td><a href="#" onclick="verDetalle('<?php echo $filaTba["averia"]?>');"><?php echo $filaTba["averia"]?></a></td>-->
		<td style='text-align: center'><?php echo $filaTba["averia"]?></a></td>
		<td style='text-align: center'><?php echo $filaTba["fecha_subida2"]?></td>
		<td style='text-align: center'><?php echo $filaTba["quiebre"]?></td>
		<td style='text-align: center'><?php echo $filaTba["esta_pendiente"]?></td>
		</tr>
		<?php
	}
}



?>
</table>
</div>

<div id="resDetalle" name="resDetalle" class="div_detalle" style="border: 1px solid white; float : right; width : 50%;">
<!--<div id="resDetalle" name="resDetalle"  >-->
</div>

</div>
</body>

</html>