<?php

class GestionManual {

    public function addGestionManual($dbh, $id_usuario, 
            $tipo_averia, $inscripcion, $fono, $direccion,
            $mdf, $observacion, $segmento, 
            $direccion, $nombre_cliente, $fonos_contacto, 
            $contrata, $zonal, $lejano, 
            $distrito, $eecc_zona, $zona_movistar_uno, 
            $codcliente, $eecc, $microzona, $celular
            ) {
        
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
                        tipo_actividad, nmov
                    ) 
                    VALUES (
                        NULL, NULL, '$nombre_cliente',
                        '$fono', '$celular',
                        '', '1', '2',
                        '2', '2', '$observacion',
                        '$fecreg', '', 
                        'Manual', '1'
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

            //Table: 
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
                    '$fecreg', '', '$atc',
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
                    '0', '0', 'RUTINA MANUAL',
                    '$lejano', '$distrito', '$eecc_zona',
                    '$zona_movistar_uno', '', '',
                    '', '$fecreg', '$codcliente',
                    '', '', '',
                    'EN CAMPO', '$eecc', '$microzona'
                )";
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

}

?>