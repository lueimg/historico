<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$id = $_POST["id"];
$paso = $_POST["paso"];

//echo "ID = ".$id;
//echo "PASO = ".$paso;

require_once("../../clases/class.OfficeTrack.php");
$objOT = new OfficeTrack();

if ($paso=="0001-Inicio") {
	$arrPaso1 = $objOT->getPaso1($id);	
	foreach ($arrPaso1 as $rowPaso1) 
		$arrPaso2_2 = $rowPaso1;
}
else if ($paso=="0002-Supervision") {
	$arrPaso2 = $objOT->getPaso2($id);	
	foreach ($arrPaso2 as $rowPaso2) 
		$arrPaso2_2 = $rowPaso2;
}
else if ($paso=="0003-Cierre") {
	$arrPaso3 = $objOT->getPaso3($id);	
	foreach ($arrPaso3 as $rowPaso3) 
		$arrPaso2_2 = $rowPaso3;
}


//print_r($arrPaso2_2);

?>

<head>
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
<script type="text/javascript">
$(document).ready(function() {
	$(".fancybox-button").fancybox({
		prevEffect		: 'none',
		nextEffect		: 'none',
		closeBtn		: false,
		helpers		: {
			title	: { type : 'inside' },
			buttons	: {}
		}
	});
});
</script>

</head>
<body>		


