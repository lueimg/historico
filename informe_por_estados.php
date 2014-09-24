<?php
error_reporting(1);
ini_set('display_errors',1);


require_once("../../cabecera.php");
require_once("../../clases/class.Conexion.php");


$objCnx = new Conexion();
$cnx = $objCnx->conectarBD();

$filtroM1 = " ";

//echo $filtroM1;

/*
$cad = "SELECT * FROM webpsi_criticos.`viewInformePorEstados` WHERE eecc_zona!='' ";

$res = mysql_query($cad, $cnx) or die(mysql_error());
while ($row = mysql_fetch_array($res, MYSQL_ASSOC))
{
    $arr[] = $row;
}

//print_r($arr);
*/

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

/*$cadTmp0 = "DROP TABLE IF EXISTS `webpsi_criticos`.".$tablaTmp." ";
$resTmp0 = mysql_query($cadTmp0) or die(mysql_error()) ;
*/



$cadLejano = "SELECT lejano FROM `webpsi_criticos`.`viewInformePorEstados` WHERE eecc_zona!='' AND lejano IS NOT NULL
		GROUP BY 1 ORDER BY 1 ASC";
$resLejano = mysql_query($cadLejano);
while ($rowLejano = mysql_fetch_array($resLejano, MYSQL_ASSOC))
{
    $arrLejano[] = $rowLejano;
}

$cadQuiebre = "SELECT quiebre FROM `webpsi_criticos`.`viewInformePorEstados` WHERE eecc_zona!='' AND quiebre IS NOT NULL
		GROUP BY 1 ORDER BY 1 ASC";
$resQuiebre = mysql_query($cadQuiebre);
while ($rowQuiebre = mysql_fetch_array($resQuiebre, MYSQL_ASSOC))
{
    $arrQuiebre[] = $rowQuiebre;
}

$cadArea2 = "SELECT area2 FROM `webpsi_criticos`.`viewInformePorEstados` WHERE (area2 IS NOT NULL AND area2!='')
		GROUP BY 1 ORDER BY 1 ASC";
$resArea2 = mysql_query($cadArea2);
while ($rowArea2 = mysql_fetch_array($resArea2, MYSQL_ASSOC))
{
    $arrArea2[] = $rowArea2;
}


$cadTmp = "CREATE TEMPORARY TABLE IF NOT EXISTS `webpsi_criticos`.".$tablaTmp."  
( INDEX(id_atc), INDEX(id_estado), INDEX(eecc_zona), INDEX(quiebre_grupo), INDEX(averia_m1)) 
ENGINE=MyISAM 
AS (
  SELECT * FROM `webpsi_criticos`.`viewInformePorEstados` WHERE 1=1 $filtroM1
)";
$resTmp = mysql_query($cadTmp) or die(mysql_error()) ;

//die("CHAU");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
            <meta name="author" content="Sergio MC" />
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

                <?php include ("../../includes.php") ?>                

<style>
tr {
	/*border: 1px solid blue;*/
	border: 2px solid red;
}

label {
	padding: 1px;
	font-size: 10px;
}

</style>

<!--
<link rel="stylesheet" type="text/css" href="../../css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="../../css/assets/style.css" />
<link rel="stylesheet" type="text/css" href="../../css/assets/prettify.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../css/assets/prettify.js"></script>
<script type="text/javascript" src="../../css/assets/jquery.multiselect.js"></script>
-->

<link type="text/css" href='css/demo_page.css' rel="Stylesheet" />
<link type="text/css" href='css/demo_table.css' rel="Stylesheet" />
<link type="text/css" href='css/informes.css' rel="Stylesheet" />

<script type="text/javascript">
$(function(){
	$("#comboLejano").multiselect();
	$("#comboQuiebre").multiselect();
	$("#comboArea2").multiselect();

	$("#cmbMovistarUno").multiselect({
	   multiple: false,
	   header: "Select an option",
	   noneSelectedText: "Select an Option",
	   selectedList: 1
	});	
});
</script>

