<?php
require_once("../../cabecera.php");

require_once('clases/casosNuevos.php');
require_once 'clases/mdfs.php';
require_once 'clases/gestionManual.php';

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
        
        $save = $GestionManual->addGestionManual(
                $cnx, $id_usuario, $rm_averia,
                $tipo_averia, $inscripcion, $fono, $direccion,
                $mdf, $observacion, $segmento, 
                $direccion, $nombre_cliente, $fonos_contacto, 
                $contrata, $zonal, $lejano, 
                $distrito, $eecc_zona, $zona_movistar_uno, 
                $inscripcion, $eecc, $microzona, $celular,
                $quiebre);
        echo json_encode($save);
     }
     
     //Registrar rutina con agenda
    if ( $_POST["action"] === "registraRutinaAgenda" ) {
        $GestionManual = new GestionManual();
        
        $arrMdf = explode("___", $_POST['mdf']);

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
        
        $save = $GestionManual->addGestionManualAgenda(
                $cnx, $id_usuario, $rm_averia,
                $tipo_averia, $inscripcion, $fono, $direccion,
                $mdf, $observacion, $segmento, 
                $direccion, $nombre_cliente, $fonos_contacto, 
                $contrata, $zonal, $lejano, 
                $distrito, $eecc_zona, $zona_movistar_uno, 
                $inscripcion, $eecc, $microzona, $celular,
                $quiebre);
        echo json_encode($save);
     }
     
 }