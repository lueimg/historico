<?php
$PATH =  $_SERVER['DOCUMENT_ROOT']."/webpsi/";
require_once ($PATH."clases/class.Conexion.php");
require_once($PATH.'modulos/historico/clases/empresa.php');
require_once($PATH.'modulos/historico/clases/gestionCriticos.php');
session_start();

$fecha=date("d/m/Y");
$hora = date("h:i:s");
$hora = substr($hora,0,2)."_".substr($hora,3,2)."_".substr($hora,6,2);
$filename = $fecha."-".$hora;
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_bandeja_criticos_$filename.xls");

?>

<html>
<head>
<title>Excel</title>
</head>
<body>

<?php
$db = new Conexion();
$cnx = $db->conectarPDO();

$nuevoCritico = "";
$filtro_inicial = $_POST["filtro_inicial"];
$ob_critico = new gestionCriticos();

//El cuarto parametro es para la configuracion inicial en el where de busqueda
$contrata = "";
if($filtro_inicial=="1"){
	
	$contrata = $_POST["empresa"];
	$tot_empresas = sizeof($contrata);
	if($tot_empresas>1){
		$contrata = implode("','",$contrata);
		$contrata = "'".$contrata."'";
	}else{
		$contrata = "'".$contrata."'";
	}
	$data_emp = array("empresa"=>$contrata);
	$cliente = $ob_critico->getGestionCriticosFiltro($cnx,$data_emp,1,1,$nuevoCritico);
}else{
	$actividad = $_POST["actividades"];
	if($actividad!=""){
		$actividad = implode("','",$actividad);
		$actividad = "'".$actividad."'";
	}else{
		$actividad = "";
	}

	if($actividad !=""){
			//si en actividad existe alta o rutina lo separamos para el campo
			//tipo actuacion
			$alta = strpos($actividad, "ALTA");
			$rutina = strpos($actividad, "RUTINA");
			$manual = strpos($actividad, "MANUAL");
			$avers = strpos($actividad, "Averias");
			$provs = strpos($actividad, "Provision");
			$comodin_averias = ($avers!==false)? "'',":"";//para que filtre al tipo_actividad vacio

			$pos = "";
			if(($alta!==false && $rutina!==false && $manual!==false) || ($alta!==false && $rutina===false && $manual===false)){
				//si los dos estan solo uso altas ya q es el primer item
				
				$pos = $alta - 1;
				$actuacion = substr($actividad,$pos,strlen($actividad));
				$actuacion = $actuacion.",'TRASLADO'";
				if($filtro_sql!=""){
					$filtro_sql .= " and actuacion in(".$comodin_averias.$actuacion.")";
				}else{
					$filtro_sql .= "where actuacion in(".$comodin_averias.$actuacion.")";
				}
			}else if($alta===false && $rutina!==false && $manual===false){
				$pos = $rutina - 1;
				$actuacion = substr($actividad,$pos,strlen($actividad));
				if($filtro_sql!=""){
					$filtro_sql .= " and actuacion in(".$comodin_averias.$actuacion.")";
				}else{
					$filtro_sql .= "where actuacion in(".$comodin_averias.$actuacion.")";
				}
			}else if($alta===false && $rutina===false && $manual!==false){
				$pos = $manual - 1;
				$actuacion = substr($actividad,$pos,strlen($actividad));
				if($filtro_sql!=""){
					$filtro_sql .= " and actuacion in(".$comodin_averias.$actuacion.")";
				}else{
					$filtro_sql .= "where actuacion in(".$comodin_averias.$actuacion.")";
				}
			}

			if($pos!=""){
				//para obiar la anterior coma
				$pos = $pos -1;
				$tipo_actividad = substr($actividad,0,$pos);

				//si no esta marcado Provision que para el tipo de actividad la agrego
				$comodin_prov = ($provs===false)? "'Provision',":"";//para que filtre al tipo_actividad Provision sin ser marcado
			}else{
				$tipo_actividad = $actividad;
				if($tipo_actividad==""){
					$tipo_actividad = "'Provision'";
				}else{
					$comodin_prov = ($provs===false)? "'Provision',":"";
				}
			}

			if($actividad=="'Averias'" || $actividad=="'Manual'" || $actividad=="'Averias','Manual'"){//si solo marcan averias
				$comodin_prov = "";
			}

			if($filtro_sql!=""){
				$filtro_sql .= " and tipo_actividad in(".$comodin_prov.$tipo_actividad.")";
			}else{
				$filtro_sql .= "where tipo_actividad in(".$comodin_prov.$tipo_actividad.")";
			}	
			
		}

		$empresa = $_POST["empresa"];
		if($empresa!=""){
			$empresa = implode("','",$empresa);
			$empresa = "'".$empresa."'";
		}else{
			$empresa = "";
		}

		$zonales = $_POST["zonales"];
		if($zonales!=""){
			$zonales = implode("','",$zonales);
			$zonales = "'".$zonales."'";
		}else{
			$zonales = "";
		}

		$negocio = $_POST["negocio"];
		if($negocio!=""){
			$negocio = implode("','",$negocio);
			$negocio = "'".$negocio."'";
		}else{
			$negocio = "";
		}

		$microzona = $_POST["microzona"];
		if($microzona!=""){
			$microzona = implode("','",$microzona);
			$microzona = "'".$microzona."'";
		}else{
			$microzona = "";
		}

		$area2 = $_POST["area2"];
		if($area2!=""){
			$area2 = implode("','",$area2);
			$area2 = "'".$area2."'";
		}else{
			$area2 = "";
		}

		$lejano = $_POST["lejano"];
		if($lejano!=""){
			$lejano = implode("','",$lejano);
			$lejano = "'".$lejano."'";
		}else{
			$lejano = "";
		}

		$mdf = $_POST["mdf"];
		if($mdf!=""){
			$mdf = implode("','",$mdf);
			$mdf = "'".$mdf."'";
		}else{
			$mdf = "";
		}

		$nodo = $_POST["nodo"];
		if($nodo!=""){
			$nodo = implode("','",$nodo);
			$nodo = "'".$nodo."'";
		}else{
			$nodo = "";
		}

		$estados = $_POST["estados"];
		$tot_estados = sizeof($_POST["estados"]);
		if($tot_estados>0){
			for($i=0;$i<$tot_estados;$i++){
				if($estados[$i] == "Temporal"){
					$estados[$i] = "'".$estados[$i]."'";
				}
			}
		}
		if($estados!=""){
			$estados = implode(",",$estados);
		}else{
			$estados = "";
		}

		$flags = $_POST["filtro_tecnico"];
		if($flags!=""){
			$flags = implode("','",$flags);
			$flags = "'".$flags."'";
		}else{
			$flags = "";
		}

		$quiebre = $_POST["quiebre"];
		if($quiebre!=""){
			$quiebre = implode("','",$quiebre);
			$quiebre = "'".$quiebre."'";
		}else{
			$quiebre = "";
		}

		$area_ = $_POST["area_"];
		if($area_!=""){
			$area_ = implode("','",$area_);
			$area_ = "'".$area_."'";
		}else{
			$area_ = "";
		}

		$tecnico = $_POST["tecnico_nombre"];
		if($tecnico!=""){
			$tecnico = implode("','",$tecnico);
			$tecnico = "'".$tecnico."'";
		}else{
			$tecnico = "";
		}

		$movistar1 = $_POST["movistar1"];
		if($movistar1!=""){
			$movistar1 = implode("','",$movistar1);
			$movistar1 = "'".$movistar1."'";
		}else{
			$movistar1 = "";
		}

		$distrito = $_POST["distrito"];
		if($distrito!=""){
			$distrito = implode(",",$distrito);			
		}else{
			$distrito = "";
		}

	if($_POST["fecha_ini"]!="" && $_POST["fecha_fin"]!=""){
			$fecha_ini = trim($_POST["fecha_ini"]);
			$fecha_ini = substr($fecha_ini, 6,4)."-".substr($fecha_ini, 3,2)."-".substr($fecha_ini, 0,2);
			$fecha_fin = trim($_POST["fecha_fin"]);
			$fecha_fin = substr($fecha_fin, 6,4)."-".substr($fecha_fin, 3,2)."-".substr($fecha_fin, 0,2);
	}else{
		$fecha_ini = "";
		$fecha_fin = "";
	}
	
	$data = array("actividad"=>$actividad,"empresa"=>$empresa,"zonal"=>$zonales,"negocio"=>$negocio,"microzona"=>$microzona,"area2"=>$area2,"lejano"=>$lejano,
		"mdf"=>$mdf,"nodo"=>$nodo,"estado"=>$estados,"flags"=>$flags,"quiebre"=>$quiebre,"area_"=>$area_,"tecnico"=>$tecnico,
		"movistar1"=>$movistar1,"fecha_ini"=>$fecha_ini,"fecha_fin"=>$fecha_fin,"distrito"=>$distrito);
	
	$cliente = $ob_critico->getGestionCriticosFiltro($cnx,$data,0,0);
}
/*
$ob_empresa = new Empresa();
$empresa_usuario = $_SESSION["exp_user"]["ideecc"];
if($empresa_usuario=="1"){
	$empresas = $ob_empresa->getEmpresaAll($cnx);
	$data_emp = array("empresa"=>"TDP");
}else{
	$empresas = $ob_empresa->getEmpresaxID($cnx,$empresa_usuario);
	$nombre_empresa = $empresas["nombre"];
	$data_emp = array("empresa"=>"'".$nombre_empresa."'");
}
$ob_cliente = new gestionCriticos();
//print_r($data_emp);
$cliente = $ob_cliente->getGestionCriticosFiltro($cnx,$data_emp,1);*/

