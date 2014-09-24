<?php
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);


//echo date("Y-m-d H:i:s");

include ("../../clases/class.HistoricoCliente.php");

//var_dump($_REQUEST);

if (!isset($_REQUEST["telefonoCliente"])){
	$telefonoCliente = "14436673";
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
<table>
<thead>
	<th>TIPO</th>
	<th>AVERIA</th>
	<th>FECHA REGISTRO</th>
	<th>ESTADO</th>
	<th>FECHA LIQUIDADA</th>
</thead>


<?php
// INICIAMOS
$i=0;
$arrTba = $obj->getAveriasTbaPendientes("fono", $telefonoCliente);
if (count($arrTba)>0) {
	foreach ($arrTba as $filaTba) {
		$arrTotal[$i]["negocio"] = "TBA";
		$arrTotal[$i]["negocio2"] = "tba-pen";
		$arrTotal[$i]["averia"] = $filaTba["averia"];
		$arrTotal[$i]["fec_reg"] = $filaTba["fecreg"];
		$arrTotal[$i]["fec_liq"] = "";
		$arrTotal[$i]["estado"] = "Pendiente";
		$i++;
	}
}

// Liquidadas Basica: Ver si es LIMA o PROVINCIA
if (substr($telefonoCliente,0,1)=="1") {   // LIMA
	$arrTbaLiq = $obj->getAveriasTbaLiquidadasLima("fono", $telefonoCliente);
	if (count($arrTbaLiq)>0) {
		foreach ($arrTbaLiq as $filaTbaLiq) {
			$arrTotal[$i]["negocio"] = "TBA";
			$arrTotal[$i]["negocio2"] = "tba-liq";
			$arrTotal[$i]["averia"] = $filaTbaLiq["averia"];
			$arrTotal[$i]["fec_reg"] = $filaTbaLiq["fecha_registro"];
			$arrTotal[$i]["fec_liq"] = $filaTbaLiq["fecha_de_liquidacion"];
			$arrTotal[$i]["estado"] = "Liquidada";
			$i++;
		}
	}
}
else { 
	$arrTbaLiqProv = $obj->getAveriasTbaLiquidadasProvincia("fono", $telefonoCliente);
	if (count($arrTbaLiqProv)>0) {
		foreach ($arrTbaLiqProv as $filaTbaLiqProv) {
			$arrTotal[$i]["negocio"] = "TBA";
			$arrTotal[$i]["negocio2"] = "tba-liqprovincia";
			$arrTotal[$i]["averia"] = $filaTbaLiq["averia"];
			$arrTotal[$i]["fec_reg"] = $filaTbaLiq["fecha_registro"];
			$arrTotal[$i]["fec_liq"] = $filaTbaLiq["fecha_de_liquidacion"];
			$arrTotal[$i]["estado"] = "Liquidada";
			$i++;
		}
	}
}


if (count($arrAdslPen)>0) {
	foreach ($arrAdslPen as $filaAdslPen) {
		$arrTotal[$i]["negocio"] = "ADSL";
		$arrTotal[$i]["negocio2"] = "adsl-pen";
		$arrTotal[$i]["averia"] = $filaAdslPen["averia"];
		$arrTotal[$i]["fec_reg"] = $filaAdslPen["fecha_registro"];
		$arrTotal[$i]["fec_liq"] = "";
		$arrTotal[$i]["estado"] = "Pendiente";
		$i++;
	}
}

$arrAdslLiq = $obj->getAveriasAdslLiquidadas("fono", $telefonoCliente);
if (count($arrAdslLiq)>0) {
	foreach ($arrAdslLiq as $filaAdslLiq) {
		$arrTotal[$i]["negocio"] = "ADSL";
		$arrTotal[$i]["negocio2"] = "adsl-liq";
		$arrTotal[$i]["averia"] = $filaAdslLiq["averia"];
		$arrTotal[$i]["fec_reg"] = $filaAdslLiq["fecha_registro"];
		$arrTotal[$i]["fec_liq"] = $filaAdslLiq["fecha_liquidacion"];
		$arrTotal[$i]["estado"] = "Liquidada";
		$i++;
	}
}


if (count($arrCatv)>0) {
	foreach ($arrCatv as $filaCatvPen) {
		$arrTotal[$i]["negocio"] = "CATV";
		$arrTotal[$i]["negocio2"] = "catv-pen";
		$arrTotal[$i]["averia"] = $filaCatvPen["averia"];
		$arrTotal[$i]["fec_reg"] = $filaCatvPen["fecharegistro"];
		$arrTotal[$i]["fec_liq"] = "";
		$arrTotal[$i]["estado"] = "Pendiente";
		$i++;
	}
}

$arrCatvLiq = $obj->getAveriasCatvLiquidadas("codServicio", $codigoServicioCMS);
if (count($arrCatvLiq)>0) {
	foreach ($arrCatvLiq as $filaCatvLiq) {
		$arrTotal[$i]["negocio"] = "CATV";
		$arrTotal[$i]["negocio2"] = "catv-liq";
		$arrTotal[$i]["averia"] = $filaCatvLiq["averia"];
		$arrTotal[$i]["fec_reg"] = $filaCatvLiq["fecharegistro"];
		$arrTotal[$i]["fec_liq"] = $filaCatvLiq["fecha_liquidacion"];
		$arrTotal[$i]["estado"] = "Liquidada";
		$i++;
	}
}




// Ordenamos el Array por fecha de registro descendente

function build_sorter($key, $dir='ASC') {
    return function ($a, $b) use ($key, $dir) {
        $t1=strtotime(is_array($a)?$a[$key]:$a->$key);
        $t2=strtotime(is_array($b)?$b[$key]:$b->$key);
        if($t1==$t2) return 0;
        return (strtoupper($dir)=='ASC'?($t1 < $t2):($t1 > $t2)) ? -1 : 1;
    };
}


// $sort - key or property name 
// $dir - ASC/DESC sort order or empty
usort($arrTotal, build_sorter("fec_reg", "DESC"));


foreach ($arrTotal as $filaTotal) {
	?>
	<tr>
	<td><?php echo $filaTotal["negocio"]?></td>
	<td><a href="#" onclick="verDetalle('<?php echo $filaTotal["negocio"]?>','<?php echo $filaTotal["negocio2"]?>','<?php echo $arrTotal["averia"]?>');"><?php echo $filaTotal["averia"]?>
	</a>
	</td>
	<td><?php echo $filaTotal["fec_reg"]?></td>
	<td><?php echo $filaTotal["estado"]?></td>>
	<td><?php echo $filaTotal["fec_liq"]?></td>
	</tr>
	<?php
}


//print_r($arrTotal);

?>
</table>
</div>

<div id="resDetalle" name="resDetalle" class="div_detalle" style="border: 1px solid red; float : right; width : 50%;">
<!--<div id="resDetalle" name="resDetalle"  >-->
</div>

</div>
</body>

</html>