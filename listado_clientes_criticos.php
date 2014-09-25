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
require_once('clases/distrito.php');
require_once('clases/quiebre.php');
require_once('clases/mdfs.php');
require_once('clases/nodos.php');
require_once('clases/estados.php');
require_once('clases/gestionCriticos.php');
require_once('clases/usuarios_contratas_criticos.php');

//Abriendo la conexion
 $db = new Conexion();
 $cnx = $db->conectarPDO();

$ob_empresa = new Empresa();
$usuario = $_SESSION["exp_user"]["id"];
$ob_usuario_contrata = new Usuarios_contratas_criticos();
$empresas = $ob_usuario_contrata->getUsuarios_contratas_criticos_id($cnx,$usuario);
$tot_empresas = sizeof($empresas);

$data_emp = array();
if($tot_empresas>1){
	$contratas = "";
	foreach ($empresas as $emp){
		$contratas .= "'".$emp["nombre"]."',";
	}

	$contratas = substr($contratas, 0,strlen($contratas)-1);
	$data_emp = array("empresa"=> $contratas);
	//para tecnicos todos
	$ob_tecnico = new Tecnicos();
	$tecnicos = $ob_tecnico->getTecnicoAll($cnx);
}else{
	//echo "ff".$empresas[0]["nombre"]."pp";
	$nombre_empresa = $empresas[0]["nombre"];
	$data_emp = array("empresa"=>"'".$nombre_empresa."'");

	//para tecnicos x empresa
	$ob_tecnico = new Tecnicos();
	$tecnicos = $ob_tecnico->getTecnicoAllxEmpresa($cnx,$empresas[0]["id_empresa"]);
}

/*print_r($data_emp);
exit();*/


//Mostrar nuevos ingresos
$nuevoCritico = "";
if ( isset( $_POST["hNumProvCtc"] ) ) {
    $nuevoCritico = "provision";
}
if ( isset( $_POST["hNumAverCtc"] ) ) {
    $nuevoCritico = "averia";
}

//$ob_cliente = new gestionCriticos();
//$cliente = $ob_cliente->getGestionCriticosFiltro($cnx,$data_emp,1,1,$nuevoCritico);
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

$ob_distrito = new Distrito();
$distrito = $ob_distrito->getDistritoAll($cnx);

$ob_quiebre = new Quiebre();
$quiebre = $ob_quiebre->getQuiebre($cnx,$usuario);

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

echo "<!--";
//print_r($cliente);
echo "-->";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>PSI - Web SMS - Clientes Críticos</title>
        <!--<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">-->
        <meta http-equiv="content-type" content="text/html; charset=utf8">
        <meta name="author" content="Sergio MC" />

        <?php include ("../../includes.php") ?>

        <link type="text/css" href='css/demo_page.css' rel="Stylesheet" />
		<link type="text/css" href='css/demo_table.css' rel="Stylesheet" />
		<link type="text/css" href='css/TableTools.css' rel="Stylesheet" />
		<link type="text/css" href='css/prettify.css' rel="Stylesheet" />
		<link type="text/css" href='css/jquery.multiselect.css' rel="Stylesheet" />

        <script type="text/javascript" src="js/prettify.js"></script>
        <script type="text/javascript" src="js/jquery.multiselect.min.js"></script>
		
        <script type="text/javascript" src="js/historico.js"></script>
		<script type="text/javascript" src="js/select.js"></script>
        <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/criticos_datatable.js"></script>
        <script type="text/javascript" src="js/jquery.iframe.js"></script>

        <script type="text/javascript" charset="utf-8" src="js/ZeroClipboard/ZeroClipboard.js"></script>
		<!--<script type="text/javascript" charset="utf-8" src="js/TableTools.js"></script>-->

        <!--<script type="text/javascript" charset="utf-8">
			
		</script>-->
		
		<link type="text/css" href='css/estilo.css' rel="Stylesheet" />
</head>

<body>
<div class="modalPop"></div>
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
<input type="hidden" value="<?php echo $data_emp["empresa"];?>" name="empresa_raiz" id="empresa_raiz"/>
<!--<div id="page-wrap">-->
<?php echo pintar_cabecera(); ?>

