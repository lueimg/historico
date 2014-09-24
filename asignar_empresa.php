<?php
include_once "../../clases/class.Conexion.php";
require_once('clases/usuarios_contratas_criticos.php');
require_once('clases/cedula.php');
session_start();
//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$usuario = $_SESSION["exp_user"]["id"];
$ob_usuario_contrata = new Usuarios_contratas_criticos();
$empresas = $ob_usuario_contrata->getUsuarios_contratas_criticos_id($cnx,$usuario);
$tot_empresas = sizeof($empresas);
if(isset($_REQUEST["codigo"])){
	$codigos = $_REQUEST["codigo"];
}

if(isset($_REQUEST["actividad"])){
	$actividad = $_REQUEST["actividad"];
}

$quie=$_REQUEST['quie'];

$ob_cedula = new Cedula();
$cedula = $ob_cedula->getCedulaAll($cnx);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Asignar Empresa</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>	
        <script type="text/javascript">
        		//Actualizacion de Tecnicos
        	$(document).ready(function(){
				$("#actualizar_empresa").click(function(){
					var empresa = $("#cb_empresa option:selected").text()
					var tecnico = $("#cb_tecnicos option:selected").text()
					var idtecnico=$("#cb_tecnicos").val()
					var cedula = $("#slct_cedula").val();
					
					if(empresa!="" && empresa!="Seleccione Empresa" && tecnico!="" && codigo!=""){
						var codigo = $("#codigo").val()
						var actividad = "<?php echo $actividad;?>"

						var parametros = {codigo:codigo,empresa:empresa,actividad:actividad,tecnico:tecnico,idtecnico:idtecnico,actualizar_empresa:"actualizar_empresa"}
							$.ajax({
						    	type: "POST",
						        url: "controladorHistorico/historicoController.php",
						        data: parametros,
						        dataType: "Json",
						        success: function (data) {
						        		window.parent.$("#filtro_general").click()
						        		if(data.estado){
						        			alert(data.msg)
						        		}else{
						        			alert("Error: no se pudo realizar la asignación")
						        			console.log(data.msg)
						        		}

						        		window.parent.jQuery('#dialog-asignar-empresa').dialog('close');						        		
						        },
						        error: function () {
						            alert("Error no se realizo la asignación");
						        }
						    });
					}
					else if(empresa=='' || empresa=="Seleccione Empresa"){
						alert("Selecione una empresa");
					}
					else if(cedula==''){
						alert("Selecione una cedula");
					}
					else if(tecnico==''){
						alert("Selecione un tecnico");
					}
			    });

			})


			function cargarTecnico(){			
				if($('#slct_cedula').val()==''){
					$('#cb_tecnicos').html('<option value="">-- Seleccione --</option>');
					$('#cb_tecnicos').val('');
				}
				else{
				var parametros = {cargarTecnico:"cargarTecnico",cedula:$("#slct_cedula").val(),idempresa:$("#cb_empresa").val(),quiebre:"<?php echo $quie; ?>"}
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
					        $('#cb_tecnicos').html('<option value="">-- Seleccione --</option>'+html);
					        $('#cb_tecnicos').val('');
				        },
				        error: function () {
				            alert("Error no cargaron los tecnicos");
				        }
				    });
				}
			}

			function cargarCedula(){	
			$('#cb_tecnicos').html('<option value="">-- Seleccione --</option>');
			$('#cb_tecnicos').val('');		
				if($('#cb_empresa').val()==''){
					$('#slct_cedula').html('<option value="">-- Seleccione --</option>');
					$('#slct_cedula').val('');					
				}
				else{
				var parametros = {cargarCedula:"cargarCedula",idempresa:$("#cb_empresa").val()}
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

<div class="asignacion_clientes">
	<form name="frm_criticos" id="frm_criticos" action="" method="POST">
		<div class="filtro_clientes">
				<div class="filtroxcampo">
				  	<label>Empresa</label>
				  	<?php
					if($empresas!=""){
						if($tot_empresas>1){
					?>
						<select class="cb_empresa" id="cb_empresa" name="cb_empresa" onchange="cargarCedula();cargarTecnico();">
						<option value="">Seleccione Empresa</option>
					<?php
								foreach ($empresas as $emp):
					?>
									<option value="<?php echo $emp["id_empresa"];?>" data-id="<?php echo $emp["id"];?>"><?php echo $emp["nombre"];?></option>
					<?php
								endforeach;
					?>
						</select>
					<?php
							}else{
					?>
						<select class="cb_empresa" id="cb_empresa" name="cb_empresa" onchange="cargarCedula();cargarTecnico();" >
							<option value="<?php echo $empresas[0]["id_empresa"];?>" selected><?php echo $empresas[0]["nombre"];?></option>
						</select>
					<?php
							}
						}
					?>
					<input type="hidden" id="codigo" name="codigo" value="<?php echo $codigos;?>">
			  	</div>
			  	<div class="filtroxcampo">
				  	<label>Celula</label>
				  	<select class="slct_cedula" id="slct_cedula" name="slct_cedula" onchange="cargarTecnico();">
				  	<option value=''>-- Seleccione --</option>
					
					</select>
			  	</div>
			  	<div class="filtroxcampo" style="display:block">
				  	<label>Tecnicos</label>
				  	<select class="cb_tecnicos" id="cb_tecnicos" name="cb_tecnicos" >
				  	<option value=''>-- Seleccione --</option>
				  	</select>
				</div>
			  	<div class="asignar">
			  		<div class="btn_buscar" id="actualizar_empresa">Aceptar</div>
			  	</div>
		</div>

		<div class="registrar">
			
		</div>
	</form>
</div>
</body>
</html>