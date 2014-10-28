<?php

class GestionManual {

    public function addGestionManual($dbh, $id_usuario, $rm_averia,
            $tipo_averia, $inscripcion, $fono, $direccion,
            $mdf, $observacion, $segmento, 
            $direccion, $nombre_cliente, $fonos_contacto, 
            $contrata, $zonal, $lejano, 
            $distrito, $eecc_zona, $zona_movistar_uno, 
            $codcliente, $eecc, $microzona, $celular,
            $quiebre,$averia,$tipo_actividad,$fftt,$x,$y) {

        $tabla="";
        $campo="";
        if($tipo_actividad!="AVERIA"){
            $tabla="_provision";
            $campo="_Provision";
        }
        
        try {
            //Iniciar transaccion
            $dbh->beginTransaction();
            
            /**
			 * 0. Verificar existencia de registro por Insc./Cod.Cli.
             * 1. Guardar en gestion_criticos
             * 2. Obtener ID y actualizar ATC
             * 3. Guardar en gestion movimientos
             * 4. Guardar en gestion_rutina_manual 
             */
            
            $fecreg = date("Y-m-d H:i:s");
			
			//Table: gestion_rutina_manual, verificar registro nuevo pendiente 
            if($tabla==""){
            $sql = "SELECT 
                        gr.tipo_averia, gr.averia, 
                        gr.nombre_cliente, gr.telefono
                    FROM 
                        webpsi_criticos.gestion_rutina_manual gr, 
                        webpsi_criticos.gestion_criticos gc
                    WHERE 
                        gr.averia='$averia' 
                        AND gc.id_estado=2 
                        AND gr.id_gestion=gc.id";
            }
            else{
            $sql = "SELECT 
                        gr.origen, gr.codigo_req, 
                        gr.nomcliente, gr.telefono
                    FROM 
                        webpsi_criticos.gestion_rutina_manual_provision gr, 
                        webpsi_criticos.gestion_criticos gc
                    WHERE 
                        gr.codigo_req='$averia' 
                        AND gc.id_estado=2 
                        AND gr.id_gestion=gc.id";
            }

            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);

			try {
                if($tabla==""){
                    if ( trim($data['averia']) !== ""  ) {
                        $msg = "Existe un registro PENDIENTE:"
                                . "\nTipo averia: " . $data['tipo_averia']
                                . "\nAveria: " . $data['averia']
                                . "\nCliente: " . $data['nombre_cliente'];
                        throw new Exception( utf8_encode($msg) );
                    }    
                }
                else{
                    if ( trim($data['codigo_req']) !== ""  ) {
                        $msg = "Existe un registro PENDIENTE:"
                                . "\nOrigen: " . $data['origen']
                                . "\nCod Requer.: " . $data['codigo_req']
                                . "\nCliente: " . $data['nomcliente'];
                        throw new Exception( utf8_encode($msg) );
                    } 
                }
                
            } catch (Exception $error) {
                $dbh->rollback();
                $result["estado"] = false;
                $result["msg"] = $error->getMessage();
                $result["data"] = "";
                return $result;
            }
            
            //Table: gestion_criticos
            $sql = "INSERT INTO webpsi_criticos.gestion_criticos 
                    (
                        id, id_atc, nombre_cliente_critico,
                        telefono_cliente_critico, celular_cliente_critico,
                        fecha_agenda, id_horario, id_motivo,
                        id_submotivo, id_estado, observacion,
                        fecha_creacion, flag_tecnico, 
                        tipo_actividad, nmov, n_evento 
                    ) 
                    VALUES (
                        NULL, NULL, '$nombre_cliente',
                        '$fono', '$celular',
                        '', '1', '2',
                        '2', '2', '$observacion',
                        '$fecreg', '', 
                        'Manual$campo', '1', '0'
                    )";
            $dbh->exec($sql);
            
            //Ultimo ID registrado
            $id = $dbh->lastInsertId();
            $atc = "RTC_" . date("Y") . "_" . $id;


            
            //Table: gestion_criticos -> update
            $sql = "UPDATE 
                        webpsi_criticos.gestion_criticos 
                    SET 
                        id_atc='$atc' 
                    WHERE id=$id";
            $dbh->exec($sql);
            
            //Consultar Zonal
            $sql = "SELECT 
                        id 
                    FROM 
                        webpsi_criticos.zonales 
                    WHERE abreviatura = '$zonal'";
            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);
            $idZonal = $data['id'];
            
