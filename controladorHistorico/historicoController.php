<?php
$PATH =  $_SERVER['DOCUMENT_ROOT']."/webpsi/";
include_once $PATH."clases/class.Conexion.php";
include_once $PATH."modulos/historico/clases/gestionCriticos.php";
include_once $PATH."modulos/historico/clases/GestionManual.php";
include_once $PATH."modulos/historico/clases/gestionProvision.php";
include_once $PATH."modulos/historico/clases/gestionAveria.php";
include_once $PATH."modulos/historico/clases/submotivos.php";
include_once $PATH."modulos/historico/clases/mdfs.php";
include_once $PATH."modulos/historico/clases/tecnicos.php";
include_once $PATH."modulos/historico/clases/empresa.php";
include_once $PATH."modulos/historico/clases/liquidados.php";
include_once $PATH."modulos/historico/clases/cedula.php";

//La clase estados esta incluida en gestionCriticos.php

 $db = new Conexion();
 $cnx = $db->conectarPDO();

//SI SE HACE UN REGISTRO CRITICO
if(isset($_POST["registro_critico"]) && $_POST["registro_critico"]=="registro_critico"){

	$ob_critico = new gestionCriticos();
    //PROCESO DE VALIDACION Y GUARDODO DE AVERIA CRITICO
	$res = $ob_critico->addClienteCritico($cnx);
    ;
    //SI ADD CLIENTE ES FALSO
    //ENTONCES PASAMOS A ACTUALIZAR
    if(!$res["estado"] && $res["averia"]== $_POST["averia"]) {

        $res = $ob_critico->ReAgendarAveria($cnx);

    }


    echo $res["msg"];


}

if(isset($_POST["registrar_pendientes"]) && $_POST["registrar_pendientes"]=="registrar_pendientes"){

	//Paso los codigos como array para ahorrarme validaciones y código repetido
	$cods = $_POST["codigos"];
	    $pos = strpos($cods, ",");
	    if($pos !== false){
			$codigos = explode(",", $cods);
		}else{
			$codigos = array($cods);
		}

	$tipo_actividad = $_POST["actividad"];
	    $pos = strpos($tipo_actividad, ",");
	    if($pos !== false){
			$actividad = explode(",", $tipo_actividad);
		}else{
			$actividad = array($tipo_actividad);
		}

	$ob_critico = new gestionCriticos();
	$res = $ob_critico->addClienteCriticoPendiente($cnx,$codigos,$actividad);
	if($res["estado"]){
    	echo $res["msg"];
    }else{
    	echo $res["msg"];
    }
}

if(isset($_POST["tipo"]) && $_POST["tipo"]=="submotivo"){

	$id = $_POST["id"];
	if($id==""){
		echo json_encode(array("error"=>"true"));
	}else{
		$ob_sub = new Submotivos();
		$submotivos = $ob_sub->getSubmotivos($cnx,$id);
		echo json_encode($submotivos);
	}
}

//Controlador para cargar los Estados y Técnicos según el Código de Estado
if(isset($_POST["tipo"]) && $_POST["tipo"]=="estado"){

	$id_motivo = $_POST["id_motivo"];
	$id_submotivo = $_POST["id_submotivo"];
	$id_gestion = $_POST["id_gestion"];
	if($id_submotivo==0){
		echo json_encode(array("error"=>"true"));
	}else{
		
		$ob_est = new Estados();
		if($id_motivo==5 && $id_submotivo==7){
			//Este método es por el motivo observaciones que no genera ningún estado solo obtiene el anterior
			$estados = $ob_est->getEstadoxCritico($cnx,$id_gestion);
		}else{
			$estados = $ob_est->getEstado($cnx,$id_motivo,$id_submotivo);
		}

		$tecnicos = "";
		//Cargando Técnicos
		if(($id_motivo==1 && $id_submotivo==1) || ($id_motivo==2 && $id_submotivo==2)){
			
			$id_empresa = $_POST["id_empresa"];
			$ob_tecnico = new Tecnicos();
			$tecnicos = $ob_tecnico->getTecnicoAllxEmpresa($cnx, $id_empresa);
		}
		$combos  = array("estado"=>$estados,"tecnico"=>$tecnicos);
		echo json_encode($combos);
	}
}

if(isset($_POST["tipo"]) && $_POST["tipo"]=="tecnico"){

	$id_motivo = $_POST["id_motivo"];
	$id_submotivo = $_POST["id_submotivo"];
	$id_estado = $_POST["id_estado"];
	$id_empresa = $_POST["id_empresa"];
	if($id_motivo!=1 && $id_submotivo!=1 && $id_estado!=1){
		echo "";
	}else{
		//cargando tecnicos
		$ob_tecnico = new Tecnicos();
		$res = $ob_tecnico->getTecnicoAllxEmpresa($cnx, $id_empresa);
		$tecnicos = '<select name="tecnico" id="tecnico">';
					foreach ($res as $tec):
					$tecnicos .= '<option value="'.$tec["nombre_tecnico"].'">'.$tec["nombre_tecnico"].'</option>';
					endforeach;
		$tecnicos .= '</select>';
		$tecnicos .= '<span class="checkbox"><input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico" style="margin:0 5px 0 5px">Tecnico Entregado</span>';
		echo $tecnicos;
	}
}

