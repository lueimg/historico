<?php 
//header('Content-typ: content="text/html; charset=UTF-8');
require_once("../../clases/class.Conexion.php");


/* Recoger variables */

if (isset($_POST["idMovistarUno"])) {
	if ($_POST["idMovistarUno"]=="1") {
		$filtroM1 = " "; 
		$textoFiltroM1 = " ";
	}
	else if ($_POST["idMovistarUno"]=="2") {
		$filtroM1 = " AND averia_m1='MOVISTAR UNO' ";
		$textoFiltroM1 = " SOLO MOVISTAR UNO ";
	}
	else if ($_POST["idMovistarUno"]=="3") {
		$filtroM1 = " AND (averia_m1!='MOVISTAR UNO' OR averia_m1 IS NULL  )";
		$textoFiltroM1 = " EXCLUYE MOVISTAR UNO ";
	}
}
else
	$filtroM1 = "";

if (isset($_POST["lejano"])) {
	$arrFiltroLejano = $_POST["lejano"];
	if ($arrFiltroLejano!=='null')  {
		$arrFiltroLejano2 = explode(",", $arrFiltroLejano);
		$filtroLejano = "";
		foreach ($arrFiltroLejano2 as $row) {
			$filtroLejano .= "'".$row."',";
		}
		$filtroLejano = substr($filtroLejano,0,strlen($filtroLejano)-1);
		$filtroLejano2 = " AND lejano IN ( ".$filtroLejano." )";
		//echo $filtroLejano;
	}
	else $filtroLejano2 = "";
}
else
	$filtroLejano2 =  " ";

if (isset($_POST["comboQuiebre"])) {
	$arrFiltroQuiebre = $_POST["comboQuiebre"];
	if ($arrFiltroQuiebre!=='null')  {
		$arrFiltroQuiebre2 = explode(",", $arrFiltroQuiebre);
		$filtroQuiebre = "";
		foreach ($arrFiltroQuiebre2 as $row) {
			$filtroQuiebre .= "'".$row."',";
		}
		$filtroQuiebre = substr($filtroQuiebre,0,strlen($filtroQuiebre)-1);
		$filtroQuiebre2 = " AND quiebre IN ( ".$filtroQuiebre." )";
		//echo $filtroQuiebre;
	} 
	else $filtroQuiebre2 = "";
}
else
	$filtroQuiebre2 = " ";

if (isset($_POST["comboArea2"])) {
	$arrFiltroArea2 = $_POST["comboArea2"];
	if ($arrFiltroArea2!=='null')  {
		$arrFiltroArea22 = explode(",", $arrFiltroArea2);
		$filtroArea2 = "";
		foreach ($arrFiltroArea22 as $row) {
			$filtroArea2 .= "'".$row."',";
		}
		$filtroArea2 = substr($filtroArea2,0,strlen($filtroArea2)-1);
		$filtroArea22 = " AND area2 IN ( ".$filtroArea2." )";
		//echo $filtroArea2;	
	}
	else $filtroArea22 = "";
}
else
	$filtroArea22 = " ";

//echo $filtroArea2;
/*
echo "<br/>".$filtroLejano2;
echo "<br/>".$filtroQuiebre2;
echo "<br/>".$filtroArea22;
*/

/* Fin de recoger variables */


$objCnx = new Conexion();
$cnx = $objCnx->conectarBD();

//mysql_query("SET NAMES 'utf8'");


$cad2 = "SELECT id_estado,estado FROM `webpsi_criticos`.`viewInformePorEstados` WHERE 1=1 $filtroM1 
		GROUP BY 1,2 ORDER BY 2 ASC;";
$res2 = mysql_query($cad2) ;
while ($row2 = mysql_fetch_array($res2, MYSQL_ASSOC))
{
    $arrEstados[] = $row2;
}

$cad22 = "SELECT eecc_zona FROM `webpsi_criticos`.`viewInformePorEstados` WHERE eecc_zona!='' $filtroM1 
		GROUP BY 1 ORDER BY 1 ASC;";
$res22 = mysql_query($cad22) ;
while ($row22 = mysql_fetch_array($res22, MYSQL_ASSOC))
{
    $arrEmpresas[] = $row22;
}

//print_r($arrEmpresas);
$totalEmpresas = count($arrEmpresas);
//echo $totalEmpresas;

//$tablaTmp = "viewInformePorEstados";
$tablaTmp = "tmp_viewInformePorEstados_".date("his");


$cadTmp = "CREATE TEMPORARY TABLE IF NOT EXISTS `webpsi_criticos`.".$tablaTmp."  
( INDEX(id_atc), INDEX(id_estado), INDEX(eecc_zona), INDEX(quiebre_grupo), INDEX(averia_m1)) 
ENGINE=MyISAM 
AS (
  SELECT * FROM `webpsi_criticos`.`viewInformePorEstados` WHERE 1=1 $filtroM1 $filtroLejano2 $filtroQuiebre2 $filtroArea22
)";
$resTmp = mysql_query($cadTmp) or die(mysql_error()) ;



