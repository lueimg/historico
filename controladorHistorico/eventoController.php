<?php
$PATH =  $_SERVER['DOCUMENT_ROOT']."/webpsi/";
include_once $PATH."clases/class.Conexion.php";
include_once $PATH."modulos/historico/clases/gestionCriticos.php";
include_once $PATH."modulos/historico/clases/tecnicos.php";
include_once $PATH."modulos/historico/clases/estados.php";
include_once $PATH."modulos/historico/clases/averias.php";
require_once($PATH.'modulos/historico/clases/gestionAveria.php');
require_once($PATH.'modulos/historico/clases/gestionProvision.php');
require_once($PATH.'modulos/historico/clases/gestionManual.php');

 $db = new Conexion();
 $cnx = $db->conectarPDO();


if(isset($_POST["gestion_critico"]) && $_POST["gestion_critico"]=="gestion_critico"){

	$actividad = $_POST['actividad'];

	if($actividad=="Provision"){
			$id = $_POST['idfila'];
			$ob_averia = new gestionProvision();
			$averias = $ob_averia->getGestionProvisionId($cnx,$id);
		
	}else if($actividad=="Manual"){
			$id = $_POST['idfila'];
			//print_r($_GET);
			$ob_manual = new GestionManual();
			$averias = $ob_manual->getGestionManualId($cnx,$id);
		
	}else if($actividad=="Averias"){
			$id = $_POST['idfila'];
			$ob_averia = new gestionAveria();
			$averias = $ob_averia->getGestionAveriaId($cnx,$id);
	}

	$id_gestion = $_POST["id_gestion"];

	$tecnico="Ninguno";
	if($_POST['motivo']=='1' and $_POST['submotivo']=='1' and $_POST['flag_tecnico']=='si'){
		$tecnico=$_POST['tecnico'];
	}

	$ob_evento = new Tecnicos();
	$carnet = $ob_evento->getTecnicoCarnet($cnx,$tecnico);

	$ob_estados= new Estados();
	$estados = $ob_estados->getEstadoxId($cnx,$_POST['estado']);

	/*$ob_averia= new Averias();
	$averia_critico_final=$ob_averia->getAverias($cnx,$averias['averia']);*/

	$velocidad=array('','','');
	if(trim($averias['paquete'])!=''){
		$velocidad=explode("|",$averias['paquete']);
	}

	$envio=array();
	$falta=""; // falta datos

	$operation="AutoSelect";
	/*$operation="Create";
	if($_POST['n_evento']*1>0){
        $operation="Update";
     }*/

	$envio['UserName']					=	'';
	$envio['Password']					=	'';

	$envio['Operation']					=	$operation;
	$envio['TaskNumber']				=	$_POST['id_gestion'];
	$envio['EmployeeNumber']			=	$carnet; // jala el carnet del tecnico
	$envio['DueDateAsYYYYMMDDHHMMSS']	=	date("YmdHis",strtotime($averias['fecha_registro']));

	$Duration=2;
	if($_POST['hora_agenda']!=''){
		$hora_agenda=$_POST['hora_agenda'];
		$buscar=array('am','pm',' ');
		$reemplazar=array('','','');
		$hora_agenda=str_replace($buscar,$reemplazar,$hora_agenda);
		$dha=explode("-",$hora_agenda);
		$Duration=$dha[1]*1-$dha[0]*1;
	}

	$envio['Duration']					=	$Duration;
	$envio['Notes']						=	"";	

	if($_POST['hora_agenda']!=''){
		$hora_agenda=$_POST['hora_agenda'];
		$buscar=array('am','pm',' ');
		$reemplazar=array(':00',':00','');
		$hora_agenda=str_replace($buscar,$reemplazar,$hora_agenda);
	}

	$envio['Description']				=	trim("Agenda: ".$_POST['fecha_agenda']." ".$hora_agenda);
	$envio['Status']					=	"NewTask";
	$envio['CustomerName']				=	$_POST['id_gestion']."-".$averias['averia'];
	$envio['Location ']					=	array("East" => "-77.10123", "North" => "-12.10123", "Address" => "Calle Lima 150");
	$envio['Data1']						=	trim($_POST['fecha_agenda']." ".$hora_agenda);
	$envio['Data2']						=	$averias['averia'];
	$envio['Data3']						=	$averias['fecha_registro'];
	$envio['Data4']						=	$averias['nombre_cliente'];
	$envio['Data5']						=	$averias['direccion_instalacion'];
	$envio['Data6']						=	$averias['codmotivo_req_catv'];
	$envio['Data7']						=	$averias['orden_trabajo'];
	$envio['Data8']						=	$averias['fftt'];
	$envio['Data9']						=	$averias['dir_terminal'];
	$envio['Data10']					=	$averias['inscripcion'];
	$envio['Data11']					=	$averias['mdf'];
	$envio['Data12']					=	$averias['segmento'];
	$envio['Data13']					=	$averias['clase_servicio_catv'];
	$envio['Data14']					=	$averias['total_averias'];
	$envio['Data15']					=	$averias['zonal'];
	$envio['Data16']					=	$averias['llamadastec15dias'];
	$envio['Data17']					=	$averias['quiebre'];
	$envio['Data18']					=	$averias['lejano'];
	$envio['Data19']					=	$averias['distrito'];
	$envio['Data20']					=	$averias['averia_m1'];
	$envio['Data21']					=	$averias['telefono_codclientecms'];
	$envio['Data22']					=	$averias['area2'];
	$envio['Data23']					=	''; //tipo_servicio
	$envio['Data24']					=	''; //tipo_actuacion
	$envio['Data25']					=	$averias['eecc_final'];
	$envio['Data26']					=	$averias['id_gestion'];
	$envio['Data27']					=	$estados['estado']; //Estado Webpsi
	$envio['Data28']					=	$_POST['cr_observacion'];
	$envio['Data29']					=	$velocidad[2];
	$envio['Options']					=	"SendNotificationToMobile";

}
elseif(isset($_POST["registro_critico"]) && $_POST["registro_critico"]=="registro_critico"){


	$ob_gc = new gestionCriticos();
	$id_gestion_critico = $ob_gc->getGenerarIDCritico($cnx);

	$id_gestion = $id_gestion_critico;
	$ob_evento = new Tecnicos();
	$carnet = $ob_evento->getTecnicoCarnet($cnx,$_POST['tecnico']);

	$ob_estados= new Estados();
	$estados = $ob_estados->getEstadoxId($cnx,"1");

	/*$ob_averia= new Averias();
	$averia_critico_final=$ob_averia->getAverias($cnx,$averias['averia']);*/

	$velocidad=array('','','');
	if(trim($_POST['paquete'])!=''){
		$velocidad=explode("|",$_POST['paquete']);
	}

	$envio=array();
	$falta=""; // falta datos

	$operation="AutoSelect";
	/*$operation="Create";
	if($_POST['n_evento']*1>0){
        $operation="Update";
     }*/

	$envio['UserName']					=	'';
	$envio['Password']					=	'';

	$envio['Operation']					=	$operation;
	$envio['TaskNumber']				=	$id_gestion;
	$envio['EmployeeNumber']			=	$carnet; // jala el carnet del tecnico

	$envio['DueDateAsYYYYMMDDHHMMSS']	=	date("YmdHis",strtotime($_POST['fecha_registro']));

	$Duration=2;
	if($_POST['hora_agenda']!=''){
		$hora_agenda=$_POST['hora_agenda'];
		$buscar=array('am','pm',' ');
		$reemplazar=array('','','');
		$hora_agenda=str_replace($buscar,$reemplazar,$hora_agenda);
		$dha=explode("-",$hora_agenda);
		$Duration=$dha[1]*1-$dha[0]*1;
	}

	$envio['Duration']					=	$Duration;
	$envio['Notes']						=	"";	

	if($_POST['hora_agenda']!=''){
		$hora_agenda=$_POST['hora_agenda'];
		$buscar=array('am','pm',' ');
		$reemplazar=array(':00',':00','');
		$hora_agenda=str_replace($buscar,$reemplazar,$hora_agenda);
	}

	$envio['Description']				=	trim($sin." Agenda: ".$_POST['fecha_agenda']." ".$hora_agenda);
	$envio['Status']					=	"NewTask";
	$envio['CustomerName']				=	$id_gestion."-".$_POST['averia'];
	$envio['Location ']					=	array("East" => "-77.10123", "North" => "-12.10123", "Address" => "Calle Lima 150");
	$envio['Data1']						=	trim($_POST['fecha_agenda']." ".$hora_agenda);
	$envio['Data2']						=	$_POST['averia'];
	$envio['Data3']						=	$_POST['fecha_registro'];
	$envio['Data4']						=	$_POST['nombre_cliente'];
	$envio['Data5']						=	$_POST['direccion_instalacion'];
	$envio['Data6']						=	$_POST['codmotivo_req_catv'];
	$envio['Data7']						=	$_POST['orden_trabajo'];
	$envio['Data8']						=	$_POST['fftt'];
	$envio['Data9']						=	$_POST['dir_terminal'];
	$envio['Data10']					=	$_POST['inscripcion'];
	$envio['Data11']					=	$_POST['mdf'];
	$envio['Data12']					=	$_POST['segmento'];
	$envio['Data13']					=	$_POST['clase_servicio_catv'];
	$envio['Data14']					=	$_POST['total_averias'];
	$envio['Data15']					=	$_POST['zonal'];
	$envio['Data16']					=	$_POST['llamadastec15dias'];
	$envio['Data17']					=	$_POST['quiebre'];
	$envio['Data18']					=	$_POST['lejano'];
	$envio['Data19']					=	$_POST['distrito'];
	$envio['Data20']					=	$_POST['averia_m1'];
	$envio['Data21']					=	$_POST['telefono_codclientecms'];
	$envio['Data22']					=	$_POST['area2'];
	$envio['Data23']					=	''; //tipo_servicio
	$envio['Data24']					=	''; //tipo_actuacion
	$envio['Data25']					=	$_POST['eecc_final'];
	$envio['Data26']					=	$id_gestion;
	$envio['Data27']					=	$estados['estado']; //Estado Webpsi
	$envio['Data28']					=	$_POST['cr_observacion'];
	$envio['Data29']					=	$velocidad[2];
	$envio['Options']					=	"SendNotificationToMobile";

}

echo json_encode($envio);

?>