$table = "<table><tr><td>Averia</td><td>atc</td><td>Tipo Actividad</td><td>nombre</td><td>Fecha Registro</td><td>quiebres</td><td>empresa</td><td>telefono_codclientecms</td>
				 <td>fecha_agenda</td><td>horario</td><td>estado</td>
				 <td>tecnico</td><td>tipo_averia</td><td>horas_averia</td><td>fecha_registro</td><td>ciudad</td><td>codigo_averia</td><td>inscripcion</td>
				 <td>fono1</td><td>telefono</td><td>MDF</td><td>Microzona</td><td>observacion_102</td><td>segmento</td><td>area_</td><td>direccion_instalacion</td><td>codigo_distrito</td><td>distrito</td>
				 <td>nombre_cliente</td><td>orden_trabajo</td><td>veloc_adsl</td><td>clase_servicio_catv</td><td>codmotivo_req_catv</td><td>total_averias_cable</td><td>total_averias_cobre</td><td>total_averias</td>
				 <td>fftt</td><td>llave</td><td>dir_terminal</td><td>data_multiproducto</td><td>averia_m1</td><td>fecha_data_fuente</td><td>telefono_codclientecms</td><td>rango_dias</td>
				 <td>sms1</td><td>sms2</td><td>area2</td><td>total_llamadas_tecnicas</td><td>total_llamadas_seguimiento</td><td>Fecha Creacion ATC</td>
