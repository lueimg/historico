<?php
include_once "../../clases/class.Conexion.php";
require_once('clases/empresa.php');
require_once('clases/cedula.php');
//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();
if($_REQUEST["empresa"]){
	$empresa = $_REQUEST["empresa"];
	$codigo = $_REQUEST["codigo"];
	$estado = $_REQUEST["estado"];
	$quiebre=$_REQUEST['quiebre'];


	$ob_empresa = new Empresa();
	$id_empresa = $ob_empresa->getIdEmpresa($cnx,$empresa);
	$ob_cedula = new Cedula();
	$cedula = $ob_cedula->getCedulaAll($cnx,$id_empresa,$quiebre);
}else{
	echo "No se pueden cargar técnicos sin el dato empresa";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Asignar Técnico</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>	
        <script type="text/javascript">
        		//Actualizacion de Tecnicos
        	$(document).ready(function(){
				$("#actualizar_tecnico").click(function(){

					var error="";

					if($("#slct_cedula").val()==''){
						alert('Seleccione cedula');
						error="error";
						$("#slct_cedula").focus();
					}
					else if($("#cb_tecnico").val()==''){
						alert('Seleccione Tecnico');
						error="error";
						$("#cb_tecnico").focus();
					}

					if(error==''){
						var tecnico = $("#cb_tecnico option:selected").text()
						var idtecnico = $("#cb_tecnico").val()
						if($("#flag_tecnico").attr("checked")){
							var flag_tecnico = $("#flag_tecnico").val()
						}else{
							var flag_tecnico = "no"
						}
						var codigo = $("#codigo").val()
						var estado = $("#estado").val()
						var parametros = {codigo:codigo,tecnico:tecnico,idtecnico:idtecnico,flag_tecnico:flag_tecnico,estado:estado,actualizar_tecnico:"actualizar_tecnico"}
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

						        		window.parent.jQuery('#dialog-asignar-tecnico').dialog('close');						        		
						        },
						        error: function () {
						            alert("Error no se realizo la asignación");
						        }
						    });
					}					
			    });
			})

			function cargarTecnico(){			
					if($('#slct_cedula').val()==''){
						$('#cb_tecnico').html('<option value="">-- Seleccione --</option>');
						$('#cb_tecnico').val('');
					}
					else{
					var parametros = {cargarTecnico:"cargarTecnico",cedula:$("#slct_cedula").val(),idempresa:$("#idempresa").val(),quiebre:$("#quiebre").val()}
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
						        $('#cb_tecnico').html('<option value="">-- Seleccione --</option>'+html);
						        $('#cb_tecnico').val('');
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
			  	</div><br>
				<div class="filtroxcampo">
				  	<label>Técnicos</label>
				  	<select class="cb_tecnico" id="cb_tecnico" name="cb_tecnico" >
					<option value=''>-- Seleccione --</option>
					</select>
					<div>
						<span class="checkbox">
							<input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico" style="margin:5px 5px 0 0">Tecnico Entregado
						</span>
					</div>
					<input type="hidden" id="codigo" name="codigo" value="<?php echo $codigo;?>">
					<input type="hidden" id="estado" name="estado" value="<?php echo $estado;?>">
					<input type="hidden" id="empresa" name="empresa" value="<?php echo $empresa;?>">
					<input type="hidden" id="idempresa" name="idempresa" value="<?php echo $id_empresa;?>">
					<input type="hidden" id="quiebre" name="quiebre" value="<?php echo $quiebre;?>">
			  	</div>
			  	<div class="asignar">
			  		<div class="btn_buscar" id="actualizar_tecnico">Aceptar</div>
			  	</div>
		</div>

		<div class="registrar">
			
		</div>
	</form>
</div>
</body>
</html>