            //Consultar EECC
            $sql = "SELECT 
                        id 
                    FROM 
                        webpsi_criticos.empresa 
                    WHERE nombre = '$eecc'";
            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);
            $idEmpresa = $data['id'];
            
            //Table: gestion_movimientos
            $sql = "INSERT INTO webpsi_criticos.gestion_movimientos (
                        id, id_gestion, 
                        id_empresa, id_zonal, fecha_agenda,
                        id_horario, id_dia, id_motivo,
                        id_submotivo, id_estado,
                        tecnicos_asignados,
                        observacion, id_usuario,
                        fecha_movimiento, tecnico,
                        fecha_consolidacion
                    ) VALUES (
                        NULL, $id,
                        '$idEmpresa', '$idZonal', '',
                        '1', '1', '2',
                        '2', '2',
                        '',
                        '$observacion', '$id_usuario',
                        '$fecreg', '',
                        '$fecreg'
                    )";
            $dbh->exec($sql);


            $idmov = $dbh->lastInsertId();
             $sqlgmt= "  insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
                        select gm.id,tt.id
                        from webpsi_criticos.gestion_movimientos gm
                        left join 
                            (select t.id,t.nombre_tecnico 
                            from webpsi_criticos.tecnicos t 
                            GROUP by t.nombre_tecnico) tt on trim(gm.tecnico)=trim(tt.nombre_tecnico)
                        where tt.id is not null
                        and gm.tecnico!=''
                        and gm.id='$idmov'";
            $res = $dbh->exec($sqlgmt);
			
			$atc_ave = $atc;
            if ($rm_averia !== '') {
                $atc_ave = $rm_averia;
            }

            //Table: 
            if($tabla==""){

            $sql = "INSERT INTO webpsi_criticos.gestion_rutina_manual (
                    id, id_gestion,
                    tipo_averia, horas_averia, fecha_reporte,
                    fecha_registro, ciudad, averia,
                    inscripcion, fono1, telefono,
                    mdf, observacion_102, segmento,
                    area_, direccion_instalacion, codigo_distrito,
                    nombre_cliente, orden_trabajo, veloc_adsl,
                    clase_servicio_catv, codmotivo_req_catv, total_averias_cable,
                    total_averias_cobre, total_averias,
                    fftt, llave, dir_terminal,
                    fonos_contacto, contrata, zonal,
                    wu_nagendas, wu_nmovimientos, wu_fecha_ult_agenda,
                    total_llamadas_tecnicas, total_llamadas_seguimiento,
                    llamadastec15dias, llamadastec30dias, quiebre,
                    lejano, distrito, eecc_zona,
                    zona_movistar_uno, paquete, data_multiproducto,
                    averia_m1, fecha_data_fuente, telefono_codclientecms,
                    rango_dias, sms1, sms2,
                    area2, eecc_final, microzona,
                    x,y
                ) VALUES (
                    NULL, '$id',
                    '$tipo_averia', '0', '',
                    '$fecreg', '', '$averia',
                    '$inscripcion', '$fono', '$fono',
                    '$mdf', '$observacion', '$segmento',
                    '', '$direccion', '',
                    '$nombre_cliente', '', '',
                    '', '', '',
                    '', '',
                    '$fftt', '', '',
                    '$fonos_contacto', '$contrata', '$zonal',
                    '0', '0', '',
                    '0', '0',
                    '0', '0', '$quiebre',
                    '$lejano', '$distrito', '$eecc_zona',
                    '$zona_movistar_uno', '', '',
                    '', '$fecreg', '$codcliente',
                    '', '', '',
                    'EN CAMPO', '$eecc', '$microzona',
                    '$x','$y'
                )";
            }
            else{
            $sql="INSERT INTO webpsi_criticos.gestion_rutina_manual_provision (
                  id,id_gestion,origen,horas_pedido,fecha_Reg
                  ,ciudad,codigo_req,codigo_del_cliente,fono1,telefono
                  ,mdf,obs_dev,codigosegmento,estacion,direccion,distrito
                  ,nomcliente,orden,veloc_adsl,servicio,tipo_motivo ,tot_aver_cab
                  ,tot_aver_cob,tot_averias,fftt,llave,dir_terminal,fonos_contacto
                  ,contrata,zonal,wu_nagendas,wu_nmovimient,wu_fecha_ult_age
                  ,tot_llam_tec,tot_llam_seg,llamadastec15d,llamadastec30d,quiebre
                  ,lejano,des_distrito,eecc_zon,zona_movuno,paquete,data_multip,aver_m1
                  ,fecha_data_fuente,telefono_codclientecms,rango_dias,sms1,sms2,area2 
                  ,tipo_actuacion,eecc_final,microzona,x,y)
                VALUES (
                    NULL, '$id','$tipo_averia', '0','$fecreg'
                    , '', '$averia','$inscripcion', '$fono', '$fono'
                    ,'$mdf', '$observacion', '$segmento','', '$direccion', ''
                    ,'$nombre_cliente', '', '','','',''
                    , '','', '$fftt','', '','$fonos_contacto'
                    , '$contrata', '$zonal','0', '0', '0'
                    ,'0', '0','0', '0', '$quiebre'
                    ,'$lejano', '$distrito', '$eecc_zona','$zona_movistar_uno', '', '',''
                    , '$fecreg', '$codcliente','', '', '','EN CAMPO'
                    ,'RUTINA', '$eecc', '$microzona','$x','$y'
                )";
            }
            $dbh->exec($sql);

            $dbh->commit();
            $result["estado"] = true;
            $result["msg"] = "Pedido registrado correctamente";
            $result["data"] = $atc;
            return $result;
        } catch (PDOException $error) {
            $dbh->rollback();
            $result["estado"] = false;
            $result["msg"] = $error->getMessage();
            $result["data"] = "";
            return $result;
            exit();
        }

        
    }
    
    /**
     * Método para grabar la gestión manual del visor histórico
     * con agenda propia
     * 
     * @param type $dbh
     * @param type $id_usuario
     * @param type $rm_averia
     * @param type $tipo_averia
     * @param type $inscripcion
     * @param type $fono
     * @param type $direccion
     * @param type $mdf
     * @param type $observacion
     * @param type $segmento
     * @param type $direccion
     * @param type $nombre_cliente
     * @param type $fonos_contacto
     * @param type $contrata
     * @param type $zonal
     * @param type $lejano
     * @param type $distrito
     * @param type $eecc_zona
     * @param type $zona_movistar_uno
     * @param type $codcliente
     * @param type $eecc
     * @param type $microzona
     * @param type $celular
     * @param type $quiebre
     * @return string
     * @throws Exception
     */
    public function addGestionManualAgenda($dbh, $id_usuario, $rm_averia,
            $tipo_averia, $inscripcion, $fono, $direccion,
            $mdf, $observacion, $segmento, 
            $direccion, $nombre_cliente, $fonos_contacto, 
            $contrata, $zonal, $lejano, 
            $distrito, $eecc_zona, $zona_movistar_uno, 
            $codcliente, $eecc, $microzona, $celular,
            $quiebre, $fecha_agenda, $id_horario, $id_dia,
            $tipo_actu) {
        
        try {
            //Iniciar transaccion
            $dbh->beginTransaction();
            
            /**
             * 0. Verificar existencia de registro por Insc./Cod.Cli.
             * 1. Guardar en gestion_criticos
             * 2. Obtener ID y actualizar ATC
             * 3. Guardar en gestion movimientos
             * 4. Guardar en gestion_rutina_manual 
             */
            
            date_default_timezone_set("America/Lima");
            
            $fecreg = date("Y-m-d H:i:s");
			
            //Table: gestion_rutina_manual, verificar registro nuevo pendiente 
            if ($tipo_actu==="averia")
            {
                $sql = "SELECT 
                            gr.tipo_averia, gr.averia, 
                            gr.nombre_cliente, gr.telefono
                        FROM 
                            webpsi_criticos.gestion_rutina_manual gr, 
                            webpsi_criticos.gestion_criticos gc
                        WHERE 
                            gr.inscripcion='$inscripcion' 
                            AND gc.id_estado=2 
                            AND gr.id_gestion=gc.id";
            } else {
                $sql = "SELECT 
                            gr.tipo_averia, gr.averia, 
                            gr.nombre_cliente, gr.telefono
                        FROM 
                            webpsi_criticos.gestion_rutina_manual gr, 
                            webpsi_criticos.gestion_criticos gc
                        WHERE 
                            gr.inscripcion='$inscripcion' 
                            AND gc.id_estado=2 
                            AND gr.id_gestion=gc.id";
            }
            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);
			
            try {
                if ( trim($data['averia']) !== ""  ) {
                    $msg = "Existe un registro PENDIENTE:"
                            . "\nTipo averia: " . $data['tipo_averia']
                            . "\nAveria: " . $data['averia']
                            . "\nCliente: " . $data['nombre_cliente'];
                    throw new Exception( utf8_encode($msg) );
                }
            } catch (Exception $error) {
                $dbh->rollback();
                $result["estado"] = false;
                $result["msg"] = $error->getMessage();
                $result["data"] = "";
                return $result;
            }
            
            //Table: gestion_criticos
            $sql = "INSERT INTO webpsi_criticos.gestion_criticos 
                    (
                        id, id_atc, nombre_cliente_critico,
                        telefono_cliente_critico, celular_cliente_critico,
                        fecha_agenda, id_horario, id_motivo,
                        id_submotivo, id_estado, observacion,
                        fecha_creacion, flag_tecnico, 
                        tipo_actividad, nmov, n_evento 
                    ) 
                    VALUES (
                        NULL, NULL, '$nombre_cliente',
                        '$fono', '$celular',
                        '', '1', '1',
                        '1', '8', '$observacion',
                        '$fecreg', '', 
                        'Manual_" . ucfirst($tipo_actu) . "', '1', '0'
                    )";
            $dbh->exec($sql);
            
            //Ultimo ID registrado
            $id = $dbh->lastInsertId();
            $atc = "RTC_" . date("Y") . "_" . $id;


            
            //Table: gestion_criticos -> update
            $sql = "UPDATE 
                        webpsi_criticos.gestion_criticos 
                    SET 
                        id_atc='$atc' 
                    WHERE id=$id";
            $dbh->exec($sql);
            
            //Consultar Zonal
            $sql = "SELECT 
                        id 
                    FROM 
                        webpsi_criticos.zonales 
                    WHERE abreviatura = '$zonal'";
            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);
            $idZonal = $data['id'];
            
            //Consultar EECC
            $sql = "SELECT 
                        id 
                    FROM 
                        webpsi_criticos.empresa 
                    WHERE nombre = '$eecc'";
            $bind = $dbh->prepare($sql);
            $bind->execute();
            $data = $bind->fetch(PDO::FETCH_ASSOC);
            $idEmpresa = $data['id'];
            
            //Table: gestion_movimientos
            $sql = "INSERT INTO webpsi_criticos.gestion_movimientos (
                        id, id_gestion, 
                        id_empresa, id_zonal, fecha_agenda,
                        id_horario, id_dia, id_motivo,
                        id_submotivo, id_estado,
                        tecnicos_asignados,
                        observacion, id_usuario,
                        fecha_movimiento, tecnico,
                        fecha_consolidacion
                    ) VALUES (
                        NULL, $id,
                        '$idEmpresa', '$idZonal', '$fecha_agenda',
                        '$id_horario', '$id_dia', '1',
                        '1', '8',
                        '',
                        '$observacion', '$id_usuario',
                        '$fecreg', '',
                        '$fecreg'
                    )";
            $dbh->exec($sql);


            $idmov = $dbh->lastInsertId();
             $sqlgmt= "  insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
                        select gm.id,tt.id
                        from webpsi_criticos.gestion_movimientos gm
                        left join 
                            (select t.id,t.nombre_tecnico 
                            from webpsi_criticos.tecnicos t 
                            GROUP by t.nombre_tecnico) tt on trim(gm.tecnico)=trim(tt.nombre_tecnico)
                        where tt.id is not null
                        and gm.tecnico!=''
                        and gm.id='$idmov'";
            $res = $dbh->exec($sqlgmt);
			
	    $atc_ave = $atc;
            if ($rm_averia !== '') {
                $atc_ave = $rm_averia;
            }

            //Table: 
            if ($tipo_actu === "averia"){
                $sql = "INSERT INTO webpsi_criticos.gestion_rutina_manual (
                        id, id_gestion,
                        tipo_averia, horas_averia, fecha_reporte,
                        fecha_registro, ciudad, averia,
                        inscripcion, fono1, telefono,
                        mdf, observacion_102, segmento,
                        area_, direccion_instalacion, codigo_distrito,
                        nombre_cliente, orden_trabajo, veloc_adsl,
                        clase_servicio_catv, codmotivo_req_catv, total_averias_cable,
                        total_averias_cobre, total_averias,
                        fftt, llave, dir_terminal,
                        fonos_contacto, contrata, zonal,
                        wu_nagendas, wu_nmovimientos, wu_fecha_ult_agenda,
                        total_llamadas_tecnicas, total_llamadas_seguimiento,
                        llamadastec15dias, llamadastec30dias, quiebre,
                        lejano, distrito, eecc_zona,
                        zona_movistar_uno, paquete, data_multiproducto,
                        averia_m1, fecha_data_fuente, telefono_codclientecms,
                        rango_dias, sms1, sms2,
                        area2, eecc_final, microzona
                    ) VALUES (
                        NULL, '$id',
                        '$tipo_averia', '0', '',
                        '$fecreg', '', '$atc_ave',
                        '$inscripcion', '$fono', '$fono',
                        '$mdf', '$observacion', '$segmento',
                        '', '$direccion', '',
                        '$nombre_cliente', '', '',
                        '', '', '',
                        '', '',
                        '', '', '',
                        '$fonos_contacto', '$contrata', '$zonal',
                        '0', '0', '',
                        '0', '0',
                        '0', '0', '$quiebre',
                        '$lejano', '$distrito', '$eecc_zona',
                        '$zona_movistar_uno', '', '',
                        '', '$fecreg', '$codcliente',
                        '', '', '',
                        'EN CAMPO', '$eecc', '$microzona'
                    )";
            } else {
                $sql = "INSERT INTO webpsi_criticos.gestion_rutina_manual_provision (
                        id, id_gestion,
                        origen, horas_pedido, fecha_Reg,
                        ciudad, codigo_req, codigo_del_cliente,
                        fono1, telefono, mdf,
                        obs_dev, codigosegmento, estacion,
                        direccion, distrito, nomcliente,
                        orden, veloc_adsl, servicio,
                        tipo_motivo, tot_aver_cab, tot_aver_cob,
                        tot_averias, fftt, llave,
                        dir_terminal, fonos_contacto, contrata,
                        zonal, wu_nagendas, wu_nmovimient,
                        wu_fecha_ult_age, tot_llam_tec, tot_llam_seg,
                        llamadastec15d, llamadastec30d,
                        quiebre, lejano, des_distrito,
                        eecc_zon, zona_movuno, paquete,
                        data_multip, aver_m1, fecha_data_fuente,
                        telefono_codclientecms, rango_dias,
                        sms1, sms2, area2,
                        tipo_actuacion, eecc_final, microzona 
                    ) VALUES (
                        NULL, '$id',
                        '', '', '$fecreg',
                        '', '', '',
                        '$fono', '$fono', '',
                        '', '', '',
                        '$direccion', '', '',
                        '', '', '',
                        '', '', '',
                        '', '', '',
                        '', '', '',
                        '$zonal', '', '',
                        '', '', '',
                        '', '',
                        '$quiebre', '$lejano', '$distrito',
                        '', '$zona_movistar_uno', '',
                        '', '', '',
                        '', '', '',
                        '', '', '',
                        '', '$eecc', '$microzona'
                    )";
            }
            $dbh->exec($sql);

            $dbh->commit();
            $result["estado"] = true;
            $result["msg"] = "Pedido registrado correctamente";
            $result["data"] = $atc;
            return $result;
        } catch (PDOException $error) {
            $dbh->rollback();
            $result["estado"] = false;
            $result["msg"] = $error->getMessage();
            $result["data"] = "";
            return $result;
            exit();
        }

        
    }

    function getGestionManualId($cnx,$id){
        
        $cnx->exec("set names utf8");
        $sql = "SELECT * FROM webpsi_criticos.gestion_rutina_manual where id_gestion=$id";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
    }

    function getGestionManualProvisionId($cnx,$id){
        
        $cnx->exec("set names utf8");
        $sql = "SELECT codigo_req AS 'averia',nomcliente 'nombre',fecha_Reg AS 'fecha_reg',quiebre 'quiebres',telefono_codclientecms 'telefono_cliente_critico',
                       origen AS 'tipo_averia',horas_pedido AS 'horas_averia',fecha_Reg AS 'fecha_registro',ciudad,codigo_req AS 'codigo_averia',
                    codigo_del_cliente AS 'inscripcion',fono1,telefono,mdf,obs_dev AS 'observacion_102',codigosegmento AS 'segmento',
                    estacion AS 'area_',direccion AS 'direccion_instalacion',distrito AS 'codigo_distrito',nomcliente AS 'nombre_cliente',orden AS 'orden_trabajo',
                    veloc_adsl,servicio AS 'clase_servicio_catv',tipo_motivo AS 'codmotivo_req_catv',tot_aver_cab AS 'total_averias_cable',
                    tot_aver_cob AS 'total_averias_cobre',tot_averias AS total_averias,fftt,llave,dir_terminal,fonos_contacto,contrata,zonal,
                    quiebre,lejano,des_distrito AS 'distrito',eecc_final,zona_movuno AS 'zona_movistar_uno',paquete,data_multip AS 'data_multiproducto',aver_m1 AS 'averia_m1',
                    fecha_data_fuente,telefono_codclientecms,rango_dias,sms1,wu_fecha_ult_age as 'wu_fecha_ult_agenda',
                    sms2,area2,microzona FROM webpsi_criticos.gestion_rutina_manual_provision where id_gestion=$id";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
    }

    function updateEmpresa($cnx,$vempresa,$vtecnico,$codigo,$idtecnico){

        try{

            $cnx->beginTransaction();

            if($vempresa!=""){
                $cnx->exec("set names utf8");
                $cad = "update webpsi_criticos.`gestion_rutina_manual` set eecc_final='$vempresa' where id_gestion=$codigo";//sin contrarta o zonal
                //echo $cad;
                $res = $cnx->exec($cad);
            }else{
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione una empresa";
                return $result;
            }

            if($vempresa!=""){
                $empresa = new Empresa();
                $id_empresa = $empresa->getIdEmpresa($cnx,$vempresa);
                $gestMovimiento = new gestionMovimientos();
                $gestMovimiento->updateEmpresaMovimientos($cnx,$codigo,$id_empresa,$vtecnico,$idtecnico);
            }else{
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione una empresa";
                return $result;
            }

            $cnx->commit();
            $result["estado"] = TRUE;
            $result["msg"] = "Se asigno la empresa correctamente";
            return $result;

        }catch (PDOException $error){

            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            return $result;
            exit();

        }
    }

    function updateEmpresaProvision($cnx,$vempresa,$vtecnico,$codigo,$idtecnico){

        try{

            $cnx->beginTransaction();

            if($vempresa!=""){
                $cnx->exec("set names utf8");
                $cad = "update webpsi_criticos.`gestion_rutina_manual_provision` set eecc_final='$vempresa' where id_gestion=$codigo";//sin contrarta o zonal
                //echo $cad;
                $res = $cnx->exec($cad);
            }else{
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione una empresa";
                return $result;
            }

            if($vempresa!=""){
                $empresa = new Empresa();
                $id_empresa = $empresa->getIdEmpresa($cnx,$vempresa);
                $gestMovimiento = new gestionMovimientos();
                $gestMovimiento->updateEmpresaMovimientos($cnx,$codigo,$id_empresa,$vtecnico,$idtecnico);
            }else{
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione una empresa";
                return $result;
            }

            $cnx->commit();
            $result["estado"] = TRUE;
            $result["msg"] = "Se asigno la empresa correctamente";
            return $result;

        }catch (PDOException $error){

            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            return $result;
            exit();

        }
    }

}

?>