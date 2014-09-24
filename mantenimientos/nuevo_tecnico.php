<?php
error_reporting(1);
ini_set('display_errors',1);


require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");
require_once("../clases/class.EmpresasCriticos.php");

$Tecnico = new TecnicosCriticos();

$Empresa = new EmpresasCriticos();

$arrEmpresas = $Empresa->ListarEmpresas();
//$arrCedulas = $Tecnico->ListarCelulas($idEmpresaTecnico);

$comboEmpresas = "<select name='cmbEmpresas' id='cmbEmpresas' class='caja_texto3' onchange='cambiarEmpresa();'>";
$comboEmpresas .= "<option value='0'>-- Seleccione --</option>";
foreach($arrEmpresas as $rowEmpresas) {
	if ($arrTecnico[0]["id_empresa"]==$rowEmpresas["id"]) 
		$comboEmpresas .= "<option value=".$rowEmpresas['id']." selected >".$rowEmpresas['nombre']."</option>";
	else
		$comboEmpresas .= "<option value=".$rowEmpresas['id'].">".$rowEmpresas['nombre']."</option>";
}

$comboEmpresas .= "</select>";


$comboOfficetrack = "<select name='cmbOfficetrack' id='cmbOfficetrack' class='caja_texto3'>";
$comboOfficetrack .= "<option value=0 selected='selected'>-- NO --</option>";
$comboOfficetrack .= "<option value=1 >-- SI --</option>";

$comboOfficetrack .= "</select>";

/*
$comboCedulas = "<select name='cmbCedulas' id='cmbCedulas' class='caja_texto3' style='width: 200px'>";
foreach ($arrCedulas as $rowCedulas ) {
	$comboCedulas .= "<option value='".$rowCedulas["idcedula"]."'>".$rowCedulas["cedula"]."</option>";
}

$comboCedulas .= "</select>";
*/
$comboCedulas = "";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title></title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
            <meta name="author" content="Sergio MC" />
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

                <?php include ("../../includes.php") ?>                

<!--<script type="text/javascript" src="../js/js.js"></script>-->

<script type="text/javascript">

function validar_requerido(field,alerttxt)
{
	var value = $(field).val();
	if (value==null||value==""||value==0)
		{alert(alerttxt);return false}
	else {return true}
}



$(document).ready(function(){

	$("#txtNombre").focus();
	
	$('#btnsalir').click(function(){
		//alert("aaa");
        $("#childModal_nuevo").dialog('close');
    });
	
	$('#btnGuardarTecnico').click(function(){
		var idtecnico = $("#txt_idtecnico");
        var nombre = $("#txtNombre");
		var apellidoP = $("#txtApellidoP");
		var apellidoM = $("#txtApellidoM");
		var empresa = $("#cmbEmpresas");
		var carnet = $("#txtCarnet");
		var carnetCritico = $("#txtCarnetCritico");
		var officetrack = $("#cmbOfficetrack");
		var cedula = $("#cmbCedulas");

		if (validar_requerido(nombre,"Falta llenar el campo Nombre.")==false)
		  { nombre.focus();return false}
		if (validar_requerido(apellidoP,"Falta llenar el campo Apellido Paterno.")==false)
		  {apellidoP.focus();return false}
		if (validar_requerido(apellidoM,"Falta llenar el campo Apellido Materno.")==false)
		  {apellidoM.focus();return false}		  
		if (validar_requerido(empresa,"Falta seleccionar la empresa.")==false)
		  {empresa.focus();return false}
		if (validar_requerido(carnet,"Ingrese el carnet.")==false)
		  {carnet.focus();return false}
		if (validar_requerido(carnetCritico,"Ingrese el carnet CRITICO.")==false)
		  {carnetCritico.focus();return false}

		// todo conforme, se procede a clonar usuario
		var pagina="receptor_nuevo_tecnico.php";

		$.ajax({
	        type: "POST",
	        url: pagina,
	        data: {
				action: 'nuevo_tecnico',
				nombre: nombre.val(),
				apellidoP: apellidoP.val(),
				apellidoM: apellidoM.val(),
				empresa: empresa.val(),
				carnet: carnet.val(),
				carnetCritico: carnetCritico.val(),
				officetrack: officetrack.val(),
				idcedula: cedula.val()				
			},
	        success: function(html){
				if (html=='ok') {
					alert("Nuevo tecnico ingresado correctamente.");
					$("#childModal_nuevo").dialog('close');
				}
				else {
					alert("Ocurrio un error.");
					return false;
				}
	        }
	    });
	  
    });	
	

});
                    
