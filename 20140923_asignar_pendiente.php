<?php
include_once "../../clases/class.Conexion.php";
require_once('clases/averias.php');
require_once('clases/gestionCriticos.php');
require_once('clases/empresa.php');
require_once('clases/tecnicos.php');
require_once('clases/cedula.php');
require_once('clases/usuarios_contratas_criticos.php');

session_start();
$IDUSUARIO = $_SESSION["exp_user"]["id"];
$codigos = $_REQUEST["codigo"];
$empresa_seleccionadas = $_REQUEST["empresa"];
$quiebre=$_REQUEST['quiebre'];
//$tot_empresa_seleccionadas = sizeof($empresa_seleccionadas);
$pos = "";
$pos = strpos($empresa_seleccionadas, ",");


$actividad = $_REQUEST["actividad"];
//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$ob_usuario_contrata = new Usuarios_contratas_criticos();
$empresas_asignadas = $ob_usuario_contrata->getUsuarios_contratas_criticos_id($cnx,$IDUSUARIO);
$id_empresa=0;

//si tiene , es por que hay mas de una empresa seleccionada
if($pos!==false){
	$tot_empresas = "varias";
	//
}else if($pos===false && $empresa_seleccionadas!=""){
	$ob_empresa = new Empresa();
	$id_empresa = $ob_empresa->getIdEmpresa($cnx,$empresa_seleccionadas);
	$tot_empresas = "";
}


$ob_cedula = new Cedula();
$cedula = $ob_cedula->getCedulaAll($cnx,$id_empresa);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>	
        <script type="text/javascript">
        		//Actualizacion de Tecnicos
        	$(document).ready(function(){

        		tot_empresas  = "<?php echo $tot_empresas;?>" //este dato me indica si es mas de una empresa
				if(tot_empresas==""){
				    $("#empresa").attr("disabled","disabled")
				}else{
				    //si es mas de una empresa necesariamente tendra que asignar una nueva empresa
				}

				$(".registrar_pendientes").click(function(){

					var cedula=$("#slct_cedula").val();
					var empresa = $("#empresa option:selected").val()
					if(tot_empresas=="" && empresa==""){
						empresa = "<?php echo $empresa_seleccionadas;?>"
					}else{
						empresa = empresa
					}
					var codigos = "<?php echo $codigos;?>"
					var actividad = "<?php echo $actividad;?>"
					var tecnico = $("#tecnico option:selected").text()
					var idtecnico=$("#tecnico").val()
					if(!tecnico){
						tecnico = ""
						idtecnico=""
					}
					var observacion = $("#observacion").val()
					var usuario = "<?php echo $IDUSUARIO;?>"
					if($("#flag_tecnico").attr("checked")){
						var flag_tecnico = $("#flag_tecnico").val()
					}
					
					tot_empresas  = "<?php echo $tot_empresas;?>"
					if(empresa==""){
						alert("Seleccione una empresa");						
					}
					else if(cedula==''){
						alert("Seleccione una cedula");
					}
					else if(tecnico==''){
						alert('Seleccione un tecnico');
					}
					else{
						var parametros = {empresa:empresa,codigos:codigos,actividad:actividad,tecnico:tecnico,idtecnico:idtecnico,flag_tecnico:flag_tecnico,observacion:observacion,usuario:usuario,registrar_pendientes:"registrar_pendientes"}
							$.ajax({
						    	type: "POST",
						        url: "controladorHistorico/historicoController.php",
						        data: parametros,
						        dataType: "html",
						        success: function (data) {
						        	window.parent.$("#filtro_general").click();
						        	alert(data);
						            window.parent.jQuery('#dialog-asignar-pendiente').dialog('close');						            
						             window.parent.$("#seleccion_general").removeAttr('checked')
						        		/*
						        		window.parent.jQuery('#dialog-asignar-empresa').dialog('close');
						        		window.parent.$("#filtro_general").click()*/
						        },
						        error: function () {
						        	window.parent.jQuery('#dialog-asignar-pendiente').dialog('close');
						            alert("Error no se realizo el Registro Masivo");
						        }
						    });
					}
			    });

				$("#cambiar_empresa").click(function(){
					
					if($("#cambiar_empresa").attr('checked')){//si ya esta marcado
				    	$("#empresa").removeAttr("disabled")
				    	$("#tecnico").removeAttr("disabled")
				    	$("#flag_tecnico").removeAttr("disabled")
					}else{
						if(tot_empresas==""){
						    $("#empresa").attr("disabled","disabled")
						}else{
						    //si es mas de una empresa necesariamente tendra que asignar una nueva empresa
						}
					}
					
				});

			})

			function cargarTecnico(){			
				if($('#slct_cedula').val()==''){
					$('#tecnico').html('<option value="">-- Seleccione --</option>');
					$('#tecnico').val('');
				}
				else{
				var parametros = {cargarTecnico:"cargarTecnico",cedula:$("#slct_cedula").val(),idempresa:$("#empresa").val(),quiebre:"<?php echo $quiebre; ?>"}
					$.ajax({
				    	type: "POST",
				        url: "controladorHistorico/historicoController.php",
				        data: parametros,
				        dataType: "Json",
				        success: function (obj) {
				        	var html='';
				        	if(obj!=null){
				        		$.each(obj,function(key,data){
									html+='<option value="'+data.id+'">'+data.nombre+'</option>';
								});
				        	}							
					        $('#tecnico').html('<option value="">-- Seleccione --</option>'+html);
					        $('#tecnico').val('');
				        },
				        error: function () {
				            alert("Error no cargaron los tecnicos");
				        }
				    });
				}
			}

			function cargarCedula(){	
			$('#tecnico').html('<option value="">-- Seleccione --</option>');
			$('#tecnico').val('');		
				if($('#empresa').val()==''){
					$('#slct_cedula').html('<option value="">-- Seleccione --</option>');
					$('#slct_cedula').val('');					
				}
				else{
				var parametros = {cargarCedula:"cargarCedula",idempresa:$("#empresa").val()}
					$.ajax({
				    	type: "POST",
				        url: "controladorHistorico/historicoController.php",
				        data: parametros,
				        dataType: "Json",
				        success: function (obj) {
				        	var html='';
				        	if(obj!=null){
								$.each(obj,function(key,data){
									html+='<option value="'+data.id+'">'+data.nombre+'</option>';
								});
							}
					        $('#slct_cedula').html('<option value="">-- Seleccione --</option>'+html);
					        $('#slct_cedula').val('');
				        },
				        error: function () {
				            alert("Error no cargaron los tecnicos");
				        }
				    });
				}
			}
        </script>
		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
		<link type="text/css" href='css/horarios.css' rel="Stylesheet" />
