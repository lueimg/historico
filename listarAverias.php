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
   

tr {
	border: 1px solid #6CA6D1;
}
   
.tabla_resultados { 
	color: #000;
	text-align: center;
	border-collapse:collapse;
	border: 1px solid #000000;	
	background: #FFFFFF;
	width: 100%;
}

.celda { 
  font: normal 10px Arial, Helvetica, sans-serif;
  color: #000099 ;
  padding: 1px;

}

    #agendarVisita button{
        cursor: pointer;
    }

 </style>
</head>

<body>

<div id="resultado_historico" class="resultado_historico" style="display: table; width:90%; border: 0px solid red; float: left;">

<div id="resultado_historico2" class="resultado_historico" style="display: table; border: 0px solid green; width: 75%; float: left">

<div id="resListadoPend" name="resListado" class="div_listado1" style="border: 0px solid white; width: 100%;">


<table class='tabla_resultados'>
<thead>
	<th>TIPO</th>
	<th>AVERIA</th>
	<th>FECHA REGISTRO</th>
	<th>ESTADO</th>
	<th>ACCIONES</th>
</thead>
<?php 

$arrTotal = array();
$arrTotal2 = array();
$arrTotal3 = array();
$arrTotal4 = array();

$arrTba = $obj->getAveriasTbaPendientes("fono", $telefonoCliente);
if (count($arrTba)>0) {
	foreach ($arrTba as $filaTba) {

		?>
		<tr>
		<td>TBA</td>
		<td><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px'>
			<a href="#" onclick="verDetalle('<?php echo $filaTba["averia"]?>');"><?php echo $filaTba["averia"]?></a>
		</span</td>
		<td><?php echo $filaTba["fecreg"]?></td>
		<td>Pendiente
        <?php
        $accion = "Agendar Visita";
         if($filaTba["id_atc"]){

             print "<br>" . $filaTba["id_atc"];
             print "<br>Agenda:<br> " . $filaTba["fecha_agenda"] . "<br>". $filaTba["horario"];
             $accion = "Reagendar Visita";


         }

        ?>
        </td>
                <td>
                    <?php
                    if ( $_POST['esCritico']==='false' && empty($filaTba["id_atc"]) ) {
                        echo "<a href=\"" . trim($filaTba["averia"]) . "\" class=\"rmanual\" title=\"rutina-bas-lima\" >";
						echo "<span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color:#FF0000; font-size: 12px'>";
						echo "Registro manual</span></a>";
                    }else
                    {
                    ?>
                        <?php if($filaTba["n_evento"] == 0):?>
                        <div id='agendarVisita' name='agendarVisita' >
                            <button type="button" id="btn_cliente_critico"
                                    style="margin-top:5px;background:red;border:0;border-radius:4px;color:#fff;padding:5px"><?=$accion;?></button>
                        </div>
                        <?php endif; ?>
                    <?php
                    }
                    ?>
                </td>
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
		<td>
			<span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px'>
			<a href="#" onclick="verDetalle('averia','adsl-pen', '<?php echo $filaAdslPen["averia"]?>');"><?php echo $filaAdslPen["averia"]?>
			</a>
			</span>
		</td>	
		<td><?php echo $filaAdslPen["fecha_registro"]?></td>
		<td>Pendiente</td>
		<td><?php echo $filaAdslPen["fecha_de_liquidacion"]?></td>
                <td>
                    <?php
                    if ( $_POST['esCritico']==='false' ) {
                        echo "<a href=\"" . trim($filaAdslPen["averia"]) . "\" class=\"rmanual\" title=\"rutina-adsl-pais\">";
						echo "<span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color:#FF0000; font-size: 12px'>";
						echo "Registro manual</span></a>";
                    }
                    ?>
                </td>
		</tr>
		<?php
	}
}

$arrCatvPen = $obj->getAveriasCatvPendientes("cliente", $codigoClienteCMS);
if (count($arrCatvPen)>0) {
	foreach ($arrCatvPen as $filaCatvPen) {
		?>
		<tr>
		<td>CATV</td>
		<td>
			<span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px'>
			<a href="#" onclick="verDetalle('averia','adsl-pen', '<?php echo $filaCatvPen["averia"]?>');"><?php echo $filaCatvPen["averia"]?>
			</span>
			</a>
		</td>	
		<td><?php echo $filaCatvPen["fecha_registro"]?></td>
		<td>Pendiente</td>
		<td><?php echo $filaCatvPen["fecha_de_liquidacion"]?></td>
                <td>
                    <?php
                    if ( $_POST['esCritico']==='false' ) {
                        echo "<a href=\"" . trim($filaCatvPen["averia"]) . "\" class=\"rmanual\" title=\"rutina-catv-pais\">Registro manual</a>";
						echo "<span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color:#FF0000; font-size: 12px'>";						
						echo "Registro manual</span></a>";
                    }
                    ?>
                </td>
		</tr>
		<?php
	}
}

