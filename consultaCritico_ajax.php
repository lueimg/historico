<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include ("../../clases/class.Conexion.php");

$objCnx = new Conexion();


if ($_POST['action']=='buscar')
{
	$cnx = $objCnx->conectarBD();
	
	$cmbTipo = $_POST["cmbTipo"];
	$txtBus = $_POST["txtBus"];
	
	switch($cmbTipo) {
		case 1:
			// Por telefono	
			$filtroBusqueda = " AND telefono='$txtBus' ";
			break;
		case 2:
			// Por Averia	
			$filtroBusqueda = " AND averia='$txtBus' ";
			break;
	}


	$cad1 = "SELECT * FROM webpsi_criticos.`gestion_averia` WHERE 1=1 $filtroBusqueda LIMIT 1";
	//echo $cad1;
	$res1 = mysql_query($cad1) or die("Error 1: ".mysql_error());
	$res1_results = mysql_num_rows($res1); 
	//echo $res1_results;
	
	if ($res1 && $res1_results>0 ) {
		$arrAveria = mysql_fetch_array($res1, MYSQL_ASSOC);
	

		//var_dump($arrAveria);
		
		$idGestion = $arrAveria["id_gestion"];
		//echo "Idgestion = ".$idGestion;
		
		$cad2 = "SELECT a.*, e.estado, h.horario FROM webpsi_criticos.`gestion_criticos` a, webpsi_criticos.estados e, webpsi_criticos.horarios h
				WHERE a.id=$idGestion AND e.id=a.id_estado AND h.id=a.id_horario LIMIT 1";
		$res2 = mysql_query($cad2) or die("Error 2: ".mysql_error());
		
		$arrCabecera = mysql_fetch_array($res2, MYSQL_ASSOC);
		
		$cad3 = "SELECT a.*, e.estado, h.horario, m.motivo, m2.submotivo, u.usuario,
				 date_format(a.fecha_movimiento, '%d-%m-%Y %H:%i:%s') as fecha_mov2,
				 date_format(a.fecha_agenda, '%d-%m-%Y') as fecha_agenda2
				 FROM webpsi_criticos.`gestion_movimientos` a, webpsi_criticos.estados e, webpsi_criticos.horarios h,
				 webpsi.tb_usuario u,
				 webpsi_criticos.motivos m, webpsi_criticos.submotivos m2
				 WHERE id_gestion=1393 AND e.id=a.id_estado AND h.id=a.id_horario 
				 AND m.id=a.id_motivo AND m2.id=a.id_submotivo
				 AND u.id=a.id_usuario
				 ORDER BY fecha_movimiento DESC ";
		$res3 = mysql_query($cad3) or die(mysql_error());
		
		while ($row3 = mysql_fetch_array($res3, MYSQL_ASSOC))
			$arrMovimientos[] = $row3;
		
		//var_dump($arrCabecera);
		//var_dump($arrMovimientos);
		
		//echo "1";
		
		?>
		

<style>

.celda0 {
	background-color: #6E9CC8; padding: 3px;
	color: #FFFFFF;
	border: 1px solid #17688B;
	font-size: 11px;
}


.celda1 {
	background-color: white; padding: 3px;
	color: #2A4266;
	border: 1px solid #6297BC;
	font-size: 11px;
}

.celda2 {
	background-color: white; padding: 3px;
	color: #2A4266;
	border: 1px solid #6297BC;
	font-size: 10px;
	font: Verdana,Arial,Helvetica,sans-serif;
}

</style>

		
		<div id="div_bus" class="divBusqueda" style="width: 750px" >

			<table class="tablaBusqueda" style='width: 90%'>
				<thead>
					<th colspan='4'>Datos del cliente</th>
				</thead>	
			
				<tr class="tr_busqueda">
					<td class="celda0">ATC:</td ><td class="celda1"><?php echo $arrCabecera["id_atc"]?></td>
					<td class="celda0">Averia:</td ><td class="celda1"><?php echo $arrAveria["averia"]?></td>
				</tr>
				<tr>
					<td class="celda0">Nombre del cliente:</td >
					<td class="celda1" colspan='3'><?php echo ucwords(strtolower($arrCabecera["nombre_cliente_critico"]))?></td>
				</tr>
				<tr class="tr_busqueda">
					<td class="celda0">Fecha registro averia:</td ><td class="celda1"><?php echo $arrAveria["fecha_registro"]?></td>
					<td class="celda0">Fecha creacion ATC:</td ><td class="celda1"><?php echo $arrCabecera["fecha_creacion"]?></td>
				</tr>	
				<tr class="tr_busqueda">
					<td class="celda0">Estado:</td >
					<td class="celda1"><?php echo $arrCabecera["estado"]?></td>
					<td class="celda0">Fecha Agenda:</td >
					<td class="celda1"><?php echo $arrCabecera["fecha_agenda"]." / T.".$arrCabecera["horario"]?></td>
				</tr>	
				<tr class="tr_busqueda">
					<td class="celda0">Observaciones:</td >
					<td class="celda1" colspan='3'><?php echo ucwords(strtolower($arrCabecera["observacion"]))?></td>
				</tr>					
			</table>
		</div>

		<br/>
		<div id="div_bus" class="divBusqueda" style="width: 750px" >
			<table class="tablaBusqueda" style='width: 90%'>
				<thead>
					<th colspan='7'>Movimientos de gestion Criticos</th>
				</thead>
				<tr class="tr_busqueda">
					<td class="celda0" style='text-align:center'>Fecha Movimiento</td >
					<td class="celda0" style='text-align:center'>Usuario</td >
					<td class="celda0" style='text-align:center'>Estado</td >
					<td class="celda0" style='text-align:center'>Observaciones</td >
					<td class="celda0" style='text-align:center'>Motivo</td >
					<td class="celda0" style='text-align:center'>Submotivo</td >	
					<td class="celda0" style='text-align:center'>Fecha Agenda / Turno</td >
					
				</tr>
				
				
				<?php
				foreach($arrMovimientos as $filaMov) {
					?>
					<tr>
					<td class="celda2"><?php echo $filaMov["fecha_mov2"]?></td>
					<td class="celda2"><?php echo $filaMov["usuario"]?></td>
					<td class="celda2"><?php echo $filaMov["estado"]?></td>
					<td class="celda2"><?php echo ucwords(strtolower($filaMov["observacion"]))?></td>
					<td class="celda2"><?php echo $filaMov["motivo"]?></td>
					<td class="celda2"><?php echo $filaMov["submotivo"]?></td>
					<td class="celda2"><?php echo $filaMov["fecha_agenda2"]." / T.".$filaMov["horario"]?></td>
					</tr>
					<?php
				}
				
				?>
				
				
			</table>
		</div>	
		
<?php
	}
	else {
		echo "No se encontraron registros sobre la busqueda.";
	}
    
}



?>
