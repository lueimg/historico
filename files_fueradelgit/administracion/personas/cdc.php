<?php
require_once("../../../clases/class.EstructuraLari.php");


$objLari = new EstructuraLari();
$arrCdc = $objLari->getEstructura();





?>
<head>
<style type="text/css">

.fila1 { background-color:#e5e5e5; padding: 0px; }
.fila2 { background-color:#ffffff; padding: 0px; }

.titulo1 { 
  font: normal 12px Arial, Helvetica, sans-serif;
  color: red	 ;
  background: #FFECC0       ;
  height:28px;
  width: 50%;
  padding: 1px;
}

.titulo2 { 
  font: normal 12px Arial, Helvetica, sans-serif;
  color: white	 ;
  background: #006699       ;
  height:28px;
  width: 50%;
  padding: 1px;
}

.titulo3 { 
  font: normal 12px Arial, Helvetica, sans-serif;
  color: white	 ;
  background: #6E9CC8       ;
  height:28px;
  width: 50%;
  padding: 1px;
  text-align: center;
}

.celda_normal { 
  font: normal 11px Arial, Helvetica, sans-serif;
  color: #2A557F	 ;
  background: #FFFFFF       ;
  height:24px;
  width: 50%;
  padding: 1px;
  text-align: center;  
  border: 1px solid #CDCDCD;  
}

.celda_odd { 
  font: normal 11px Arial, Helvetica, sans-serif;
  color: #2A557F	 ;
  background: #E1EEF4       ;
  height:24px;
  width: 50%;
  padding: 1px;
  text-align: center;
  border: 1px solid #CDCDCD;
}

.tabla1 {
	border-collapse: collapse;
	border: 1px solid #175CAA; /*#000000;*/
	background: #6E9CC8;
	width: 80%;
	padding: 1px;
}

</style>
</head>

<body>

<?php
$i=1;
foreach ($arrCdc as $row1) {
	?>
	<table class="tabla1" style='width: 750px'>
		<tr><td class="titulo1" style='text-align: center'><?php echo $row1["cdc"]?></td></tr>
	</table>
	<?php 
	$idCdc = $row1["idlari_cdc"];
	$arrCdc_main = $objLari->getEstructuraCdc($idCdc) ;
	foreach ($arrCdc_main as $fila) {
	?>
		<tr>
		<table style='width: 750px' class="tabla1">
		<?php 
			$idCdcSub = $fila["intlari_cdc_sub"];
		?>
			<tr>
				<td class="titulo2" style="text-align: center" ><?php echo $fila["cdc_sub"]; ?></td>
			</tr>
		</table>
		<table class="tabla1" style="width: 750px">
			<tr>
				<td class="titulo3" style="width:15%">Apellido Paterno</td>
				<td class="titulo3" style="width:15%" >Apellido Materno</td>
				<td class="titulo3" style="width:15%">Nombre</td>
				<td class="titulo3" style="width:25%">Categoria</td>
				<td class="titulo3" style="width:10%">Celular</td>
				<td class="titulo3" style="width:10%">RPM</td>
				<td class="titulo3" style="width:10%">Correo</td>
			</tr>
		<?php
			$arrCdc_detalle = $objLari->getCDC($idCdc, $idCdcSub) ;	
			$c = 0;
			foreach ($arrCdc_detalle as $fila2) {
				if ($c%2==0)
					$estiloCelda = "celda_normal";
				else
					$estiloCelda = "celda_odd";
			?>
			<tr class="fila2">
			<td style="width:15%" class="<?php echo $estiloCelda?>"   ><?php echo $fila2["apellido_p"];?></td>
			<td style="width:15%" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["apellido_m"];?></td>
			<td style="width:15%" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["nombre"];?></td>
			<td style="width:25%" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["categoria"];?></td>
			<td style="width:10%; text-align: right;" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["celular"];?></td>
			<td style="width:10%; text-align: right;" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["rpm"];?></td>
			<td style="width:10%; text-align: center;" class="<?php echo $estiloCelda?>"  ><?php echo $fila2["correo"];?></td>
			</tr>
			<?php
			$c++;
			}
		?> 
		</table>
		<br/>
	<?php
	}
	$i++;

}

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<link href="../../../css/form-styles.css" rel="stylesheet" type="text/css">

<script>
function guardar() {
	//if (validar_requerido_jquery($("#txt_observaciones"),"Debe ingresar observaciones.")==false)
    //    {$("#txt_observaciones").focus();return false};	
		
	var url = "newPersona_ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data: $("#form1").serialize(), // Adjuntar los campos del formulario enviado.
		success: function(data)
		{
			var response = jQuery.trim(data);
			var resp = response.split("|");

			if (resp[0]=="1")  {  // grabo OK
				alert("Se registro correctamente a la persona.");
			}

		}
	});		
}

</script>

</head>

<body>
