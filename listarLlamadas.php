<?php
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);


//echo date("Y-m-d H:i:s");

include ("../../clases/class.HistoricoCliente.php");

//var_dump($_REQUEST);

if (!isset($_REQUEST["telefonoCliente"])){
	$telefonoCliente = "14340294";
} else {
	$telefonoCliente = $_REQUEST["telefonoCliente"];
}

$obj = new HistoricoCliente();

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

<div id="resultado_historico" class="resultado_historico" style="display: table; width:100%">

<div id="resListado" name="resListado" class="div_listado" style="border: 1px solid blue; float : left; width : 100%;">
<table width="100%">
<thead>
	<th>FECHA LLAMADA</th>
	<th>SERVICIO</th>
	<th>GES PRODUCTO</th>
	<th>GES SERVICIO</th>
	<th>GES ACCION</th>
	<th>OBSERVACIONES</th>
</thead>


<?php 

$arr = $obj->getLlamadasCliente("fono", $telefonoCliente);
if (count($arr)>0) {
	foreach ($arr as $fila) {
		?>
		<tr>
		<td><?php echo $fila["fecha_llamada1"]?></td>
		<td><?php echo $fila["servicio"]?></td>
		<td><?php echo $fila["ges_producto"]?></td>
		<td><?php echo $fila["ges_servicio"]?></td>
		<td><?php echo $fila["ges_accion"]?></td>
		<td><?php echo $fila["observacion"]?></td>
		</tr>
		<?php
	}
}

?>
</table>
</div>

</div>
</body>

</html>