// LIQUIDADAS
if (substr($telefonoCliente,0,1)=="1") {   // LIMA
	$arrTbaLiq = $obj->getAveriasTbaLiquidadasLima("fono", $telefonoCliente);
	if (count($arrTbaLiq)>0) {
		foreach ($arrTbaLiq as $filaTbaLiq) {
			$arrTotal2["tipo"] = "TBA";
			$arrTotal2["averia"] = $filaTbaLiq["averia"];
			$arrTotal2["fecha_registro"] = $filaTbaLiq["fecha_reporte"];
			$arrTotal2["estado"] = "Liquidada";
			$arrTotal2["fecha_liquidacion"] = $filaTbaLiq["fecha_de_liquidacion"];
			array_push($arrTotal, $arrTotal2);
		}
	}
}
else {
	$arrTbaLiq = $obj->getAveriasTbaLiquidadasProvincia("fono", $telefonoCliente);
	if (count($arrTbaLiq)>0) {
		foreach ($arrTbaLiq as $filaTbaLiq) {
			$arrTotal2["tipo"] = "TBA";
			$arrTotal2["averia"] = $filaTbaLiq["averia"];
			$arrTotal2["fecha_registro"] = $filaTbaLiq["fecha_registro"];
			$arrTotal2["estado"] = "Liquidada";
			$arrTotal2["fecha_liquidacion"] = $filaTbaLiq["fecha_de_liquidacion"];
			array_push($arrTotal, $arrTotal2);		
		}
	}
}

$arrAdslLiq = $obj->getAveriasAdslLiquidadas("fono", $telefonoCliente);
if (count($arrAdslLiq)>0) {
	foreach ($arrAdslLiq as $filaAdslLiq) {
		$arrTotal3["tipo"] = "ADSL";
		$arrTotal3["averia"] = $filaAdslLiq["averia"];
		$arrTotal3["fecha_registro"] = $filaAdslLiq["fecha_registro"];
		$arrTotal3["estado"] = "Liquidada";
		$arrTotal3["fecha_liquidacion"] = $filaAdslLiq["fecha_liquidacion"];	
		array_push($arrTotal, $arrTotal3);		
	}
}

$arrCatvLiq = $obj->getAveriasCatvLiquidadas("codServicio", $codigoServicioCMS);
if (count($arrCatvLiq)>0) {
	foreach ($arrCatvLiq as $filaCatvLiq) {
		$arrTotal4["tipo"] = "CATV";
		$arrTotal4["averia"] = $filaCatvLiq["averia"];
		$arrTotal4["fecha_registro"] = $filaCatvLiq["fecharegistro"];
		$arrTotal4["estado"] = "Liquidada";
		$arrTotal4["fecha_liquidacion"] = $filaCatvLiq["fecha_liquidacion"];		
		array_push($arrTotal, $arrTotal4);	
	}
}
echo "</table>";
?>
<br/>
** Listado de aver&iacute;as a 120 dias (4 meses)
</div>
<div id="resListado" name="resListado" class="div_listadox" style="border: 0px solid white; width : 90%;">

<table class='tabla_resultados'>
<thead>
	<th>TIPO</th>
	<th>AVERIA</th>
	<th>FECHA REGISTRO</th>
	<th>ESTADO</th>
	<th>FECHA LIQUIDADA</th>
</thead>

<?php


function cmp($a, $b)
{
    // orden DESCENDENTE
	return strcmp($a["fecha_liquidacion"], $b["fecha_liquidacion"])*-1;
}

usort($arrTotal, "cmp");

foreach ($arrTotal as $filaTotal) {
	if ($filaTotal["tipo"]=='ADSL')
		$tipo='adsl-liq';
	else if ($filaTotal["tipo"]=='CATV')
		$tipo='catv-liq';
	else if ($filaTotal["tipo"]=='TBA')
		$tipo='tba-liq';	
		
?>
	<tr>
	<td class='celda'><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px;'>
		<?php echo $filaTotal["tipo"]?>&nbsp;</span>
	</td>
	<td class='celda'><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px'>
		<a href="#" onclick="verDetalle('averia','<?php echo $tipo;?>','<?php echo $filaTotal["averia"]?>');">
			<?php echo $filaTotal["averia"]?>
		</a>
		<?php
		if ($obj->esAveriaCritica($filaTotal["averia"]) == true) {
			echo "<img src='img/dialog_warning.png' style='width:21px;height:21px;vertical-align:middle'
				alt='critico' title='critico' >";
		}
		?>
		</span>
	</td>
	<td class='celda'><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 11px'><?php echo $filaTotal["fecha_registro"]?></span></td>
	<td class='celda'><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 10px'><?php echo $filaTotal["estado"]?></span></td>
	<td class='celda'><span style='font-family: Tahoma, Verdana, Segoe, sans-serif; font-weight: bold; color: grey; font-size: 10px'><?php echo $filaTotal["fecha_liquidacion"]?></span></td>
	</tr>	
<?php
}


?>
</table>
</div>

</div>

<div id="resDetalle" name="resDetalle" class="div_detalle" style="border: 0px solid brown ; width : 45%;"></div>


</div>
</body>

</html>