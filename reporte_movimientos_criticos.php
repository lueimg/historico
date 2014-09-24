<?php
require_once("../../cabecera.php");
require_once('clases/empresa.php');
require_once('clases/zonales.php');
require_once('clases/lejano.php');
require_once('clases/microzona.php');
require_once('clases/area2.php');
require_once('clases/movistar1.php');
require_once('clases/area_.php');
require_once('clases/tecnicos.php');
require_once('clases/mdfs.php');
require_once('clases/nodos.php');
require_once('clases/estados.php');
require_once('clases/gestionCriticos.php');
require_once('clases/usuarios_contratas_criticos.php');

//Abriendo la conexion
 $db = new Conexion();
 $cnx = $db->conectarPDO();

$ob_empresa = new Empresa();
//$_SESSION["exp_user"]["id"] = "3";
$usuario = $_SESSION["exp_user"]["id"];
$ob_usuario_contrata = new Usuarios_contratas_criticos();
$empresas = $ob_usuario_contrata->getUsuarios_contratas_criticos_id($cnx,$usuario);
$tot_empresas = sizeof($empresas);

$contratas = "";
if($tot_empresas>1){
	$contratas = "";
	foreach ($empresas as $emp){
		$contratas .= "'".$emp["nombre"]."',";
	}

	$contratas = substr($contratas, 0,strlen($contratas)-1);
}else{
	$contratas = "'".$empresas[0]["nombre"]."'";
}

$ob_zonales = new Zonales();
$zonales = $ob_zonales->getZonalAll($cnx);

$ob_lejano = new lejano();
$lejanos = $ob_lejano->getLejanoAll($cnx);

$ob_microzona = new Microzona();
$microzona = $ob_microzona->getMicrozonalAll($cnx);

$ob_area2 = new Area2();
$area2s = $ob_area2->getArea2All($cnx);

$ob_mdfs = new Mdfs();
$mdfs = $ob_mdfs->getMdfsAll($cnx);

$ob_nodos = new Nodos();
$nodos = $ob_nodos->getNodos($cnx);

$ob_aream = new Area_();
$area_n = $ob_aream->getArea_All($cnx);

$ob_movistar1 = new Movistar1();
$movistar1 = $ob_movistar1->getMovistar1All($cnx);

$ob_estados = new Estados();
$estados = $ob_estados->getEstadoAll($cnx);

$estados_neo = array();
$i=0;
foreach ($estados as $estado1):
	foreach ($estados as $estado2):

		if($estado1["estado"]==$estado2["estado"]){
			$estados_neo[$i]["id"] .= $estado2["id"].",";
			$estados_neo[$i]["estado"] = html_entity_decode($estado2["estado"]);
		}
	endforeach;

		if(@$estados_neo[$i-1]["estado"]===$estados_neo[$i]["estado"]){
			unset($estados_neo[$i]);
		}else{
			$estados_neo[$i]["id"] = substr($estados_neo[$i]["id"],0,strlen($estados_neo[$i]["id"])-1);
			$i++;
		}
endforeach;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>PSI - Web SMS - Clientes Críticos</title>
        <!--<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">-->
        <meta http-equiv="content-type" content="text/html; charset=utf8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>

        <script type="text/javascript" src="js/jquery.multiselect.min.js"></script>
		<script type="text/javascript" src="js/select.js"></script>
		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
</head>

<body>
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
<!--<input type="hidden" value="<?php echo $data_emp["empresa"];?>" name="empresa_raiz" id="empresa_raiz"/>-->
<!--<div id="page-wrap">-->
<?php echo pintar_cabecera(); ?>

<div class="bandeja_clientes">
	<div class="filtro_criticos">
		<div class="filtro_clientes">
		<form action="generar_movimientos.php" method="POST" name="frmExcelMov" id="frmExcelMov">
			<fieldset>
			<legend class="title">Filtrar por Fecha:</legend>
			 <div class="filtroxcampo">
			  	<input type="radio" value="act" name="reporte" id="reporte_mov_1" checked /><label> Fecha Registro de Actuación</label>
			  	<input type="text" maxlength="17" class="buscarFecha" name="h_fecha_ini_act" id="h_fecha_ini_act" disabled />
			  	<input type="hidden" name="fecha_ini_act" id="fecha_ini_act" />
			  	<input type="text" maxlength="17" class="buscarFecha" name="h_fecha_fin_act" id="h_fecha_fin_act" disabled/>
			  	<input type="hidden" name="fecha_fin_act" id="fecha_fin_act"/>
			  </div>
			  <div class="filtroxcampo asignar">
			    <span class="btn_buscar generar_movimientos">Reporte de Movimientos</span>
				<input type="hidden" value="<?php echo $contratas;?>" name="empresa_usuario" id="empresa_usuario">

			  </div>
			  <div class="filtroxcampo" style="width:200px">
			  </div>
			  <div class="filtroxcampo">
			  	<input type="radio" value="atc" name="reporte" id="reporte_mov_2" /><label style="padding-left:52px"> Fecha Registro ATC</label>
			  	<input type="text" maxlength="17" class="buscarFecha" name="h_fecha_ini_atc" id="h_fecha_ini_atc" disabled />
			  	<input type="hidden" name="fecha_ini_atc" id="fecha_ini_atc" />
			  	<input type="text" maxlength="17" class="buscarFecha" name="h_fecha_fin_atc" id="h_fecha_fin_atc" disabled/>
			  	<input type="hidden" name="fecha_fin_atc" id="fecha_fin_atc" />
			  </div>
		  	</fieldset>
		</form>
		</div>
	</div>
	<div class="listado_clientes">
	</div>
</div>

<div id="dialog-criticos" title="Registro de clientes criticos"></div>
<div id="dialog-gestion-criticos" title="Gesti&oacute;n Clientes Cr&iacute;ticos"></div>
<div id="dialog-gestion-averias" title="Gesti&oacute;n Averias"></div>
<div id="dialog-gestion-movimientos" title="Gesti&oacute;n Movimientos"></div>
<div id="dialog-asignar-empresa" title="Asignar Empresa"></div>
<div id="dialog-asignar-tecnico" title="Asignar T&eacute;cnico"></div>
<div id="dialog-asignar-pendiente" title="Asignar Pendiente"></div>
<!--<div id="dialog-excel" title="Generar Excel"></div>-->
<iframe id="dialog-excel" src="" width="100" height="300" style="display:none"></iframe>