function cambiarEmpresa() {
	var idEmpresa = $("#cmbEmpresas").val();
	var pagina="receptor_nuevo_tecnico.php";
	//alert(pagina);

	$.ajax({
        type: "POST",
        url: pagina,
        data: {
			action: 'cambiar_empresa',
			idEmpresa: idEmpresa				
		},
        success: function(html){
        	$("#divCedulas").html(html);
        }
    });	
}
                 
</script>

<link rel="stylesheet" type="text/css" href="../../../css/estiloAdmin.css">
<link rel="stylesheet" type="text/css" href="../../../css/buttons.css">
</head>

<body>
	<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
	<input type="hidden" value="<?php echo $idtecnico?>" name="txt_idtecnico" id="txt_idtecnico"/>
	
	<div id="div_Clonar" class="divClonar">
	<table class="tablaClonar"  >
	<tr>
		<td class="celda_titulo">Apellido Paterno:</td>
		<td class="celda_res"  colspan="2">
			<input type="text" name="txtApellidoP" id="txtApellidoP" class="caja_texto3" value="<?php echo $arrTecnico[0]["ape_paterno"]?>"/></td>
	</tr>
	<tr>
		<td class="celda_titulo">Apellido Materno:</td>
		<td class="celda_res"  colspan="2">
			<input type="text" name="txtApellidoM" id="txtApellidoM" class="caja_texto3" value="<?php echo $arrTecnico[0]["ape_materno"]?>"/></td>
	</tr>
	<tr>
		<td class="celda_titulo">Nombres:</td>
		<td class="celda_res"  colspan="2">
			<input type="text" name="txtNombre" id="txtNombre" class="caja_texto3" value="<?php echo $arrTecnico[0]["nombres"]?>"/></td>
	</tr>	
	<tr>
		<td class="celda_titulo">Empresa:</td>
		<td class="celda_res"  colspan="2">
			<?php echo $comboEmpresas;?>
		</td>
	</tr>
	<tr>
		<td class="celda_titulo">Carnet Legado:</td>
		<td class="celda_res"  colspan="2">
			<input type="text" name="txtCarnet" id="txtCarnet" class="caja_texto3" value="<?php echo $arrTecnico[0]["carnet"]?>"/></td>
	</tr>
	<tr>
		<td class="celda_titulo">Carnet Critico:</td>
		<td class="celda_res"  colspan="2">
			<input type="text" name="txtCarnetCritico" id="txtCarnetCritico" class="caja_texto3" value="<?php echo $arrTecnico[0]["carnet_critico"]?>"/></td>
	</tr>	
	<tr>
		<td class="celda_titulo">Cedula:</td>
		<td class="celda_res"  colspan="2">
			<div id="divCedulas"><?php echo $comboCedulas;?></div>
		</td>
	</tr>	
	<tr>
		<td class="celda_titulo">Officetrack:</td>
		<td class="celda_res"  colspan="2">
			<?php echo $comboOfficetrack;?>
		</td>
	</tr>
	<tr>
		<td class="celda_res" colspan="3" align="center">
        
		<button id="btnGuardarTecnico" class="action blue" title="Generar Password" >
			<span class="label">Guardar Usuario</span>
		</button>
		<!--<button id="btnsalir" class="action red" title="Cancelar">
			<span class="label">Salir</span>
		</button>
		-->
		</td>
	</tr>

	</table>
		
	</div>

</body>
</html>