</tr>"; 
		
		foreach($cliente as $row){

			$fecha_agenda = ($row["codigo_estado"]==1 || $row["codigo_estado"]==8 || $row["codigo_estado"]==9
				 || $row["codigo_estado"]==10 || $row["codigo_estado"]==20)? $row["fecha_agenda"]:'';
			$horario = ($row["codigo_estado"]==1 || $row["codigo_estado"]==8 || $row["codigo_estado"]==9
				 || $row["codigo_estado"]==10 || $row["codigo_estado"]==20)? $row["horario"]:'';

            $table .= "<tr><td>".$row["averia"]."</td><td>".$row["id_atc"]."</td><td>".$row["tipo_actividad"]."</td><td>".$row["nombre"]."</td>".
					  "<td>".$row["fecha_reg"]."</td><td>".$row["quiebres"]."</td><td>".$row["empresa"]."</td><td>".$row["telefono_cliente_critico"]."</td>".
				      "<td>".$fecha_agenda."</td><td>".$horario."</td><td>".$row["estado"]."</td>".
					  "<td>".$row["tecnico"]."</td><td>".$row["tipo_averia"]."</td><td>".$row["horas_averia"]."</td>".
					  "<td>".$row["fecha_registro"]."</td><td>".$row["ciudad"]."</td><td>".$row["codigo_averia"]."</td><td>".$row["inscripcion"]."</td>".
					  "<td>".$row["fono1"]."</td><td>".$row["telefono"]."</td><td>".$row["mdf"]."</td><td>".$row["microzona"]."</td><td>".$row["observacion_102"]."</td>".
					  "<td>".$row["segmento"]."</td><td>".$row["area_"]."</td><td>".$row["direccion_instalacion"]."</td><td>".$row["codigo_distrito"]."</td><td>".$row["distrito"]."</td>".
					  "<td>".$row["nombre_cliente"]."</td><td>".$row["orden_trabajo"]."</td><td>".$row["veloc_adsl"]."</td><td>".$row["clase_servicio_catv"]."</td>".
					  "<td>".$row["codmotivo_req_catv"]."</td><td>".$row["total_averias_cable"]."</td><td>".$row["total_averias_cobre"]."</td><td>".$row["total_averias"]."</td>".
					  "<td>".$row["fftt"]."</td><td>".$row["llave"]."</td><td>".$row["dir_terminal"]."</td><td>".$row["data_multiproducto"]."</td>".
					  "<td>".$row["averia_m1"]."</td><td>".$row["fecha_data_fuente"]."</td><td>".$row["telefono_codclientecms"]."</td><td>".$row["rango_dias"]."</td>".
					  "<td>".$row["sms1"]."</td><td>".$row["sms2"]."</td><td>".$row["area2"]."</td><td>".$row["total_llamadas_tecnicas"]."</td>".
					  "<td>".$row["total_llamadas_seguimiento"]."</td><td>".$row["fecha_creacion"]."</td>".
					  "</tr>"; 
        }

		$table .= "</table>";
		echo $table;

?>

</body>
</html>
