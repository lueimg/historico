<?php
require_once("../../cabecera.php");

require_once('clases/casosNuevos.php');
require_once 'clases/mdfs.php';
require_once 'clases/gestionManual.php';
require_once 'clases/gestionCriticos.php';

//Abriendo la conexion
 $db = new Conexion();
 $cnx = $db->conectarPDO();
 
 
 if ( isset( $_POST["action"] ) ) {
     
     if ( $_POST["action"] === "checkNewCtc" ) {
         $ob_nuevos = new CasosNuevos();
         $casos = $ob_nuevos->getResumenAll($cnx);
         echo json_encode($casos);
     }
     
     /**
      * Ingreso manual de pendientes (rutinas)
      */
     
     //Lista de mdfs
     if ( $_POST["action"] === "getMdfByZonal" ) {
         $Mdfs = new Mdfs();
         $zonal = $_POST["zonal"];
         $tipo = $_POST["tipo"];
         $arrMdf = array();
         
         if ( $tipo==="rutina-bas-lima" or $tipo==="rutina-adsl-pais" ) {
             $arrMdf = $Mdfs->getMdfs($cnx, "'$zonal'");
         }
         
         if ( $tipo==="rutina-catv-pais" ) {
             $arrMdf = $Mdfs->getMdfCatv($cnx, "'$zonal'");            
         }
         
         echo json_encode($arrMdf);
     }
     
    //lista de eecc 
    if ( $_POST["action"] === "registraRutina" ) {
        $GestionManual = new GestionManual();
        
        $arrMdf = explode("___", $_POST['mdf']);

        $tipo_actividad=trim( $_POST['tipo_actividad'] );
        $tipo_averia = trim( $_POST['tipo_averia'] );
        //$inscripcion = trim( $_POST['inscripcion'] ); // aqui deberia ser averias...
        $averia= trim( $_POST['averia'] ); // reemplazando a inscripcion
            
        $inscripcion= trim( $_POST['telefono'] );
        $fono = trim( $_POST['telefono'] );     
        $codcliente = trim( $_POST['telefono'] );          

        $mdf = trim( $arrMdf[0] );
        $observacion = trim( $_POST['cr_observacion'] );
        $segmento = trim( $_POST['segmento'] );
        $direccion = trim( $_POST['direccion'] );
        $nombre_cliente = trim( $_POST['cr_nombre'] );
        $fonos_contacto = trim( $_POST['cr_telefono'] );
        $contrata = trim( $_POST['eecc'] );
        $zonal = trim( $_POST['zonal'] );
        $lejano = trim( $_POST['lejano'] );
        $distrito = trim( $_POST['distrito'] );
        $eecc_zona = trim( $_POST['eecc'] );
        $zona_movistar_uno = trim( $_POST['movistar_uno'] );       
        $eecc = trim( $_POST['eecc'] );
        $microzona = trim( $_POST['microzona'] );
        $celular = trim( $_POST['cr_celular'] );
        $id_usuario = $_SESSION['exp_user']['id'];
        $rm_averia = '';
        if (isset($_POST['rm_averia']) and trim($_POST['rm_averia'])!=='') {
            $rm_averia = trim($_POST['rm_averia']);
        }
        $quiebre = trim( $_POST['quiebre'] );
        
        $save = $GestionManual->addGestionManual(
                $cnx, $id_usuario, $rm_averia,
                $tipo_averia, $inscripcion, $fono, $direccion,
                $mdf, $observacion, $segmento, 
                $direccion, $nombre_cliente, $fonos_contacto, 
                $contrata, $zonal, $lejano, 
                $distrito, $eecc_zona, $zona_movistar_uno, 
                $codcliente, $eecc, $microzona, $celular,
                $quiebre,$averia,$tipo_actividad);
        echo json_encode($save);
     }
     
    /**
     * Registra ATC ya no genera RTC
     */
    if ( $_POST["action"] === "registraRutinaAgenda" ) {
        
        $arrMdf = explode("___", $_POST['mdf']);
        $_POST['mdf'] = trim( $arrMdf[0] );
        $_POST["tipo_averia"] = "";
        $_POST["horas_averia"] = "";        
        $_POST["fecha_registro"] = date("Y-m-d H:i:s");
        $_POST["ciudad"] = "";
        $_POST["averia"] = trim($_POST['rm_averia']);
        $_POST["fono1"] = trim( $_POST['telefono'] );
        $_POST["observacion_102"] = "";
        $_POST["area_"] = "";
        $_POST["direccion_instalacion"] = $_POST["direccion"];
        $_POST["codigo_distrito"] = $_POST["distrito"];
        $_POST["nombre_cliente"] = $_POST["cr_nombre"];
        $_POST["orden_trabajo"] = "";
        $_POST["veloc_adsl"] = "";
        $_POST["clase_servicio_catv"] = "";
        $_POST["codmotivo_req_catv"] = "";
        $_POST["total_averias_cable"] = "";
        $_POST["total_averias_cobre"] = "";
        $_POST["total_averias"] = "";
        $_POST["fftt"] = "";
        $_POST["llave"] = "";

        $_POST["wu_nagendas"] = "";
        $_POST["wu_nmovimientos"] = "";
        $_POST["wu_fecha_ult_agenda"] = "";
        $_POST["total_llamadas_tecnicas"] = "";
        $_POST["total_llamadas_seguimiento"] = "";
        $_POST["llamadastec15dias"] = "";
        $_POST["llamadastec30dias"] = "";

        $_POST["dir_terminal"] = "";
        $_POST["fonos_contacto"] = $_POST["telefono"];
        $_POST["contrata"] = $_POST["eecc"];
        $_POST["eecc_final"] = $_POST["eecc"];
        $_POST["zona_movistar_uno"] = trim( $_POST['movistar_uno'] );
        $_POST["paquete"] = "";
        $_POST["data_multiproducto"] = "";
        $_POST["averia_m1"] = "";
        $_POST["fecha_data_fuente"] = "";
        $_POST["telefono_codclientecms"] = "";
        $_POST["rango_dias"] = "";
        $_POST["sms1"] = "";
        $_POST["sms2"] = "";
        $_POST["area2"] = "";
        $_POST["txt_idusuario"] = "";
        $_POST["nombretecnico"] = "";
        $_POST["tecnico"] = "";
        $_POST["flag_tecnico"] = "";
        $_POST["tipo_actividad"] = $_POST["tipo_actu"];
        $_POST["motivo_registro"] = "";
        $_POST["tipo_actuacion"] = $_POST["tipo_actu"];
        
        $_POST["txt_idusuario"] = $_SESSION["exp_user"]["id"];

        $_POST['datosfinal'] = "";
        
        $GestionCriticos = new gestionCriticos();        
	$save = $GestionCriticos->addClienteCritico($cnx);
        echo json_encode($save);
        
        /*        
        $GestionManual = new GestionManual();
        
        $arrMdf = explode("___", $_POST['mdf']);

        $tipo_actu = trim( $_POST['tipo_actu'] );
        $tipo_averia = trim( $_POST['tipo_averia'] );
        $inscripcion = trim( $_POST['inscripcion'] );
        $fono = trim( $_POST['telefono'] );
        $mdf = trim( $arrMdf[0] );
        $observacion = trim( $_POST['cr_observacion'] );
        $segmento = trim( $_POST['segmento'] );
        $direccion = trim( $_POST['direccion'] );
        $nombre_cliente = trim( $_POST['cr_nombre'] );
        $fonos_contacto = trim( $_POST['cr_telefono'] );
        $contrata = trim( $_POST['eecc'] );
        $zonal = trim( $_POST['zonal'] );
        $lejano = trim( $_POST['lejano'] );
        $distrito = trim( $_POST['distrito'] );
        $eecc_zona = trim( $_POST['eecc'] );
        $zona_movistar_uno = trim( $_POST['movistar_uno'] );
        $codcliente = trim( $_POST['inscripcion'] );
        $eecc = trim( $_POST['eecc'] );
        $microzona = trim( $_POST['microzona'] );
        $celular = trim( $_POST['cr_celular'] );
        $id_usuario = $_SESSION['exp_user']['id'];
        $rm_averia = '';
        if (isset($_POST['rm_averia']) and trim($_POST['rm_averia'])!=='') {
            $rm_averia = trim($_POST['rm_averia']);
        }
        $quiebre = trim( $_POST['quiebre'] );
        
        $fecha_agenda = trim( $_POST['fecha_agenda'] );
        $id_horario = trim( $_POST['horario_agenda'] );
        $id_dia = trim( $_POST['dia_agenda'] );
        
        $save = $GestionManual->addGestionManualAgenda(
                $cnx, $id_usuario, $rm_averia,
                $tipo_averia, $inscripcion, $fono, $direccion,
                $mdf, $observacion, $segmento, 
                $direccion, $nombre_cliente, $fonos_contacto, 
                $contrata, $zonal, $lejano, 
                $distrito, $eecc_zona, $zona_movistar_uno, 
                $inscripcion, $eecc, $microzona, $celular,
                $quiebre, $fecha_agenda, $id_horario, $id_dia,
                $tipo_actu);
        echo json_encode($save);
         * 
         */
     }
     
 }