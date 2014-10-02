
<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");

if ($_POST['action']=='cambiar_empresa')
{
	$idEmpresa = $_POST["idEmpresa"];
	//echo $idEmpresa;
	$Tecnico = new TecnicosCriticos();
	$arrCedulas = $Tecnico->ListarCelulas($idEmpresa);

	$comboCedulas = "";
	$comboCedulas = "<select name='cmbCedulas_editar' id='cmbCedulas_editar' class='caja_texto3' style='width: 200px'>";
	foreach ($arrCedulas as $rowCedulas ) {
		$comboCedulas .= "<option value='".$rowCedulas["idcedula"]."'>".$rowCedulas["cedula"]."</option>";
	}

	$comboCedulas .= "</select>";
	echo $comboCedulas;

}
else if ($_POST['action']=='editar_tecnico')
{
	$idtecnico = $_POST["idtecnico"];
	$nombre = $_POST["nombre"];
	$apellidoP = $_POST["apellidoP"];
	$apellidoM = $_POST["apellidoM"];
	$empresa = $_POST["empresa"];
	$carnet = $_POST["carnet"];
	$carnetCritico = $_POST["carnetCritico"];
	$officetrack = $_POST["officetrack"];
	$idCedula = $_POST["idcedula"];
	$quiebres = $_POST["quiebres"];

	$Tecnico = new TecnicosCriticos();
	if ( $Tecnico->EditarTecnico( $idtecnico, $nombre, $apellidoP, $apellidoM, $empresa,
            $carnet, $carnetCritico, $officetrack, $idCedula,$quiebres )=="1" )
		print 1;
	else
		echo "error";
    
}



?>
