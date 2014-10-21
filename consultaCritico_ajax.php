<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include ("../../clases/class.Conexion.php");

 $db = new Conexion();
 $cnx = $db->conectarPDO();


if ($_POST['action']=='buscar')
{
	$id=$_POST["id"];

		$cnx->exec("set names utf8");
		$cad2 = "SELECT a.*, e.estado, h.horario, av.averia, av.fecha_registro 
				 FROM webpsi_criticos.`gestion_criticos` a
				 INNER JOIN webpsi_criticos.gestion_averia av ON av.id_gestion=a.id
				 INNER JOIN webpsi_criticos.estados e ON e.id=a.id_estado
				 INNER JOIN webpsi_criticos.horarios h ON h.id=a.id_horario
				 WHERE a.id=$id 
				 UNION ALL
				 SELECT a.*, e.estado, h.horario, av.averia, av.fecha_registro 
				 FROM webpsi_criticos.`gestion_criticos` a
				 INNER JOIN webpsi_criticos.gestion_rutina_manual av ON av.id_gestion=a.id
				 INNER JOIN webpsi_criticos.estados e ON e.id=a.id_estado
				 INNER JOIN webpsi_criticos.horarios h ON h.id=a.id_horario
				 WHERE a.id=$id 
				 UNION ALL
				 SELECT a.*, e.estado, h.horario, av.codigo_req averia, av.fecha_Reg fecha_registro 
				 FROM webpsi_criticos.`gestion_criticos` a
				 INNER JOIN webpsi_criticos.gestion_provision av ON av.id_gestion=a.id
				 INNER JOIN webpsi_criticos.estados e ON e.id=a.id_estado
				 INNER JOIN webpsi_criticos.horarios h ON h.id=a.id_horario
				 WHERE a.id=$id
				 UNION ALL
				 SELECT a.*, e.estado, h.horario, av.codigo_req averia, av.fecha_Reg fecha_registro 
				 FROM webpsi_criticos.`gestion_criticos` a
				 INNER JOIN webpsi_criticos.gestion_rutina_manual_provision av ON av.id_gestion=a.id
				 INNER JOIN webpsi_criticos.estados e ON e.id=a.id_estado
				 INNER JOIN webpsi_criticos.horarios h ON h.id=a.id_horario
				 WHERE a.id=$id";
		$res = $cnx->query($cad2);
        $arrCabecera=$res->fetch(PDO::FETCH_ASSOC);
          
		
		$cad3 = "SELECT a.*, e.estado, h.horario, m.motivo, m2.submotivo, u.usuario,
				 date_format(a.fecha_movimiento, '%d-%m-%Y %H:%i:%s') as fecha_mov2,
				 date_format(a.fecha_agenda, '%d-%m-%Y') as fecha_agenda2
				 FROM webpsi_criticos.`gestion_movimientos` a
				 INNER JOIN webpsi_criticos.estados e ON e.id=a.id_estado 
				 INNER JOIN webpsi_criticos.horarios h ON h.id=a.id_horario 
				 INNER JOIN webpsi.tb_usuario u ON u.id=a.id_usuario 
				 INNER JOIN webpsi_criticos.motivos m ON m.id=a.id_motivo 
				 INNER JOIN webpsi_criticos.submotivos m2 ON m2.id=a.id_submotivo
				 WHERE id_gestion=$id  				 
				 ORDER BY fecha_movimiento DESC ";
		$res = $cnx->query($cad3);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $arrMovimientos[] = $row;
        }
        $cnx = NULL;
        $db = NULL;
		
		//var_dump($arrCabecera);
		//var_dump($arrMovimientos);
		
		//echo "1";
	if(count(arrMovimientos)>0){
		?>
		

		<div id="div_bus" class="divBusqueda" style="width: 750px" >

			<table class="tablaBusqueda" style='width: 90%'>
				<thead>
					<th colspan='4'>Datos del cliente</th>
				</thead>	
			
				<tr class="tr_busqueda">
					<td class="celda0">ATC:</td ><td class="celda1"><?php echo $arrCabecera["id_atc"]?></td>
					<td class="celda0">Averia:</td ><td class="celda1"><?php echo $arrCabecera["averia"]?></td>
				</tr>
				<tr>
					<td class="celda0">Nombre del cliente:</td >
					<td class="celda1" colspan='3'><?php echo ucwords(strtolower($arrCabecera["nombre_cliente_critico"]))?></td>
				</tr>
				<tr class="tr_busqueda">
					<td class="celda0">Fecha registro averia:</td ><td class="celda1"><?php echo $arrCabecera["fecha_registro"]?></td>
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