if(isset($_POST["gestion_critico"]) && $_POST["gestion_critico"]=="gestion_critico"){

	$id_gestion = $_POST["id_gestion"];
	$ob_critico = new gestionCriticos();
	$res = $ob_critico->addMovimientoCritico($cnx);
	if($res["estado"]==true){
		$res["nmov"] = $ob_critico->getNmovGestionCriticos($cnx,$id_gestion);
		echo json_encode($res);	
	}else{
		echo json_encode($res);
	}
	

}

if(isset($_POST["filtro_mdf"]) && $_POST["filtro_mdf"]=="filtro_mdf"){

	$zonal = $_POST["zonal"];
	$ob_mdfs = new Mdfs();
	$mdfs = $ob_mdfs->getMdfs($cnx,$zonal);
	echo json_encode($mdfs);
}

if(isset($_POST["filtro"]) && $_POST["filtro"]=="filtro_personalizado"){

	$tipo = $_POST["tipo"];
	$empresa_usuario = $_POST["empresa_usuario"];
	$filtro = trim($_POST["valor_filtro"]);

	$ob_critico = new gestionCriticos();
	if($empresa_usuario=="1"){
		$empresa = "TDP";
		$critico = $ob_critico->getGestionCriticosAll($cnx,$tipo,$filtro,0,$empresa);
		echo str_replace("\\u0000","",json_encode($critico));
	}else{
		//$empresa = trim("'".$_POST["empresa"]."'");
		$empresa = trim($_POST["empresa"]);
		$critico = $ob_critico->getGestionCriticosAll($cnx,$tipo,$filtro,0,$empresa);
		echo str_replace("\\u0000","",json_encode($critico));
	}
}

if(isset($_POST["actualizar_empresa"]) && $_POST["actualizar_empresa"]=="actualizar_empresa"){

	$empresa = $_POST["empresa"];
	$tecnico = $_POST["tecnico"];
	$idtecnico = $_POST["idtecnico"];
	$codigo = $_POST["codigo"];
	$codigo = explode(",", $codigo);
	$actividad = $_POST["actividad"];
	$actividad = explode(",", $actividad);
	$tot_codigos = sizeof($codigo);
	
	for($i=0;$i<$tot_codigos;$i++){
		if($actividad[$i]=="Averias"){
			$ob_averia = new gestionAveria();
			$res = $ob_averia->updateEmpresa($cnx,$empresa,$tecnico,$codigo[$i],$idtecnico);

			if(!$res["estado"]){
				echo json_encode($res);	
				exit();
			}
		}else if($actividad[$i]=="Provision"){
			$ob_provision = new gestionProvision();
			$res = $ob_provision->updateEmpresa($cnx,$empresa,$tecnico,$codigo[$i],$idtecnico);
			if(!$res["estado"]){
				echo json_encode($res);	
				exit();
			}
		}else if($actividad[$i]=="Manual"){
			$ob_manual = new GestionManual();
			$res = $ob_manual->updateEmpresa($cnx,$empresa,$tecnico,$codigo[$i],$idtecnico);
			if(!$res["estado"]){
				echo json_encode($res);	
				exit();
			}
		}else if($actividad[$i]=="Manual_Provision"){
			$ob_manual = new GestionManual();
			$res = $ob_manual->updateEmpresaProvision($cnx,$empresa,$tecnico,$codigo[$i],$idtecnico);
			if(!$res["estado"]){
				echo json_encode($res);	
				exit();
			}
		}
	}

	echo json_encode($res);
}

if(isset($_POST["actualizar_tecnico"]) && $_POST["actualizar_tecnico"]=="actualizar_tecnico"){

	$codigos = $_POST["codigo"];
	$tecnico = $_POST["tecnico"];
	$idtecnico = $_POST["idtecnico"];
	$estado = $_POST["estado"];
	$flag_tecnico = $_POST["flag_tecnico"];
	$ob_critico = new gestionCriticos();
	$res = $ob_critico->updateEstadoFlagCritico($cnx,$codigos,$tecnico,$estado,$flag_tecnico,$idtecnico);
	echo json_encode($res);
}