<div class="bandeja_clientes">
	<div class="filtro_criticos">
	<form action="generar_excel.php" method="POST" name="frmExcel" id="frmExcel">
		<div class="filtro_clientes">
		  <div class="filtro_tan">
			<label>Buscar por:</label>
			<select name="filtro_tan" id="filtro_tan">
				<option value="telefono">Telefono</option>
				<option value="averia">Averia</option>
				<option value="nombre">Nombre de Cliente</option>
				<option value="atc">ATC/RTC</option>
				<option value="id_gestion">ID GESTION</option>
			</select>
			<input class="border" type="text" value="" name="txt_tan" id="txt_tan">
			<span class="btn_buscar" id="filtro_personalizado"><img src="img/lupa.png" title="Buscar">Busqueda Personalizada</span>
			<span class="filtroxcampo">
			  	<label>Tipo de Actividad</label>
			  	<select class="actividades" id="actividades" multiple="multiple" name="actividades[]" >
					<option value="Averias">Averias</option>
					<option value="Manual">Ingreso  Manual</option>
					<option value="Provision">Provision</option>
					<option value="ALTA">Alta</option>
					<option value="RUTINA">Rutina</option>
				</select>
			</span>
			<div class="filtroxcampo asignar">
					<span class="btn_buscar" id="btn_limpiar_filtros">Limpiar Filtros</span>
				</div>
		  </div>
		</div>
		<div class="filtro_clientes">
		
			<fieldset>
			<legend class="title">Filtrar por:</legend>
			  <div class="filtroxcampo">
			  	<label>Empresa</label>
			  	
				<?php
					if($empresas!=""){
						if($tot_empresas>1){
						?>
					<select class="empresa" id="empresa" multiple="multiple" name="empresa[]">
				<?php
							foreach ($empresas as $emp):
				?>
								<option value="<?php echo $emp["nombre"];?>" data-id="<?php echo $emp["id"];?>"><?php echo $emp["nombre"];?></option>
				<?php
							endforeach;
				?>
					</select>
				<?php
					}else{
				?>
					<input type="text" value="<?php echo $empresas[0]["nombre"];?>" data-id="<?php echo $empresas[0]["id_empresa"];?>" disabled>
					<input id="empresa" multiple="multiple" name="empresa" type="hidden" value="<?php echo $empresas[0]["nombre"];?>" data-id="<?php echo $empresas[0]["id"];?>">
				<?php
						}
					}
				?>
				
			  </div>
			  <div class="filtroxcampo">
			  	<label>Zonal</label>
			  	<select class="zonales" id="zonales" multiple="multiple" name="zonales[]" >
				<?php
					foreach ($zonales as $zona):
				?>
					<option value="<?php echo $zona["abreviatura"];?>"><?php echo $zona["zonal"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Negocio</label>
			  	<select class="negocio" id="negocio" multiple="multiple" name="negocio[]" >
					<option value="aver-catv-pais','pen_prov_catv','rutina-bas-lima">CATV</option>
					<option value="aver-bas-lima','pen_prov_bas','rutina-catv-pais">STB (telefonía básica)</option>
					<option value="aver-adsl-pais','pen_prov_adsl','rutina-adsl-pais">ADSL (Speedy)</option>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Microzona</label>
			  	<select class="microzona" id="microzona" multiple="multiple" name="microzona[]" >
				<option value="">sin microzona</option>
					<?php
					foreach ($microzona as $microz):
				?>
					<option value="<?php echo $microz["microzona"];?>"><?php echo $microz["microzona"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Area2</label>
			  	<select class="area2" id="area2" multiple="multiple" name="area2[]">
			  	<option value="">sin area</option>
					<?php
					foreach ($area2s as $area2):
				?>
					<option value="<?php echo $area2["area2"];?>"><?php echo $area2["area2"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Lejano</label>
			  	<select class="lejano" id="lejano" multiple="multiple" name="lejano[]" >
					<?php
					foreach ($lejanos as $lejano):
				?>
					<option value="<?php echo $lejano["lejano"];?>"><?php echo $lejano["lejano"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>MDF</label>
			  	<select class="mdf" id="mdf" multiple="multiple" name="mdf[]">
			  	<?php
					foreach ($mdfs as $mdf):
				?>
					<option value="<?php echo $mdf["MDF"];?>"><?php echo $mdf["MDF"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Nodos</label>
			  	<select class="nodo" id="nodo" multiple="multiple" name="nodo[]">
					<?php
					foreach ($nodos as $nodo):
				?>
					<option value="<?php echo $nodo["NODO"];?>"><?php echo $nodo["NODO"];?></option>
				<?php
					endforeach;
				?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Estados</label>
			  	<select class="estados" id="estados" multiple="multiple" name="estados[]" >
				<?php
					foreach ($estados_neo as $estado):
				?>
					<option value="<?php echo $estado["id"];?>"><?php echo $estado["estado"];?></option>
				<?php
					endforeach;
				?>
				<option value="Temporal">Temporal</option>
				</select>
				 
			  </div>
			  
			  <div class="filtroxcampo">
			  	<label>Asig. Técnicos</label>
			  	<select class="filtro_tecnico" id="filtro_tecnico" multiple="multiple" name="filtro_tecnico[]" >
			  		<option value="Tecnico Entregado">Técnico Entregado</option>
			  		<option value="Tecnico Asignado">Técnico Asignado</option>
			  		<option value="">Sin Técnico</option>
				</select>
				
			  </div>
			  <div class="filtroxcampo">
			  	<label>Técnicos</label>
			  	<select class="tecnico_nombre" id="tecnico_nombre" multiple="multiple" name="tecnico_nombre[]">
			  	<option value="">sin técnico</option>
			 	<?php
					foreach ($tecnicos as $tec):
				?>
					<option value="<?php echo $tec["nombre_tecnico"];?>"><?php echo $tec["nombre_tecnico"];?></option>
				<?php
					endforeach;
				?>
				</select>
				<div class="filtroxcampo asignar">
					<!--<span class="btn_buscar generar_excel"><a href="generar_excel.php">Generar Excel</a></span>-->
					<span class="btn_buscar generar_excel">Generar Excel</span>
					<!--<input type="submit" class="btn_buscar generar_excel" value="G/enerar Excel" name="generar_excel" id="generar_excel">-->
				</div>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Quiebre</label>
			  	<select class="quiebre" id="quiebre" multiple="multiple" name="quiebre[]">
					<option value="null">sin quiebre</option>
					<?php
						foreach ($quiebre as $quiebre_1):
					?>
						<option value="<?php echo $quiebre_1["id"];?>"><?php echo $quiebre_1["nombre"];?></option>
					<?php
						endforeach;
					?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Area_</label>
			  	<select class="area_" id="area_" multiple="multiple" name="area_[]">
			  		<option value="">sin area</option>
					<?php
						foreach ($area_n as $area_n2):
					?>
						<option value="<?php echo $area_n2["area_"];?>"><?php echo $area_n2["area_"];?></option>
					<?php
						endforeach;
					?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Movistar1</label>
			  	<select class="movistar1" id="movistar1" multiple="multiple" name="movistar1[]">
					<option value="null">sin movistar</option>
					<?php
						foreach ($movistar1 as $movistar_1):
					?>
						<option value="<?php echo $movistar_1["averia_m1"];?>"><?php echo $movistar_1["averia_m1"];?></option>
					<?php
						endforeach;
					?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Distrito</label>
			  	<select class="distrito" id="distrito" multiple="multiple" name="distrito[]">
					<option value="null">sin distrito</option>
					<?php
						foreach ($distrito as $distrito_1):
					?>
						<option value="<?php echo $distrito_1["distrito"];?>"><?php echo $distrito_1["distrito"];?></option>
					<?php
						endforeach;
					?>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Estado Transmision Inf.</label>
			  	<select class="esttransmision" id="esttransmision" multiple="multiple" name="esttransmision[]">
					<option value="null">sin Estado</option>
					<option value="0001-Inicio">Inicio</option>
					<option value="0002-Supervision">Supervision</option>
					<option value="0003-Cierre">Cierre</option>
				</select>
			  </div>
			  <div class="filtroxcampo">
			  	<label>Fecha Reg</label>
			  	<input type="text" maxlength="17" class="buscarFecha" name="fecha_ini" id="fecha_ini" disabled />
			  	<input type="text" maxlength="17" class="buscarFecha" name="fecha_fin" id="fecha_fin" disabled/>
			  </div>
			  <div class="filtroxcampo asignar">
			    <span class="btn_buscar asignar_pendientes">Registrar Pendientes</span>
				<span class="btn_buscar asignar_empresa">Asignar Empresa</span>
				<span class="btn_buscar asignar_tecnico">Asignar Tecnicos</span>
				<span class="btn_buscar registro_manual">Registro Manual Pendiente</span>
				<span class="btn_buscar visor_gps">Visor GPS</span>

				<span class="btn_buscar horarios">Gestion Horarios</span>
				<span class="btn_buscar pendientes">Gestion Pendientes</span>
				<input type="hidden" value="<?php echo $tot_empresas;?>" name="empresa_usuario" id="empresa_usuario">
				<input type="hidden" name="filtro_inicial" id="filtro_inicial" value="1" />
				<!--<input type="hidden" name="filtro_general_usado" id="filtro_inicial" value="no" />-->
				

			  </div>
			  <div class="filtroxcampo" style="width: 90%;">			  	
			  	<span class="btn_buscar" id="filtro_general"><img src="img/lupa.png" title="Buscar">Buscar filtro</span>
				<span style="float: right;"><label class="alertaCritico">Mostrar/Ocultar Nuevos casos</label></span>
			  </div>
		  	</fieldset>
		</form>
		</div>
	</div>

	<div class="listado_clientes">
		
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="clienteCriticos" width="90%">
			<thead>
				<tr>
					<th><input type="checkbox" name="seleccion_general" id="seleccion_general"></th>
					<th>Averias</th>
					<th>Codigo Atencion</th>
					<th>Tipo Actividad</th>
					<th>Fecha Registro</th>
					<th>Quiebre</th>
					<th>M1</th>
					<th>Empresa</th>
					<th>Telefono</th>
					<th>Fecha Agenda</th>
					<th>Estado</th>
					<th>Tecnico</th>
					<th>MDF</th>
					<th title="Microzona">Mcz.</th>
					<th>Distrito</th>
					<th title="Fecha Cambio">Fec.Cambio</th>
					<th title="Hora Cambio">Hora Cambio</th>
					<th>Aver.</th>
					<th>Mov.</th>
					<th>Gestion</th>
					<th>Trans<br>Info</th>
					<th style="display:none">Tipo_Averia</th>
					<th style="display:none">Horas_Averia</th>
					<th style="display:none">Fecha_Reporte</th>
					<!--<th style="display:none">Fecha_Registro</th>-->
					<th style="display:none">Ciudad</th>
					<th style="display:none">Averia</th>
					<th style="display:none">Inscripcion</th>
					<th style="display:none">Fono1</th>
					<th style="display:none">Telefono</th>
					<th style="display:none">MDF</th>
					<th style="display:none">Observacion_102</th>
					<th style="display:none">Segmento</th>
					<th style="display:none">Area_</th>
					<th style="display:none">Direccion_Instalacion</th>
					<th style="display:none">Codigo_Distrito</th>
					<th style="display:none">Nombre_Cliente</th>
					<th style="display:none">Orden_Trabajo</th>
					<th style="display:none">Veloc_Adsl</th>
					<th style="display:none">Clase_Servicio_Catv</th>
					<th style="display:none">Codmotivo_req_catv</th>
					<th style="display:none">Total_Averias_Cable</th>
					<th style="display:none">Total_Averias_Cobre</th>
					<th style="display:none">Total_Averias</th>
					<th style="display:none">fftt</th>
					<th style="display:none">llave</th>
					<th style="display:none">dir_terminal</th>
					<th style="display:none">Fonos_Contacto</th>
					<th style="display:none">Contrata</th>
					<th style="display:none">Zonal</th>
					<th style="display:none">Quiebre</th>
					<th style="display:none">Lejano</th>
					<th style="display:none">Distrito</th>
					<th style="display:none">eecc_zona</th>
					<th style="display:none">Zona_Movistar_Uno</th>
					<th style="display:none">Paquete</th>
					<th style="display:none">Data_Multiproducto</th>
					<th style="display:none">Averia_m1</th>
					<th style="display:none">Fecha_Data_Fuente</th>
					<th style="display:none">Telefono_Codclientecms</th>
					<th style="display:none">Rango_Dias</th>
					<th style="display:none">Sms1</th>
					<th style="display:none">Sms2</th>
					<th style="display:none">Area2</th>
					<th style="display:none">Microzona</th>
					<th style="display:none">Tecnico</th>
				</tr>
			</thead>
			<tbody>
			<?php
			/*$c=0;

			foreach($cliente as $data):

				if($data["fecha_agenda"]!=''){

					$fecha = substr($data["fecha_agenda"], 8,2)."-".substr($data["fecha_agenda"], 5,2)."-".substr($data["fecha_agenda"], 0,4);
				}else{
					$fecha = "";
				}

				if($data["fecha_reg"]){
					$fecha_reg = substr($data["fecha_reg"], 8,2)."-".substr($data["fecha_reg"], 5,2)."-".substr($data["fecha_reg"], 0,4)." ";
					$fecha_reg .= substr($data["fecha_reg"],11);
				}

				if($data["fecha_cambio"]){
					$fecha_cambio = substr($data["fecha_cambio"], 8,2)."-".substr($data["fecha_cambio"], 5,2)."-".substr($data["fecha_cambio"], 0,4)." ";
					$fecha_cambio .= substr($data["fecha_cambio"],11);
				}
			?>
				<tr class="odd gradeX" data-indice="<?php echo trim($c);?>">
					<td><input type="checkbox" name="seleccion_criticos" id="seleccion_criticos<?php echo trim($c);?>" 
					value="si" data-id="<?php echo trim($data["id"]);?>" data-atc="<?php echo trim($data["id_atc"]);?>"
					data-estado="<?php echo trim($data["codigo_estado"]);?>" data-tecnico="<?php echo trim($data["tecnico"]);?>"
					data-empresa="<?php echo trim($data["eecc_final"]);?>" data-empresa-raiz="<?php echo trim($data_emp["empresa"]);?>"></td>
					<td class="faveria_ini<?php echo trim($c);?>"><?php echo trim($data["averia"]);?></td>
					<td><?php echo trim($data["id_atc"]);?></td>
					<td class="factividad<?php echo trim($c);?>"><?php echo trim($data["tipo_actividad"]);?></td>
					<td><span style="display: none;"><?php echo trim($data["fecha_reg"]);?></span><?php echo trim($fecha_reg);?></td>
					<td><?php echo trim($data["quiebres"]);?></td>
					<td class="fempresa<?php echo trim($data["id"]);?>"><?php echo $data["empresa"];?></td>
					<td class="ftele<?php 
					if(trim($data["id"])==""){
						echo trim($c);
					}else{
						echo trim($data["id"]);
					}
					?>">
					<?php echo $data["telefono_cliente_critico"];?>
					</td>
					<td class="fagenda<?php echo trim($data["id"]);?>">
					<?php echo (trim($data["codigo_estado"])=="1" || trim($data["codigo_estado"])=="9" || trim($data["codigo_estado"])=="10" || trim($data["codigo_estado"])=="20")? $fecha."<br>".trim($data["horario"]):'';?>
					</td>
					<td class="festado<?php echo trim($data["id"]);?>"><?php echo trim($data["estado"]);?></td>
					<!--<td class="ftecnico<?php echo trim($data["id"]);?>"><?php echo htmlspecialchars(trim($data["flag_tecnico"]));?></td>-->
					<td class="ftecnico<?php echo trim($data["id"]);?>"><?php echo htmlspecialchars(trim($data["tecnico"]));?></td>
					<td><?php echo htmlspecialchars(trim($data["mdf"]));?></td>
					<td><?php echo htmlspecialchars(trim($data["microzona"]));?></td>
					<td><?php echo htmlspecialchars(trim($data["distrito"])); //?></td>
					<td><?php echo trim($fecha_cambio);?></td>
					<td><?php echo trim($data["horas_cambio"]);?></td>
			<?php
					if($data["estado"]!="Temporal"):
			?>
						<td class="mostrar_averia" data-id="<?php echo trim($data["id"]);?>" data-actividad="<?php echo trim($data["tipo_actividad"]);?>"><img src="img/averia.png" alt="Mostrar Avería" title="Mostrar Avería" />
			<?php
						if($data["wu_nagendas"]!="0"):
			?>
						<br><img src="img/agendamiento_wu.gif" alt="Agendamiento WU" title="Agendamiento WU" />(<?php echo $data['wu_nagendas']; ?>)
			<?php
						endif;
			?>
						</td>
			<?php
					else:
			?>	
						<td class="mostrar_averia_raiz" data-averia="<?php echo trim($data["averia"]);?>" data-actividad="<?php echo trim($data["tipo_actividad"]);?>"><img src="img/averia.png" alt="Mostrar Avería" title="Mostrar Avería" />
			<?php
						if($data["wu_nagendas"]!="0"):
			?>
						<br><img src="img/agendamiento_wu.gif" alt="Agendamiento WU" title="Agendamiento WU" />(<?php echo $data['wu_nagendas']; ?>)
			<?php
						endif;
			?>
						</td>
			<?php
					endif;
			?>

			<?php
				if($data["estado"]!="Temporal" && $data["tipo_actividad"]!="Manual"):
					if($data["existe"]!=""):
						$existe = '';
					else:
						$existe = '<img src="img/info_2.png" alt="No esta Pendiente" title="No esta Pendiente" />';
					endif;
			?>
					<td class="mostrar_mov" data-id="<?php echo trim($data["id"]);?>"><?php echo $existe;?><img src="img/mov.jpg" alt="Mostrar Movimientos" title="Mostrar Movimientos" />&nbsp;<span class="nmov<?php echo trim($data["id"]);?>">(<?php echo trim($data["nmov"]);?>)</span></td>
			<?php
				elseif($data["estado"]!="Temporal" && $data["tipo_actividad"]=="Manual"):
			?>
					<td class="mostrar_mov" data-id="<?php echo trim($data["id"]);?>"><img src="img/mov.jpg" alt="Mostrar Movimientos" title="Mostrar Movimientos" />&nbsp;<span class="nmov<?php echo trim($data["id"]);?>">(<?php echo trim($data["nmov"]);?>)</span></td>
			<?php
				else:
			?>	
					<td data-id="<?php echo trim($data["id"]);?>"></td>
			<?php
				endif;
			?>

			<?php
			$n_evento='';
				if($data["n_evento"]=="1"):
			
			$n_evento='<img src="img/cel1.png" alt="Transmision de informacion" title="Transmision de informacion" />';
			
				endif;
			?>

			<?php
				if(trim($data["estado"])!="Temporal" && trim($data["codigo_estado"])!="21"):
			?>
                                        <td class="gestion quitar_gestion<?php echo trim($data["id"]);?>" data-id="<?php echo trim($data["id"]);?>" data-indice="<?php echo $c;?>" data-actividad="<?php echo trim($data["tipo_actividad"]);?>">
                                            <span class="flag_tecnico<?php echo trim($data["id"]);?>">
                                            <?php
                                            if ( trim($data["flag_tecnico"])=="Tecnico Asignado" ) {
                                                echo "<img src=\"img/user-yellow.png\" alt=\"Tecnico Asignado\" title=\"Tecnico Asignado\" />";
                                            } elseif ( trim($data["flag_tecnico"])=="Tecnico Entregado" ) {
                                                echo "<img src=\"img/user-green.png\" alt=\"Tecnico Entregado\" title=\"Tecnico Entregado\" />";
                                            } else {
                                                echo "<img src=\"img/user-clean.png\" alt=\"Sin tecnico\" title=\"Sin tecnico\" />";
                                            }
                                            ?>
                                            </span>
                                            <img src="img/gestionar.png" alt="Gestionar" title="Gestionar" /></td>
			<?php
				elseif(trim($data["estado"])!="Temporal" && trim($data["codigo_estado"])=="21"):
			?>
					<td></td>
			<?php
				elseif(trim($data["estado"])=="Temporal" && trim($data["codigo_estado"])!="21"):
			?>	
					<td class="registro_criticos quitar_gestion<?php echo trim($data["id"]);?>" data-id="<?php echo trim($data["id"]);?>" data-indice="<?php echo trim($c);?>" data-telefono="<?php echo trim($data["telefono_codclientecms"]);?>" data-actividad="<?php echo trim($data["tipo_actividad"]);?>"><img src="img/gestionar.png" alt="Gestionar" title="Gestionar" /></td>
			<?php
				endif;
			?>	

					<td class="transmision<?php echo trim($data["id"]);?>" data-id="<?php echo trim($data["id"]);?>" data-actividad="<?php echo trim($data["tipo_actividad"]);?>"><?php echo $n_evento; ?></td>
					
					<!--De aqui comienza los datos de averias-->
					<td style="display:none"><?php echo trim($data["tipo_averia"]);?></td>
					<td style="display:none"><?php echo trim($data["horas_averia"]);?></td>
					<td style="display:none"><?php echo trim($data["fecha_registro"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["ciudad"]));?></td>
					<td style="display:none"><?php echo trim($data["codigo_averia"]);?></td>
					<td style="display:none"><?php echo trim($data["inscripcion"]);?></td>
					<td style="display:none"><?php echo trim($data["fono1"]);?></td>
					<td style="display:none"><?php echo trim($data["telefono"]);?></td>
					<td style="display:none"><?php echo trim($data["mdf"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["observacion_102"]));?></td>
					<!--<td style="display:none">&nbsp;</td>-->
					<td style="display:none"><?php echo trim($data["segmento"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["area_"]));?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["direccion_instalacion"]));?></td>
					<td style="display:none"><?php echo trim($data["codigo_distrito"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["nombre_cliente"]));?></td>
					<td style="display:none"><?php echo trim($data["orden_trabajo"]);?></td>
					<td style="display:none"><?php echo trim($data["veloc_adsl"]);?></td>
					<td style="display:none"><?php echo trim($data["clase_servicio_catv"]);?></td>
					<td style="display:none"><?php echo trim($data["codmotivo_req_catv"]);?></td>
					<td style="display:none"><?php echo trim($data["total_averias_cable"]);?></td>
					<td style="display:none"><?php echo trim($data["total_averias_cobre"]);?></td>
					<td style="display:none"><?php echo trim($data["total_averias"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["fftt"]));?></td>
					<td style="display:none"><?php echo trim($data["llave"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["dir_terminal"]));?></td>
					<td style="display:none"><?php echo trim($data["fonos_contacto"]);?></td>
					<td style="display:none"><?php echo trim($data["contrata"]);?></td>
					<td style="display:none"><?php echo trim($data["zonal"]);?></td>
					<td style="display:none"><?php echo trim($data["quiebre"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["lejano"]));?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["distrito"]));?></td>
					<td style="display:none"><?php echo trim($data["eecc_zona"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["zona_movistar_uno"]));?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["paquete"]));?></td>
					<td style="display:none"><?php echo trim($data["data_multiproducto"]);?></td>
					<td style="display:none"><?php echo trim($data["averia_m1"]);?></td>
					<td style="display:none"><?php echo trim($data["fecha_data_fuente"]);?></td>
					<td style="display:none"><?php echo trim($data["telefono_codclientecms"]);?></td>
					<td style="display:none"><?php echo trim($data["rango_dias"]);?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["sms1"]));?></td>
					<td style="display:none"><?php echo trim(htmlspecialchars($data["sms2"]));?></td>
					<td style="display:none"><?php echo trim($data["area2"]);?></td>
					<td style="display:none"><?php echo trim($data["microzona"]);?></td>
					<td style="display:none"><?php echo trim($data["tecnico"]);?></td>
				</tr>
			<?php
			$c++;
			endforeach; */
			?>
			</tbody>
		</table>
	</div>
</div>

<div id="alertDiv" style="display: none">
    <h4 style="text-align: center">Nuevos casos cr&iacute;ticos</h4>
    <div style="display: table">
		<div style="display: table-row">
            <div style="display: table-cell">TIPO</div>
			<div style="display: table-cell">TOTAL</div>
			<div style="display: table-cell">&nbsp;&nbsp;Fecha</div>
            <div style="display: table-cell">
                &nbsp;&nbsp;AGEND
            </div>
            <div style="display: table-cell">
                &nbsp;&nbsp;F_PL
            </div>
            <div style="display: table-cell">
                &nbsp;&nbsp;LLAMADAS
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;REIT
            </div>
        </div>
        <div style="display: table-row">
            <div style="display: table-cell">Provisi&oacute;n</div>
            <div style="display: table-cell">
                &nbsp;&nbsp;
                <a href="" class="nuevoProv">
                    <span id="numProvCtc">0</span>
                </a>
            </div>
            <div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="dateProvCtc">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="agend_prov">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="fpl_prov">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="llamadas_prov">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="reit_prov">--</span>
            </div>
            <form name="fNuevoProv" id="fNuevoProv" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
                <input type="hidden" name="hNumProvCtc" id="hNumProvCtc" value="" />
            </form>
        </div>
        <div style="display: table-row">
            <div style="display: table-cell">Aver&iacute;as</div>
            <div style="display: table-cell">
                &nbsp;&nbsp;
                <a href="" class="nuevoAver">
                    <span id="numAverCtc">0</span>
                </a>
            </div>
            <div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="dateAverCtc">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="agend_aver">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="fpl_aver">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="llamadas_aver">--</span>
            </div>
			<div style="display: table-cell">
                &nbsp;&nbsp;
                <span id="reit_aver">--</span>
            </div>
            <form name="fNuevoAver" id="fNuevoAver" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
                <input type="hidden" name="hNumAverCtc" id="hNumAverCtc" value="" />
            </form>
        </div>
    </div>
</div>
<style>
    #alertDiv {
        position   : fixed;
        left       : 40%;
        right      : 10%;
        bottom     : 0px;
        overflow   : auto;
        background-color: #ffffff;
        width: 450px;
        border-style: solid;
        border-color: #000000;
        border-width: thin;
        padding: 5px;
    }
    #alertDiv p{
        font-size: 12px;
    }
</style>
<script>
/**
 * Verifica nuevos casos criticos
 * Tiempo en milisegundos(ms), ejemplo 5000 ms = 5 seg.
 * @returns {Boolean}
 */
function checkNewLauncher(){
    var ms = 60000;    
    setInterval(function() {
        getCasosNuevos();
    }, ms);
    return true;
}

/**
 * Envía petición Ajax consultando por 
 * nuevos casos criticos
 * @returns {Boolean}
 */
function getCasosNuevos(){
    var data_content = "action=checkNewCtc";
    $.ajax({
        type: "POST",
        url: "casos_nuevos.php",
        data: data_content,
        dataType: 'json',
        success: function(datos) {
            var showAlert = false;
            $.each(datos, function (id, val){
                if (id==="provision") {
                    if ($("#numProvCtc").html() != val.cantidad) {
                        showAlert = true;
                        $("#numProvCtc").html( val.cantidad );
                        $("#hNumProvCtc").val( val.cantidad );
                    }
                    if ($("#dateProvCtc").html() != val.fecreg) {
                        showAlert = true;
                        $("#dateProvCtc").html( val.fecreg );
                    }
                }
                if (id==="averias") {
                    if ($("#numAverCtc").html() != val.cantidad) {
                        showAlert = true;
                        $("#numAverCtc").html( val.cantidad );
                        $("#hNumAverCtc").val( val.cantidad );
                    }
                    if ($("#dateAverCtc").html() != val.fecreg) {
                        showAlert = true;
                        $("#dateAverCtc").html( val.fecreg );
                    }
                }
            });
			
			$.each(datos.averias.quiebre, function (){

				$("#agend_aver").html(datos.averias.quiebre.total[0])
				$("#fpl_aver").html(datos.averias.quiebre.total[1])
				$("#llamadas_aver").html(datos.averias.quiebre.total[2])
				$("#reit_aver").html(datos.averias.quiebre.total[3])
	
			});
			
			$.each(datos.provision.quiebre, function (){

				$("#agend_prov").html(datos.provision.quiebre.total[0])
				$("#fpl_prov").html(datos.provision.quiebre.total[1])
				$("#llamadas_prov").html(datos.provision.quiebre.total[2])
				$("#reit_prov").html(datos.provision.quiebre.total[3])
	
			});
			
            //showAlert == true -> mostrar alerta
            if ( showAlert ) {
                
                $( "#alertDiv" ).slideUp( "fast", function() {
                    // Animation complete.
                });
                $( "#alertDiv" ).slideDown( "slow", function() {
                    // Animation complete.
                });
            }
        }
    });
    return true;
}

$(document).ready(function (){
    $(".alertaCritico").click(function (event){
        event.preventDefault();
        $( "#alertDiv" ).slideToggle( "slow", function() {
            // Animation complete.
        });
    });
    
    $(".nuevoProv").click(function (event){
        event.preventDefault();
        $("#fNuevoProv").submit();
    });
    $(".nuevoAver").click(function (event){
        event.preventDefault();
        $("#fNuevoAver").submit();
    });
    
    /**
     * Nuevos casos criticos
     */
    getCasosNuevos();
    checkNewLauncher();
    $("#filtro_general").click();
});
</script>

<div id="dialog-criticos" title="Registro de clientes criticos"></div>
<div id="dialog-gestion-criticos" title="Gesti&oacute;n Clientes Cr&iacute;ticos"></div>
<div id="dialog-gestion-averias" title="Gesti&oacute;n Averias"></div>
<div id="dialog-gestion-movimientos" title="Gesti&oacute;n Movimientos"></div>
<div id="dialog-asignar-empresa" title="Asignar Empresa"></div>
<div id="dialog-asignar-tecnico" title="Asignar T&eacute;cnico"></div>
<div id="dialog-asignar-pendiente" title="Asignar Pendiente"></div>
<div id="dialog-registro-manual" title="Registro Manual de Pendiente"></div>
<div id="dialog-visor-gps" title="Visor / GPS"></div>
<div id="dialog-horarios" title="Gestion Horarios"></div>
<div id="dialog-pendientes" title="Gestion Pendientes"></div>
<div id="dialog-transmision" title="Transmisi&oacute;n de Informaci&oacute;n"></div>
<!--<div id="dialog-excel" title="Generar Excel"></div>-->
<iframe id="dialog-excel" src="" width="100" height="300" style="display:none"></iframe>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>