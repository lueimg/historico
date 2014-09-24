<?php

require_once("../../clases/class.Conexion.php");


$objCnx = new Conexion();
$cnx = $objCnx->conectarBD();


if (isset($_POST["idComboTipoFecha"])) {
	$idComboTipoFecha = $_POST["idComboTipoFecha"];
	
	if ($_POST["idMovistarUno"]=="1")
		$filtroM1 = " "; 
	else if ($_POST["idMovistarUno"]=="2")
		$filtroM1 = " AND averia_m1='MOVISTAR UNO' ";
	else if ($_POST["idMovistarUno"]=="3")
		$filtroM1 = " AND averia_m1!='MOVISTAR UNO' ";


	switch ($idComboTipoFecha) {
		case '1':
			$campoFecha = 'fecha_registro';
			$campoFecha2 = "Fecha de Registro";
			break;
		case '2':
			$campoFecha = 'fecha_ult_mov';
			$campoFecha2 = "Fecha Ult Movimiento";
			break;
		case '3':
			$campoFecha = 'fecha_subida';
			$campoFecha2 = "Fecha de Subida";
			break;

	}

	$cad2 = "SELECT estado, horas2, COUNT(*) as cantidad
	FROM (SELECT estado, horas, IF(horas<24, '00-24', IF(horas>24 AND horas<48, '24-48', 
	IF(horas>48, '>48', ''))) AS horas2
	FROM 
	(SELECT averia,estado, $campoFecha, (TIME_TO_SEC(TIMEDIFF(NOW(), $campoFecha )) / 3600) AS horas
	FROM `webpsi_criticos`.`viewInformePorEstados`  WHERE  quiebre_grupo='CRITICOS' $filtroM1  ) a)  b 
	GROUP BY 1,2 ORDER BY estado DESC, horas2 ASC;";

	$res2 = mysql_query($cad2) ;
	while ($row2 = mysql_fetch_array($res2, MYSQL_ASSOC))
	{
	    $arrEstados2[] = $row2;
	}


	$cad2 = "SELECT estado, horas2, COUNT(*) as cantidad
	FROM (SELECT estado, horas, IF(horas<24, '00-24', IF(horas>24 AND horas<48, '24-48', 
	IF(horas>48, '>48', ''))) AS horas2
	FROM 
	(SELECT averia,estado, $campoFecha, (TIME_TO_SEC(TIMEDIFF(NOW(), $campoFecha )) / 3600) AS horas
	FROM `webpsi_criticos`.`viewInformePorEstados`  WHERE  quiebre_grupo!='CRITICOS' $filtroM1 ) a)  b 
	GROUP BY 1,2 ORDER BY estado DESC, horas2 ASC;";

	$res2 = mysql_query($cad2) ;
	while ($row2 = mysql_fetch_array($res2, MYSQL_ASSOC))
	{
	    $arrEstados3[] = $row2;
	}

	?>
			<div id="div_Clonar" class="divClonar" style="display: table; border: 0px solid green; width: 45%; float: left">
			<table style="border: 1px solid;"  >
			<thead>
				<th colspan="3"><label>CRITICOS</label></th>
			</thead>
			<tr >
				<th class="td" rowspan="1"><label>Estado WebPSI</label></th>
				<th class="celda_titulo" colspan="1" style="text-align: center; font-weight: bold;"><label>Rango Horas<br/><?php echo $campoFecha2 ?></label></th>
				<th class="celda_titulo" colspan="1" style="text-align: center; font-weight: bold;"><label>Cantidad</label></th>
			</tr>

			<?php
			//	print_r($arrEstados2);
			$x = 0;
			$j = 0;
			$d = 0;
			
			//foreach ($arrEstados2 as $filaEstados2 ) {	
			for($j=0; $j<count($arrEstados2); $j++) {
				
				$c = count($arrEstados2[$j]);
				//echo "<br/>C=$c J=$j D=$d ".$arrEstados2[$j]["estado"];
				?>
				<tr>
					<td class="td" rowspan="1">
						<label><?php echo utf8_decode($arrEstados2[$j]["estado"])?></label>
					</td>
					<td class="td"><label><?php echo $arrEstados2[$j]["horas2"]?></label></td>
					<td class="td"><label><?php echo $arrEstados2[$j]["cantidad"]?></label></td>
				</tr>
			<?php
			}
			?>
			</table>
			</div>
			<div id="div_Clonar" class="divClonar">
			<table style="border: 1px solid;"  >
			<thead>
				<th colspan="3"><label>R9 - REITERADAS</label></th>
			</thead>
			<tr >
				<th class="td" rowspan="1"><label>Estado WebPSI</label></th>
				<th class="celda_titulo" colspan="1" style="text-align: center; font-weight: bold;"><label>Rango Horas<br/><?php echo $campoFecha2 ?></label></th>
				<th class="celda_titulo" colspan="1" style="text-align: center; font-weight: bold;"><label>Cantidad</label></th>
			</tr>

			<?php
			//	print_r($arrEstados2);
			foreach ($arrEstados3 as $filaEstados3 ) {
				?>
				<tr>
				<td class="td"><label><?php echo utf8_decode($filaEstados3["estado"])?></label></td>
				<td class="td"><label><?php echo $filaEstados3["horas2"]?></label></td>
				<td class="td"><label><?php echo $filaEstados3["cantidad"]?></label></td>
				</tr>
			<?php
			}

			?>
			</table>
			</div>

<?php

}