if(isset($_POST["filtro"]) && $_POST["filtro"]=="filtro_general"){

	$actividad = trim($_POST["actividad"]);
	$empresa = trim($_POST["empresa"]);
	$zonal = trim($_POST["zonal"]);
	$microzona = trim($_POST["microzona"]);
	$area2 = trim($_POST["area2"]);
	$tecnico = trim($_POST["tecnico"]);
	$lejano = trim($_POST["lejano"]);
	$negocio = trim($_POST["negocio"]);
	$mdf = trim($_POST["mdf"]);
	$nodo = trim($_POST["nodo"]);
	$estado = trim($_POST["estado"]);
	$flags = trim($_POST["flags"]);
	$quiebre = trim($_POST["quiebre"]);
	$area_ = trim($_POST["area_"]);
	$movistar1 = trim($_POST["movistar1"]);
	$distrito = trim($_POST["distrito"]);
	$esttransmision = trim($_POST['esttransmision']);
	
	if($_POST["fecha_ini"]!="" && $_POST["fecha_fin"]!=""){
		$fecha_ini = trim($_POST["fecha_ini"]);
		$fecha_ini = substr($fecha_ini, 6,4)."-".substr($fecha_ini, 3,2)."-".substr($fecha_ini, 0,2);
		$fecha_fin = trim($_POST["fecha_fin"]);
		$fecha_fin = substr($fecha_fin, 6,4)."-".substr($fecha_fin, 3,2)."-".substr($fecha_fin, 0,2);
	}else{
		$fecha_ini = "";
		$fecha_fin = "";
	}
	$data = array("actividad"=>$actividad,"empresa"=>$empresa,"zonal"=>$zonal,"microzona"=>$microzona,"area2"=>$area2,"tecnico"=>$tecnico,"lejano"=>$lejano,
		"negocio"=>$negocio,"mdf"=>$mdf,"nodo"=>$nodo,"estado"=>$estado,"flags"=>$flags,"quiebre"=>$quiebre,"area_"=>$area_,
		"movistar1"=>$movistar1,"fecha_ini"=>$fecha_ini,"fecha_fin"=>$fecha_fin,"distrito"=>$distrito,"esttransmision"=>$esttransmision);
	
	$ob_critico = new gestionCriticos();
	//El cuarto parametro es para la configuracion inicial en el where de busqueda
	$critico = $ob_critico->getGestionCriticosFiltro($cnx,$data,0,0);
	echo str_replace("\\u0000","",json_encode($critico));
}

if(isset($_POST["excel"]) && $_POST["excel"]=="generar_excel"){

	$empresa = trim($_POST["empresa"]);
	$zonal = trim($_POST["zonal"]);
	$microzona = trim($_POST["microzona"]);
	$area2 = trim($_POST["area2"]);
	$tecnico = trim($_POST["tecnico"]);
	$lejano = trim($_POST["lejano"]);
	$negocio = trim($_POST["negocio"]);
	$mdf = trim($_POST["mdf"]);
	$nodo = trim($_POST["nodo"]);
	$estado = trim($_POST["estado"]);
	$flags = trim($_POST["flags"]);
	$quiebre = trim($_POST["quiebre"]);
	
	if($_POST["fecha_ini"]!="" && $_POST["fecha_fin"]!=""){
		$fecha_ini = trim($_POST["fecha_ini"]);
		$fecha_ini = substr($fecha_ini, 6,4)."-".substr($fecha_ini, 3,2)."-".substr($fecha_ini, 0,2);
		$fecha_fin = trim($_POST["fecha_fin"]);
		$fecha_fin = substr($fecha_fin, 6,4)."-".substr($fecha_fin, 3,2)."-".substr($fecha_fin, 0,2);
	}else{
		$fecha_ini = "";
		$fecha_fin = "";
	}
	$data = array("empresa"=>$empresa,"zonal"=>$zonal,"microzona"=>$microzona,"area2"=>$area2,"tecnico"=>$tecnico,"lejano"=>$lejano,
		"negocio"=>$negocio,"mdf"=>$mdf,"nodo"=>$nodo,"estado"=>$estado,"flags"=>$flags,"quiebre"=>$quiebre,"fecha_ini"=>$fecha_ini,"fecha_fin"=>$fecha_fin);
	
	$ob_critico = new gestionCriticos();
	//El cuarto parametro es para la configuracion inicial en el where de busqueda
	$critico = $ob_critico->getGestionCriticosFiltro($cnx,$data,0,0);
	echo str_replace("\\u0000","",json_encode($critico));
}

if(isset($_POST["tipo"]) && $_POST["tipo"]=="cargar_tecnico"){

	$id_empresa = $_POST["empresa"];
	if($id_empresa!=""){
		//cargando tecnicos
		$ob_tecnico = new Tecnicos();
		$res = $ob_tecnico->getTecnicoAllxEmpresa($cnx, $id_empresa);
		$tecnicos = '';
					foreach ($res as $tec):
					$tecnicos .= '<option value="'.$tec["nombre_tecnico"].'">'.$tec["nombre_tecnico"].'</option>';
					endforeach;
		echo $tecnicos;
	}
}

if(isset($_POST['cargarTecnico']) && $_POST["cargarTecnico"]=="cargarTecnico"){
	$ob_tecnico = new Tecnicos();
	//$tecnicos = $ob_tecnico->getTecnicoAllxEmpresaxQuiebre($cnx, $_POST['idempresa'],$_POST["quiebre"],$_POST['cedula']);
	$tecnicos = $ob_tecnico->getTecnicoAllxCedula($cnx,$_POST['cedula']);

	echo json_encode($tecnicos);
}

if(isset($_POST['cargarCedula']) && $_POST["cargarCedula"]=="cargarCedula"){
	
$ob_cedula = new Cedula();
$cedula = $ob_cedula->getCedulaAll($cnx,$_POST['idempresa']);

	echo json_encode($cedula);
}

?>