<?php
if ($paso=="0001-Inicio")    // PASO 1
{  
?>

<table class="tablaBusqueda" style='width: 100%'>
	<thead>
		<th colspan='4'>Paso 1 <?php echo $id?></th>
	</thead>	

	<tr class="tr_busqueda">
		<td class="celda0" style="width: 20%">X/Y:</td >
		<td class="celda1" colspan='1'><?php echo $arrPaso2_2["x"]." / ".$arrPaso2_2["y"];?></td>
	</tr>
	<tr>
		<td class="celda0">CASA Imagen:</td >
		<td class="celda1" colspan='3'>
		<?php 
			//$tapImg1 = $arrPaso2_2["tap_img1"]; 
			$casaImg1 = ($arrPaso2_2["casa_img1"]==null) ? " ":$arrPaso2_2["casa_img1"] ; 
			//$tapImg2 = $arrPaso2_2["tap_img2"]; 
			$casaImg2 = ($arrPaso2_2["casa_img2"]==null) ? " ":$arrPaso2_2["casa_img2"] ; 
			$casaImg3 = $arrPaso2_2["casa_img3"]; 
			if(trim($casaImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$casaImg1.'" title="Img CASA 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$casaImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$casaImg1' width='30%'  />";
			}

			if(trim($casaImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$casaImg2.'" title="Img CASA 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$casaImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$casaImg2' width='30%'  />";
			}

			if(trim($casaImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$casaImg3.'" title="Img CASA 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$casaImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$casaImg3' width='30%'  />";
			}
		?>
		</td>
	</tr>
	
<?php
}
else if ($paso=="0002-Supervision")    // PASO 2
{  
?>

<table class="tablaBusqueda" style='width: 100%'>
	<thead>
		<th colspan='4'>Paso 2 <?php echo $id?></th>
	</thead>	

	<tr class="tr_busqueda">
		<td class="celda0" style="width: 20%">Motivo:</td >
		<td class="celda1" colspan='1'><?php echo $arrPaso2_2["motivo"]?></td>
	</tr>
	<tr class="tr_busqueda">
		<td class="celda0">Observaciones:</td >
		<td class="celda1" colspan='1'><?php echo $arrPaso2_2["observaciones"]?></td>
	</tr>
	<tr>
		<td class="celda0">Imagen TAP:</td >
		<td class="celda1" colspan='3'>
		<?php 
			//$tapImg1 = $arrPaso2_2["tap_img1"]; 
			$tapImg1 = ($arrPaso2_2["tap_img1"]==null) ? " ":$arrPaso2_2["tap_img1"] ; 
			//$tapImg2 = $arrPaso2_2["tap_img2"]; 
			$tapImg2 = ($arrPaso2_2["tap_img2"]==null) ? " ":$arrPaso2_2["tap_img2"] ; 
			$tapImg3 = $arrPaso2_2["tap_img3"]; 
			if(trim($tapImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tapImg1.'" title="Img TAP 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tapImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tapImg1' width='30%'  />";
			}

			if(trim($tapImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tapImg2.'" title="Img TAP 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tapImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tapImg2' width='30%'  />";
			}

			if(trim($tapImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tapImg3.'" title="Img TAP 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tapImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tapImg3' width='30%'  />";
			}
						
		?>
		</td>
	</tr>
	<tr>
		<td class="celda0">Imagen MODEM:</td >
		<td class="celda1" colspan='3'>
		<?php 
			$modemImg1 = ($arrPaso2_2["modem_img1"]==null) ? " ":$arrPaso2_2["modem_img1"] ; 
			$modemImg2 = $arrPaso2_2["modem_img2"]; 
			$modemImg3 = $arrPaso2_2["modem_img3"]; 
			if(trim($modemImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$modemImg1.'" title="Img MODEM 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$modemImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$modemImg1' width='30%'  />";
			}

			if(trim($modemImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$modemImg2.'" title="Img MODEM 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$modemImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$modemImg2' width='30%'  />";
			}

			if(trim($modemImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$modemImg3.'" title="Img MODEM 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$modemImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$modemImg3' width='30%'  />";
			}
					
		?>		
		
		</td>
	</tr>
	<tr>
		<td class="celda0">Imagen TV:</td >
		<td class="celda1" colspan='3'>
		<?php 
			$tvImg1 = $arrPaso2_2["tv_img1"]; 
			$tvImg2 = $arrPaso2_2["tv_img2"]; 
			$tvImg3 = $arrPaso2_2["tv_img3"]; 
			if(trim($tvImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tvImg1.'" title="Img TV 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tvImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tvImg1' width='30%'  />";
			}

			if(trim($tvImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tvImg2.'" title="Img TV 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tvImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tvImg2' width='30%'  />";
			}

			if(trim($tvImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$tvImg3.'" title="Img TV 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$tvImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$tvImg3' width='30%'  />";
			}
			
		?>			
		</td>
	</tr>
	
	<tr>
		<td class="celda0">Imagen PROBLEMA:</td >
		<td class="celda1" colspan='3'>
		<?php 
			$problemaImg1 = $arrPaso2_2["problema_img1"]; 
			$problemaImg2 = $arrPaso2_2["problema_img2"]; 
			$problemaImg3 = $arrPaso2_2["problema_img3"]; 
			if(trim($problemaImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$problemaImg1.'" title="Img PROBLEMA 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$problemaImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$problemaImg1' width='30%'  />";
			}

			if(trim($problemaImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$problemaImg2.'" title="Img PROBLEMA 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$problemaImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$problemaImg2' width='30%'  />";
			}

			if(trim($problemaImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$problemaImg3.'" title="Img PROBLEMA 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$problemaImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$problemaImg3' width='30%'  />";
			}

		?>			
		</td>
	</tr>		
</table>
<?php
}
else if ($paso=="0003-Cierre")    // PASO 2
{  
?>

<table class="tablaBusqueda" style='width: 100%'>
	<thead>
		<th colspan='4'>Paso 3 <?php echo $id?></th>
	</thead>	

	<tr class="tr_busqueda">
		<td class="celda0" style="width: 20%">Estado:</td >
		<td class="celda1" colspan='1'><?php echo $arrPaso2_2["motivo"]?></td>
	</tr>
	<tr class="tr_busqueda">
		<td class="celda0">Observaciones:</td >
		<td class="celda1" colspan='1'><?php echo $arrPaso2_2["observaciones"]?></td>
	</tr>
	<tr>
		<td class="celda0">Imagen Final:</td >
		<td class="celda1" colspan='3'>
		<?php 
			$finalImg1 = ($arrPaso2_2["final_img1"]==null) ? " ":$arrPaso2_2["final_img1"] ; 
			$finalImg2 = ($arrPaso2_2["final_img2"]==null) ? " ":$arrPaso2_2["final_img2"] ; 
			$finalImg3 = $arrPaso2_2["final_img3"]; 
			if(trim($finalImg1)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$finalImg1.'" title="Img Final 1 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$finalImg1' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$finalImg1' width='30%'  />";
			}

			if(trim($finalImg2)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$finalImg2.'" title="Img Final 2 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$finalImg2' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$finalImg2' width='30%'  />";
			}

			if(trim($finalImg3)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$finalImg3.'" title="Img Final 3 - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$finalImg3' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$finalImg3' width='30%'  />";
			}

		?>
		</td>
	</tr>
	<tr>
		<td class="celda0">Firma:</td >
		<td class="celda1" colspan='3'>
		<?php 
			$firmaImg = ($arrPaso2_2["firma_img"]==null) ? " ":$arrPaso2_2["firma_img"] ; 
			if(trim($firmaImg)!=""){
				echo '<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'.$firmaImg.'" title="Img Firma - OBS:'.$arrPaso2_2["observaciones"].'">';
					echo "<img src='data:image/jpg;base64,$firmaImg' width='30%'  />";
				echo '</a>';
			}
			else{
				echo "<img src='data:image/jpg;base64,$firmaImg' width='30%'  />";
			}

		?>		
		
		</td>
	</tr>
	
</table>
<?php
}

		