<script >

	function generarExcel() {
		window.open("informe_por_estados_excel.php");

	}

	function cambiarTipoFecha() {

		var envio="idComboTipoFecha="+$("#cmbtipofecha").val()+"&idMovistarUno="+$("#cmbMovistarUno").val();		
		$.ajax({
		    type: "POST",
		    url: "informe_por_estados_ajax.php",
		    data: envio,
		    success: function(html) {
				var resp = jQuery.trim(html);
				$("#divCantidadEstados").html(resp);
				//window.location.reload(); 
		 	}
		});

	}

	function cambiarMovistarUno() {

		window.location = "informe_por_estados.php?idMovistarUno="+$("#cmbMovistarUno").val();
	}	

	function buscarFiltrado() {
		var comboLejano = $("#comboLejano").val();
		var comboQuiebre = $("#comboQuiebre").val();
		var comboArea2 = $("#comboArea2").val();
		var comboMovistarUno = $("#cmbMovistarUno").val();
		
		//window.location = "informe_por_estados.php?idMovistarUno="+$("#cmbMovistarUno").val();
		var envio="idMovistarUno="+comboMovistarUno+"&lejano="+comboLejano+"&comboQuiebre="+comboQuiebre+"&comboArea2="+comboArea2;	
		$.ajax({
		    type: "POST",
		    url: "informe_por_estados_res1.php",
		    data: envio,
		    success: function(html) {
				var resp = jQuery.trim(html);
				$("#divRes1").html(resp);
				//window.location.reload(); 
		 	}
		});
		

	}

</script>

<link rel="stylesheet" type="text/css" href="../../../css/estiloAdmin.css">

</head>

<body>

<p style="padding: 5px;"><a href="#" onclick="generarExcel()">
	<img src="img/excel2007.png" alt="excel" title="Generar Excel" style="vertical-align:middle;"
		width="3%" >[ Generar Excel ]</img></a>
</p>

<br/>
<label>Filtro M1:</label>
<select name='cmbMovistarUno' id='cmbMovistarUno' onchange="cambiarMovistarUno()">
	<option value='1'>Todos</option>
	<option value='2'>Solo Movistar Uno</option>
	<option value='3'>Sin Movistar Uno</option>
</select>
<br/>
<label>Lejano:</label>
<select title="Basic example" multiple="multiple" name="comboLejano" id="comboLejano"  size="5">
<?php

	foreach ($arrLejano as $filaLejano) {
		?>
			<option value="<?php echo $filaLejano['lejano']?>"><?php echo $filaLejano['lejano']?></option>
	<?php
	}
	?>
	
</select>
<label>Quiebre:</label>
<select title="Basic example" multiple="multiple" name="comboQuiebre" id="comboQuiebre"  size="5">
<?php
	foreach ($arrQuiebre as $filaQuiebre) {
		?>
			<option value="<?php echo $filaQuiebre['quiebre']?>"><?php echo $filaQuiebre['quiebre']?></option>
	<?php
	}
	?>
</select>
<label>Area2:</label>
<select title="Basic example" multiple="multiple" name="comboArea2" id="comboArea2"  size="5">
<?php
	foreach ($arrArea2 as $filaArea2) {
		?>
			<option value="<?php echo $filaArea2['area2']?>"><?php echo $filaArea2['area2']?></option>
	<?php
	}
	?>>
</select>
<input type="button" id="botonFiltros" name="botonFiltros" value="Buscar Filtrado" onclick="buscarFiltrado();" />

<div id="divRes1" name="divRes1">
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
			<td colspan='1'><label><?php echo utf8_decode($filaEstados["estado"])?></label></td>
			<?php 
			$totEstadoCritico = 0;
			$i=0;
			foreach ($arrEmpresas as $filaEmpresas2 ) {
			?>
				<td colspan='1' style="text-align: center; ">
				<?php 
					$cad3 = "SELECT COUNT(*) FROM webpsi_criticos.".$tablaTmp."
							WHERE quiebre_grupo='CRITICOS'  $filtroM1
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
							WHERE quiebre_grupo='R9-REIT-CATV' $filtroM1
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
</div>		

	</div>
	<br/>
	<p>

	<select name='cmbtipofecha' id='cmbtipofecha' onchange="cambiarTipoFecha()">
	<option value='0'>-- Seleccione --</option>
	<option value='1'>Por fecha de registro</option>
	<option value='2'>Por fecha de Ult Mov</option>
	<option value='3'>Por fecha de subida</option>
	</select>
	</p>
	<br/>
	<div id="divCantidadEstados" style="display: table; border: 0px solid green; width: 45%;">
	</div>


</body>
</html>
