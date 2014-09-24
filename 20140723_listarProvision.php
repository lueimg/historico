<?php
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);
//echo date("Y-m-d H:i:s");

include ("../../clases/class.HistoricoCliente.php");

//var_dump($_REQUEST);

if (!isset($_REQUEST["telefonoCliente"])){
	$telefonoCliente = "14344587";
} else {
	$telefonoCliente = $_REQUEST["telefonoCliente"];
}

$obj = new HistoricoCliente();

?>

<head>

<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/jqueryui_1.8.2/js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript" >
	/*function verDetalle(tipo, negocio, actuacion) {

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
	*/
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
<table width="100%" cellpadding="0" cellspacing="0"  >
<thead>
	<th id="etiqueta">PETICION</th>
	<th id="etiqueta">COD CLI ATIS</th>
	<th id="etiqueta">DOC.ID</th>
	<th id="etiqueta">PS</th>
	<th id="etiqueta">PAQUETE</th>
	<th id="etiqueta">FECHA REGISTRO</th>
	<th id="etiqueta">ESTADO GESTEL</th>
	<th id="etiqueta">ESTADO CMS</th>
</thead>


<?php 

$arr = $obj->getRegistroAtis("fono", $telefonoCliente);
if (count($arr)>0) {
	foreach ($arr as $fila) {
		?>
		<tr>
		<td id="campo"><?php echo $fila["peticion"]?></td>
		<td id="campo"><?php echo $fila["id_cliente"]?></td>
		<td id="campo"><?php echo $fila["numdoc"]?></td>
		<td id="campo"><?php echo $fila["ps"]?></td>
		<td id="campo"><?php echo $fila["paquete"]?></td>
		<td id="campo"><?php echo $fila["fecreg"]?></td>
		<td id="campo"><?php echo $fila["estado_gestel"]?></td>
		<td id="campo"><?php echo $fila["estado_cms"]?></td>
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