</head>

<body>
<div class="modalPop"></div>


<div class="registro_clientes">
<form name="frm_criticos" id="frm_criticos" action="" method="POST" class="pendientes">
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
		<div class="datos">

			<div class="content_datos">
				<div class="caja_text">
					<label>Observacion:</label>
				</div>
				<div class="caja_text">
					<textarea class="border" maxlength="255" value="" id="observacion" name="observacion"></textarea>
				</div>
			</div>
			<?php
				if($tot_empresas==""){
			?>
			<div class="content_datos">
			<span class="checkbox">
						<input type="checkbox" value="si" name="cambiar_empresa" id="cambiar_empresa"> Cambiar Empresa
					</span>
			</div>
			<?php
				}
			?>
			<div class="content_datos">
				<div class="caja_medium">
					<label>Empresa:</label>
					<select class="empresa" id="empresa" name="empresa" onchange="cargarCedula();cargarTecnico();">
					<?php
						if($pos!==false){
					?>
								<option value="">Seleccione una empresa</option>
					<?php
							foreach ($empresas_asignadas as $emp):
					?>
								<option value="<?php echo $emp["id_empresa"];?>"><?php echo $emp["nombre"];?></option>
					<?php
							endforeach;
						}else if($pos===false && $empresa_seleccionadas!=""){
							foreach ($empresas_asignadas as $emp):
								if($empresa_seleccionadas==$emp["nombre"]){
					?>
									<option value="<?php echo $emp["id_empresa"];?>" selected><?php echo $emp["nombre"];?></option>
					<?php
								}else{
					?>
									<option value="<?php echo $emp["id_empresa"];?>" selected><?php echo $emp["nombre"];?></option>
					<?php
								}
							endforeach;
					?>
					<?php
						}
					?>
					</select>
				</div>
			</div>
			<div class="content_datos">
			  	<label>Celula</label>
			  	<select class="slct_cedula" id="slct_cedula" name="slct_cedula" onchange="cargarTecnico();">
			  	<option value=''>-- Seleccione --</option>
				<?php
					foreach ($cedula as $r):
				?>
					<option value="<?php echo $r["id"];?>"><?php echo $r["nombre"];?></option>
				<?php
					endforeach;
				?>
				</select>
		  	</div>
		  	<div class="content_datos">
		  		<label>Tecnico</label>
			  	<select class="tecnico" id="tecnico" name="tecnico" >
			  	<option value=''>-- Seleccione --</option>
			  	</select>

			  	<span class="checkbox">
					<input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico">Tecnico Entregado
				</span>
			</div>
		</div>

		<div class="registrar_pendientes">Registrar Pendientes</div>
	</form>
</div>
</body>
</html>