?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<!-- Inicio -->

	<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
	
	<div id="div_Clonar" class="divClonar">
	<table style="border: 1px solid;"  >
	<tr >
		<th class="td" rowspan="1"><label>Estado WebPSI</label></th>
		<th class="celda_titulo" colspan="<?php echo $totalEmpresas+8?>" style="text-align: center; font-weight: bold;">
			<label>Quiebre/Tipo -- <?php echo $textoFiltroM1?></label></th>
	</tr>
	<tr>
		<th >&nbsp;</td>
		<th class="input-xlarge" colspan="<?php echo $totalEmpresas?>" style="text-align: center; font-weight: bold;"><label>CRITICOS</label></th>
		<th class="celda_titulo" rowspan="2"><label>Total</label></th>
		<th class="celda_titulo" colspan="<?php echo $totalEmpresas?>" style="text-align: center; font-weight: bold;"><label>R9-REIT</label></th>
		<th class="celda_titulo" rowspan="2"><label>Total</label></th>

	</tr>
	<tr>
		<th class="celda_titulo">&nbsp;</th>
		<?php
			foreach ($arrEmpresas as $filaEmpresas) {
				?>
				<th class="celda"><label><?php echo $filaEmpresas["eecc_zona"]?></label></th>

				<?php
			}
		?>
		<?php
			foreach ($arrEmpresas as $filaEmpresas) {
				?>
				<th class="celda"><label><?php echo $filaEmpresas["eecc_zona"]?></label></th>

				<?php
			}
		?>


	</tr>	
	<?php

	$totFilaTotalR9 = 0;
	foreach ($arrEstados as $filaEstados) {
		?>
		<tr>
			<td colspan='1'><label><?php echo $filaEstados["estado"]?></label></td>
			<?php 
			$totEstadoCritico = 0;
			$i=0;
			foreach ($arrEmpresas as $filaEmpresas2 ) {
			?>
				<td colspan='1' style="text-align: center; ">
				<?php 
					$cad3 = "SELECT COUNT(*) FROM webpsi_criticos.".$tablaTmp."
							WHERE quiebre_grupo='CRITICOS'  $filtroM1 $filtroLejano2 $filtroQuiebre2 $filtroArea22
							AND eecc_zona='".$filaEmpresas2["eecc_zona"]."' AND id_estado=".$filaEstados["id_estado"]; 
					//echo $cad3;
					$res3 = mysql_query($cad3) or die(mysql_error());
					$row3 = mysql_fetch_row($res3);
					$totEstadoCritico = $row3[0]+$totEstadoCritico;
					//$arrTotalEmpresasCriticos[0][$filaEmpresas2["eecc_zona"]] += $row3[0];
					$arrTotalEmpresasCriticos[$i] += $row3[0];

					echo "<label>".$row3[0]."</label>";
				?>
			</td>
			<?php
				$i++;
			}

			?>	

			<td colspan='1' style="text-align: center; font-weight: bold;">
			<?php
				echo "<label style='font-size: 11px;'>".$totEstadoCritico."</label>";
			?>
			</td>
			<?php 
			$totEstadoR9 = 0;
			$j=0;
			foreach ($arrEmpresas as $filaEmpresas2 ) {
			?>
				<td colspan='1' style="text-align: center; ">
				<?php 
					$cad3 = "SeLEct COUNT(*) FROM webpsi_criticos.".$tablaTmp."
							WHERE quiebre_grupo='R9-REIT-CATV' $filtroM1 $filtroLejano2 $filtroQuiebre2 $filtroArea22
							AND eecc_zona='".$filaEmpresas2["eecc_zona"]."' AND id_estado=".$filaEstados["id_estado"]; 
					//echo $cad3;
					$res3 = mysql_query($cad3) or die(mysql_error());
					$row3 = mysql_fetch_row($res3);
					$totEstadoR9 = $row3[0]+$totEstadoR9;
					$arrTotalEmpresasR9[$j] += $row3[0];
					echo "<label>".$row3[0]."</label>";
				?>
			</td>
			<?php
				$j++;
			}

			?>
			<td colspan='1' style="text-align: center; font-weight: bold;">
			<?php
				echo "<label style='font-size: 11px;'>".$totEstadoR9."</label>";
			?>
			</td>			

		</tr>	
		<?php
		//$totFilaTotalCritico += $totEstadoCritico;
		//$totFilaTotalR9 += $totEstadoR9;

	}


	foreach ($arrTotalEmpresasCriticos as $filaEmpresaCriticos) {
		//echo $filaEmpresaCriticos[1];
	}
	//print_r($arrTotalEmpresasCriticos);
	?>
	<tr>
		<th class="celda_titulo">TOTALES:</th>
		<?php
			foreach ($arrTotalEmpresasCriticos as $filaTotalEmpresasCriticos ) {
				//echo "<br>";
				//print_r($filaTotalEmpresasCriticos);
			?>
				<td class="celda_titulo" style="text-align: center; font-weight: bold; color: blue;">
					<label>
						<?php echo $filaTotalEmpresasCriticos?>
					</label>
				</td>
			<?php
				$totFilaTotalCritico += $filaTotalEmpresasCriticos;

			}
		?>
		<td class="celda_titulo" rowspan="1" style="text-align: center; font-weight: bold; color: red;" >
			<label style='font-size: 11px;'>
				<?php echo $totFilaTotalCritico?>
			</label>
		</td>
		<?php
			foreach ($arrTotalEmpresasR9 as $filaTotalEmpresasR9 ) {
				//echo "<br>";
				//print_r($filaTotalEmpresasCriticos);
			?>
				<td class="celda_titulo" style="text-align: center; font-weight: bold;color: blue;">
				<label style='font-size: 11px;'>
					<?php echo $filaTotalEmpresasR9?>
				</label>
				</td>
			<?php
				$totFilaTotalR9 += $filaTotalEmpresasR9;
			}
		?>
		<td class="celda_titulo" rowspan="1" style="text-align: center; font-weight: bold; color: red;">
			<label style='font-size: 11px;'><?php echo $totFilaTotalR9?></label>
		</td>		
	</tr>

	</table>
