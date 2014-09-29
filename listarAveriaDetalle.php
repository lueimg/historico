<style>
.tabla_resultados2 { 
	color: #000;
	text-align: left;
	border-collapse:collapse;
	border: 1px solid #6CA6D1;	
	background: #FFFFFF;
	width: 50%;
}
.filatr {
	border: 1px solid #6CA6D1;
}

.celdatit1 { 
  font: normal 11px Arial, Helvetica, sans-serif;
  color: red ;
  padding: 1px;
  font-weight: bold;

}

.celda2 { 
  font: normal 11px Arial, Helvetica, sans-serif;
  color: #000000 ;
  padding: 1px;

}

</style>

<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$deb = 1;

include ("../../clases/class.HistoricoCliente.php");


$hoy = date("Y-m-d H:i:s");
$hoyFormato = date("d-m-Y H:i:s");
$hoyFecha = date("Y-m-d");

$obj = new HistoricoCliente();

$tipo = trim($_POST["tipo"]);
$negocio = trim(strtoupper($_POST["negocio"]));
$actuacion = trim(strtoupper($_POST["actuacion"]));


//echo "tipo = ".$tipo.", negocio = ".$negocio.", actuacion=".$actuacion;


if ($tipo=="averia" ) {
	//echo "XXX";
	switch ($negocio) {
		case 'CATV-LIQ':
			$arrDetalle= $obj->getAveriasCatvLiquidadas($tipo, $actuacion);	
			//var_dump($arrDetalle);		
			?>
			<span style="color: red; font-weight: bold;">AVERIAS CATV LIQUIDADAS</span>
			<table class='tabla_resultados2' >
				<tr>
					<td id="etiqueta" class='celda1'>Tipo Requerimiento</td><td id="campo" class='celda'><?php echo $arrDetalle[0]["codigotiporeq"]?></td>
					<td id="etiqueta" class='celda1'>Motivo Requerimiento</td><td id="campo"><?php echo $arrDetalle[0]["codigomotivoreq"]?></td>
					<td id="etiqueta" class='celda1'>Codigo Cliente</td><td id="campo"><?php echo $arrDetalle[0]["cod_cliente"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Categoria</td><td id="campo"><?php echo $arrDetalle[0]["categoria_cliente"]?></td>
					<td id="etiqueta">Codigo Servicio</td><td id="campo"><?php echo $arrDetalle[0]["cod_servicio"]?></td>
					<td id="etiqueta">Oficina Administrativa</td><td id="campo"><?php echo $arrDetalle[0]["oficinaadministrativa"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Departamento</td><td id="campo"><?php echo $arrDetalle[0]["departamento"]?></td>
					<td id="etiqueta">Provincia</td><td id="campo"><?php echo $arrDetalle[0]["provincia"]?></td>				
					<td id="etiqueta">Direccion</td><td id="campo"><?php echo $arrDetalle[0]["direccion"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Fecha Registro</td><td id="campo"><?php echo $arrDetalle[0]["fecharegistro"]?></td>
					<td id="etiqueta">Fecha Asignacion</td><td id="campo"><?php echo $arrDetalle[0]["fechaasignacion"]?></td>
					<td id="etiqueta">Contrata</td><td id="campo"><?php echo $arrDetalle[0]["contrata"]?></td>	
				</tr>
				<tr>			
					<td id="etiqueta">Tecnico</td><td id="campo"><?php echo $arrDetalle[0]["tecnico"]?></td>
					<td id="etiqueta">Fecha Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["fecha_liquidacion"]?></td>
					<td id="etiqueta">Codigo Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["codigodeliquidacion"]." / ".$arrDetalle[0]["detalle_liquidacion"]?></td>
				</tr>
			</table>
		<?php
			break;
		case 'TBA-LIQ':
			$arrDetalle= $obj->getAveriasTbaLiquidadasLima($tipo, $actuacion);	
			//var_dump($arrDetalle);		
			?>
			<span style="color: red; font-weight: bold;">AVERIAS TBA LIMA LIQUIDADAS</span>
			<table class='tabla_resultados2' >
				<tr class="filatr">
					<td id="etiqueta" class='celdatit1'>Averia</td><td id="campo" class='celda2'><?php echo $arrDetalle[0]["averia"]?></td>
					<td id="etiqueta" class='celdatit1'>Telefono</td><td id="campo" class='celda2'><?php echo $arrDetalle[0]["telefono"]?></td>
					<td id="etiqueta" class='celdatit1'>Inscripcion</td><td id="campo" class='celda2'><?php echo $arrDetalle[0]["inscripcion"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Observacion 102</td><td id="campo"><?php echo $arrDetalle[0]["observacion_102"]?></td>
					<td id="etiqueta">Observacion #2</td><td id="campo"><?php echo $arrDetalle[0]["otra_observacion"]?></td>
					<td id="etiqueta">Numero Comprobacion</td><td id="campo"><?php echo $arrDetalle[0]["numero_comprobacion"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">MDF</td><td id="campo"><?php echo $arrDetalle[0]["mdf"]?></td>
					<td id="etiqueta">Area</td><td id="campo"><?php echo $arrDetalle[0]["area_sig"]?></td>				
					<td id="etiqueta">FFTT</td><td id="campo"><?php echo $arrDetalle[0]["armario"]." ".$arrDetalle[0]["cable"]." ".$arrDetalle[0]["bloque"]." ".$arrDetalle[0]["terminal"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Fecha Registro</td><td id="campo"><?php echo $arrDetalle[0]["fecha_registro"]?></td>
					<td id="etiqueta">Fecha Comprobacion</td><td id="campo"><?php echo $arrDetalle[0]["fecha_de_comprobacion"]?></td>
					<td id="etiqueta">Numero Comprobacion</td><td id="campo"><?php echo $arrDetalle[0]["numero_comprobacion"]?></td>	
				</tr>
				<tr>			
					<td id="etiqueta">Tecnico</td><td id="campo"><?php echo $arrDetalle[0]["tecnico_liquidacion"]?></td>
					<td id="etiqueta">Fecha Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["fecha_de_liquidacion"]?></td>
					<td id="etiqueta">Codigo Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["liquidacion_"]." / ".$arrDetalle[0]["detalle"]?></td>
				</tr>
			</table>			
		<?php break;
		case 'ADSL-LIQ':
			$arrDetalle= $obj->getAveriasAdslLiquidadas($tipo, $actuacion);	
			//var_dump($arrDetalle);		
			?>
			<span style="color: red; font-weight: bold;">AVERIAS ADSL LIMA LIQUIDADAS</span>
			<table>
				<tr>
					<td id="etiqueta">Averia</td><td id="campo"><?php echo $arrDetalle[0]["averia"]?></td>
					<td id="etiqueta">Telefono</td><td id="campo"><?php echo $arrDetalle[0]["telefono"]?></td>
					<td id="etiqueta">Inscripcion</td><td id="campo"><?php echo $arrDetalle[0]["inscripcion"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Nombre Contacto</td><td id="campo"><?php echo $arrDetalle[0]["nombre_contacto"]?></td>
					<td id="etiqueta">Zonal</td><td id="campo"><?php echo $arrDetalle[0]["zonal"]?></td>
					<td id="etiqueta">Franqueo</td><td id="campo"><?php echo $arrDetalle[0]["franqueo"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">MDF</td><td id="campo"><?php echo $arrDetalle[0]["mdf"]?></td>
					<td id="etiqueta">Area</td><td id="campo"><?php echo $arrDetalle[0]["estado_liq"]?></td>				
					<td id="etiqueta">FFTT</td><td id="campo"><?php echo $arrDetalle[0]["cable"]." ".$arrDetalle[0]["sector"]." "
						.$arrDetalle[0]["nro_caja"]." ".$arrDetalle[0]["par_distribuidor"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Fecha Registro</td><td id="campo"><?php echo $arrDetalle[0]["fecha_registro"]?></td>
					<td id="etiqueta">Fecha Despacho</td><td id="campo"><?php echo $arrDetalle[0]["fecha_despacho"]?></td>
					<td id="etiqueta">Fecha Prog Tecnico</td><td id="campo"><?php echo $arrDetalle[0]["fecha_pro_tec"]?></td>	
				</tr>
				<tr>			
					<td id="etiqueta">Tecnico</td><td id="campo"><?php echo $arrDetalle[0]["tecnico_rep_4"]?></td>
					<td id="etiqueta">Fecha Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["fecha_liquidacion"]?></td>
					<td id="etiqueta">Codigo Liquidacion</td><td id="campo"><?php echo $arrDetalle[0]["codigo_liquidacion"]." / ".$arrDetalle[0]["observacion_liquidacion"]?></td>
				</tr>
			</table>			
		<?php break;		
		
		case 'ADSL-PEN':
			$arrDetalle = null;
			$arrDetalle = $obj->getAveriasAdslPendientes($tipo, $actuacion);	
			//var_dump($arrDetalle);		
			?>
			<span style="color: red; font-weight: bold;">AVERIA ADSL PENDIENTE</span>
			<table>
				<tr>
					<td id="etiqueta">Averia</td><td id="campo"><?php echo $arrDetalle[0]["averia"]?></td>
					<td id="etiqueta">Telefono</td><td id="campo"><?php echo $arrDetalle[0]["telefono"]?></td>
					<td id="etiqueta">Inscripcion</td><td id="campo"><?php echo $arrDetalle[0]["inscripcion"]?></td>
				</tr>

				<tr>
					<td id="etiqueta">MDF</td><td id="campo"><?php echo $arrDetalle[0]["mdf"]?></td>
					<td id="etiqueta">Zonal</td><td id="campo"><?php echo $arrDetalle[0]["zonal"]?></td>				
					<td id="etiqueta">FFTT</td><td id="campo"><?php echo $arrDetalle[0]["cable"]." ".$arrDetalle[0]["sector"]." ".$arrDetalle[0]["nro_caja"]." ".$arrDetalle[0]["borne"]?></td>
				</tr>
				<tr>
					<td id="etiqueta">Fecha Registro</td><td id="campo"><?php echo $arrDetalle[0]["fecha_registro"]?></td>
					<td id="etiqueta">Fecha Despacho</td><td id="campo"><?php echo $arrDetalle[0]["fecha_des"]?></td>
					<td id="etiqueta">Fecha Instalacion</td><td id="campo"><?php echo $arrDetalle[0]["fec_inst"]?></td>	
				</tr>
				<tr>
					<td id="etiqueta">Indicador Vip</td><td id="campo"><?php echo $arrDetalle[0]["indicador_vip"]?></td>
					<td id="etiqueta">Area</td><td id="campo"><?php echo $arrDetalle[0]["area"]?></td>
					<td id="etiqueta">Direccion Instalacion</td><td id="campo"><?php echo $arrDetalle[0]["direccion_instalacion"]?></td>	
				</tr>				
			</table>			
		<?php break;		
		
		
		default:
			echo "HOLA";
			break;
	}
}