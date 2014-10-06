<?php

$PATH = $_SERVER['DOCUMENT_ROOT'] . "/webpsi/";
require_once($PATH . 'modulos/historico/clases/gestionMovimientos.php');
require_once($PATH . 'modulos/historico/clases/averias.php');
require_once($PATH . 'modulos/historico/clases/provision.php');
require_once($PATH . 'modulos/historico/clases/gestionAveria.php');
require_once($PATH . 'modulos/historico/clases/gestionProvision.php');
require_once($PATH . 'modulos/historico/clases/estados.php');
require_once($PATH . 'modulos/historico/clases/empresa.php');
require_once($PATH . 'modulos/historico/clases/zonales.php');
date_default_timezone_set("America/Lima");

class gestionCriticos {

    public function getGestionCriticosAll($cnx, $tipo, $filtro, $liquidado, $empresa) {

        $cnx->exec("set names utf8");
        //Obs: La tabla se llena siempre si el filtro esta vacio
        $filtro_sql="";
        $filtro_sql2="";
        $buscar=array("c.id","id_atc");
        $reemplazar=array("a.quiebre","a.quiebre");

        if ($tipo == "telefono" && $filtro != "") {
            $filtro_sql = " AND a.telefono_codclientecms='$filtro'";
            $filtro_sql2 = " AND a.telefono_codclientecms='$filtro'";
        } else if ($tipo == "averia" && $filtro != "") {
            $filtro_sql = " AND a.averia='$filtro'";
            $filtro_sql2 = " AND a.codigo_req='$filtro'";
        } else if ($tipo == "nombre" && $filtro != "") {
            $filtro_sql = " AND a.nombre_cliente like '%$filtro%'";
            $filtro_sql2 = " AND a.nomcliente like '%$filtro%'";
        } else if ($tipo == "atc" && $filtro != "") {
            $filtro_sql = " AND id_atc='$filtro'";
            $filtro_sql2 = " AND id_atc='$filtro'";
        } else if ($tipo == "id_gestion" && $filtro != "") {
            $filtro_sql = " AND  c.id in ($filtro)";
            $filtro_sql2 = " AND c.id in ($filtro)";
        } else {
            $filtro_sql = "";
        }

        //para la primera carga
        $filtro_Averias = "and eecc_final in(" . $empresa . ")";
        if ($liquidado == "1") {
            $filtro_liquidado = "AND e.estado<>'Liquidado' and a.eecc_final in(" . $empresa . ")";
        } else {
            $filtro_liquidado = "and a.eecc_final in(" . $empresa . ")";
        }


        $sql = "SELECT * FROM(
	SELECT * FROM(
			
				SELECT c.id,a.averia,id_atc,tipo_actividad,a.nombre_cliente 'nombre',a.fecha_registro 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado,e.id AS 'codigo_estado',flag_tecnico,a.tipo_averia 'tipo_averia',a.horas_averia,a.fecha_registro,
				a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf 'mdf',a.observacion_102,a.segmento,
				a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl,
				a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias,
				a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal 'zonal',a.quiebre,a.lejano,
				a.distrito,a.eecc_final 'eecc_final',a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1,
				a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
				a.sms2,a.area2,c.fecha_creacion,a.microzona,mov.tecnico, c.nmov,
				(CASE aver.averia WHEN '' THEN '' ELSE aver.averia END) 'existe','' AS 'actuacion',
				(SELECT fecha_cambio FROM webpsi_coc.averias_criticos_final_historico WHERE averia=a.averia) fecha_cambio, 
				(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`averias_criticos_final_historico` WHERE averia=a.averia),NOW()) / 3600),2)) AS `horas_cambio`
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.averia),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_averia a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id LEFT JOIN
				schedulle_sistemas.pen_pais_total aver ON aver.averia=a.averia
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id)  
				$filtro_sql 
				order by a.fecha_registro asc	
			
		)AS agendas 
		
		UNION ALL
		SELECT * FROM(
			
				SELECT  c.id,a.codigo_req AS 'averia',id_atc,tipo_actividad,a.nomcliente 'nombre',fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado AS 'estado',e.id AS 'codigo_estado',
				flag_tecnico,a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',a.
				estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',a.
				veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',a.
				tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.
				quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',a.
				fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
				a.area2,c.fecha_creacion AS 'fecha_creacion',a.microzona,mov.tecnico, c.nmov,
				IFNULL((select tmp.DATA17 FROM schedulle_sistemas.tmp_gaudi_total tmp where tmp.DATA17=a.codigo_req limit 1 ),'') 'existe',
				a.tipo_actuacion AS 'actuacion'
				,(SELECT fecha_cambio FROM webpsi_coc.tmp_provision_historico WHERE codigo_req=a.codigo_req) fecha_cambio
				,(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`tmp_provision_historico` WHERE codigo_req=a.codigo_req),NOW()) / 3600),2)) AS `horas_cambio`
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.codigo_req),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_provision a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id)  
				$filtro_sql2
				order by fecha_registro asc
			
		)AS provision 
		
		UNION ALL
		SELECT * FROM(
			
                select '' as 'id',a.averia,'' as 'id_atc','Averias' as 'tipo_actividad',a.nombre_cliente 'nombre',a.fecha_registro as 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
                '' as 'fecha_agenda','' as 'horario','' as 'motivo','' as 'submotivo','' as 'm_id','' as 's_id','Temporal' as 'estado','Temporal' as 'codigo_estado',
                '' as 'flag_tecnico',a.tipo_averia,a.horas_averia,a.fecha_registro,a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf,a.observacion_102,a.segmento,
                a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl,
                a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias,
                a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.quiebre,a.lejano,
                a.distrito,a.eecc_final,a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1,
                a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
                a.sms2,a.area2,'' AS 'fecha_creacion',a.microzona,'' as 'tecnico', 0 nmov,'' as 'existe','' as 'actuacion'
                ,acfh.fecha_cambio
				,ROUND((TIMESTAMPDIFF(SECOND,acfh.fecha_cambio,NOW()) / 3600),2) horas_cambio
                ,IFNULL(a.wu_nagendas,'0') wu_nagendas ,'' n_evento
                ,null estado_evento
				from webpsi_coc.averias_criticos_final a
				left join webpsi_coc.averias_criticos_final_historico acfh on a.averia=acfh.averia
				where a.averia not in (select averia from webpsi_criticos.gestion_averia)
                and a.averia NOT IN (SELECT averia FROM webpsi_criticos.gestion_rutina_manual)
                ".str_replace($buscar,$reemplazar,$filtro_sql)."                               
        		ORDER by fecha_registro asc                

		)as averias_final
		
		UNION ALL
		SELECT * FROM(

				SELECT '' AS 'id',a.codigo_req AS 'averia','' AS 'id_atc','Provision' AS 'tipo_actividad',a.nomcliente 'nombre',a.fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				'' AS 'fecha_agenda','' AS 'horario','' AS 'motivo','' AS 'submotivo','' AS 'm_id','' AS 's_id','Temporal' AS 'estado','Temporal' AS 'codigo_estado',
				'' AS 'flag_tecnico',a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',
				a.estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',
				a.veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',
				a.tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,
				a.quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',
				a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
				a.sms2,a.area2,'' AS 'fecha_creacion',a.microzona,'' as 'tecnico', 0 nmov,'' as 'existe',a.tipo_actuacion as 'actuacion'
				,tph.fecha_cambio 
				,ROUND((TIMESTAMPDIFF(SECOND,tph.fecha_cambio,NOW()) / 3600),2) AS `horas_cambio`
			  	,IFNULL(a.wu_nagendas,'0') wu_nagendas,'' n_evento
				,null	estado_evento					
				FROM webpsi_coc.tmp_provision a
				LEFT JOIN webpsi_coc.tmp_provision_historico tph ON (a.codigo_req=tph.codigo_req)				
				WHERE a.codigo_req NOT IN (SELECT codigo_req FROM webpsi_criticos.gestion_provision)  
				and   a.codigo_req NOT IN (SELECT codigo_req FROM webpsi_criticos.gestion_rutina_manual_provision) 	
				".str_replace($buscar,$reemplazar,$filtro_sql2)."				
				order by fecha_registro asc

		)as provision_final
		
		UNION ALL
		SELECT * FROM(
				SELECT c.id,'' as averia,id_atc,tipo_actividad,a.nombre_cliente 'nombre',a.fecha_registro 'fecha_reg',a.quiebre 'quiebres',
				a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico', c.fecha_agenda,h.horario,m.motivo,s.submotivo,
				m.id 'm_id',s.id 's_id',e.estado,e.id as 'codigo_estado',flag_tecnico,a.tipo_averia 'tipo_averia',a.horas_averia,
				a.fecha_registro, a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf 'mdf',a.observacion_102,
				a.segmento, a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl, 
				a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias, a.fftt,a.llave,
				a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal 'zonal',a.quiebre,a.lejano, a.distrito,a.eecc_final 'eecc_final',
				a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1, a.fecha_data_fuente,a.telefono_codclientecms,
				a.rango_dias,a.sms1, a.sms2,a.area2,c.fecha_creacion,a.microzona,mov.tecnico, c.nmov,
				'' as 'existe','' as 'actuacion','' as 'fecha_cambio','' as 'horas_cambio'
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.averia),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c 
				inner join webpsi_criticos.gestion_movimientos mov on c.id=mov.id_gestion
				 inner join webpsi_criticos.gestion_rutina_manual a on c.id=a.id_gestion 
				inner join webpsi_criticos.horarios h on c.id_horario=h.id 
				inner join webpsi_criticos.motivos m on c.id_motivo=m.id 
				inner join webpsi_criticos.submotivos s on c.id_submotivo=s.id 
				inner join webpsi_criticos.estados e on c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2 
											WHERE mov2.id_gestion=c.id)  
				$filtro_sql
				order by a.fecha_registro asc
		)AS rutina_manual
		
		UNION ALL
		SELECT * FROM(
			
				SELECT  c.id,a.codigo_req AS 'averia',id_atc,tipo_actividad,a.nomcliente 'nombre',fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado AS 'estado',e.id AS 'codigo_estado',
				flag_tecnico,a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',a.
				estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',a.
				veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',a.
				tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.
				quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',a.
				fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
				a.area2,c.fecha_creacion AS 'fecha_creacion',a.microzona,mov.tecnico, c.nmov,
				IFNULL((select tmp.DATA17 FROM schedulle_sistemas.tmp_gaudi_total tmp where tmp.DATA17=a.codigo_req limit 1 ),'') 'existe',
				a.tipo_actuacion AS 'actuacion'
				,(SELECT fecha_cambio FROM webpsi_coc.tmp_provision_historico WHERE codigo_req=a.codigo_req) fecha_cambio
				,(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`tmp_provision_historico` WHERE codigo_req=a.codigo_req),NOW()) / 3600),2)) AS `horas_cambio`
			 	,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.codigo_req),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_rutina_manual_provision a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id)   
				$filtro_sql2
				order by fecha_registro asc
			
		)AS rutina_manual_provision
		
	)AS T1 ";

        //echo $sql;
        $arr = array();
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function getGestionCriticosFiltro($cnx, $data, $liquidado, $carga_ini, $nuevoCritico = "") {

        $cnx->exec("set names utf8");
        $filtro_sql = "";

        if ($data["actividad"] != "") {
            //si en actividad existe alta o rutina lo separamos para el campo
            //tipo actuacion
            $alta = strpos($data["actividad"], "'ALTA'");
            $rutina = strpos($data["actividad"], "'RUTINA'");
            $manual = strpos($data["actividad"], "'MANUAL'");
            $avers = strpos($data["actividad"], "'Averias'");
            $provs = strpos($data["actividad"], "'Provision'");
            $manual_provs = strpos($data["actividad"], "'Manual_Provision'");

            $comodin_averias = ($avers !== false) ? "''," : ""; //para que filtre al tipo_actividad vacio

            $pos = "";
            if (($alta !== false && $rutina !== false && $manual !== false) || ($alta !== false && $rutina === false && $manual === false)) {
                //si los dos estan solo uso altas ya q es el primer item

                $pos = $alta - 1;
                $actuacion = substr($data["actividad"], $pos, strlen($data["actividad"]));
                $actuacion = $actuacion . ",'TRASLADO'";
                if ($filtro_sql != "") {
                    $filtro_sql .= " and actuacion in(" . $comodin_averias . $actuacion . ")";
                } else {
                    $filtro_sql .= "where actuacion in(" . $comodin_averias . $actuacion . ")";
                }
            } else if ($alta === false && $rutina !== false && $manual === false) {
                $pos = $rutina - 1;
                $actuacion = substr($data["actividad"], $pos, strlen($data["actividad"]));
                if ($filtro_sql != "") {
                    $filtro_sql .= " and actuacion in(" . $comodin_averias . $actuacion . ")";
                } else {
                    $filtro_sql .= "where actuacion in(" . $comodin_averias . $actuacion . ")";
                }
            } else if ($alta === false && $rutina === false && $manual !== false) {
                $pos = $manual - 1;
                $actuacion = substr($data["actividad"], $pos, strlen($data["actividad"]));
                if ($filtro_sql != "") {
                    $filtro_sql .= " and actuacion in(" . $comodin_averias . $actuacion . ")";
                } else {
                    $filtro_sql .= "where actuacion in(" . $comodin_averias . $actuacion . ")";
                }
            }

            /* if($pos!=""){
              //para obiar la anterior coma
              $pos = $pos -1;
              $tipo_actividad = substr($data["actividad"],0,$pos);

              //si no esta marcado Provision que para el tipo de actividad la agrego
              $comodin_prov = ($provs===false)? "'Provision',":"";//para que filtre al tipo_actividad Provision sin ser marcado
              }else{ */
            $tipo_actividad = $data["actividad"];
            if ($tipo_actividad == "") {
                $tipo_actividad = "'Provision'";
            } else {
                $comodin_prov = ($provs === false) ? "'Provision'," : "";
            }
            //}

            if ($data["actividad"] == "'Averias'" || $data["actividad"] == "'Manual'" || $data["actividad"] == "'Averias','Manual'") {//si solo marcan averias
                $comodin_prov = "";
            }

            if ($filtro_sql != "") {
                $filtro_sql .= " and tipo_actividad in(" . $comodin_prov . $tipo_actividad . ")";
            } else {
                $filtro_sql .= "where tipo_actividad in(" . $comodin_prov . $tipo_actividad . ")";
            }
        }

        if ($data["zonal"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and zonal in(" . $data["zonal"] . ")";
            } else {
                $filtro_sql .= "where zonal in(" . $data["zonal"] . ")";
            }
        }

        if ($data["microzona"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and microzona in(" . $data["microzona"] . ")";
            } else {
                $filtro_sql .= "where microzona in(" . $data["microzona"] . ")";
            }
        }

        if ($carga_ini == 1) {
            $filtro_sql .= "where area2 in('','EN CAMPO','CATV EN CAMPO')";
            //$filtro_sql .= "where tipo_actividad='Provision'";
        } else {
            if ($data["area2"] != "") {
                if ($filtro_sql != "") {
                    $filtro_sql .= " and area2 in(" . $data["area2"] . ")";
                } else {
                    $filtro_sql .= "where area2 in(" . $data["area2"] . ")";
                }
            }
        }

        if ($carga_ini == 1) {
            $filtro_sql .= "and lejano in('CRITICOS')";
        } else {
            if ($data["lejano"] != "") {
                if ($filtro_sql != "") {
                    $filtro_sql .= " and lejano in(" . $data["lejano"] . ")";
                } else {
                    $filtro_sql .= "where lejano in(" . $data["lejano"] . ")";
                }
            }
        }

        if ($data["negocio"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and tipo_averia in(" . $data["negocio"] . ")";
            } else {
                $filtro_sql .= "where tipo_averia in(" . $data["negocio"] . ")";
            }
        }

        if ($data["mdf"] != "" && $data["nodo"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and mdf in(" . $data["mdf"] . "," . $data["nodo"] . ")";
            } else {
                $filtro_sql .= "where mdf in(" . $data["mdf"] . "," . $data["nodo"] . ")";
            }
        } else if ($data["mdf"] != "" && $data["nodo"] == "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and mdf in(" . $data["mdf"] . ")";
            } else {
                $filtro_sql .= "where mdf in(" . $data["mdf"] . ")";
            }
        } else if ($data["mdf"] == "" && $data["nodo"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and mdf in(" . $data["nodo"] . ")";
            } else {
                $filtro_sql .= "where mdf in(" . $data["nodo"] . ")";
            }
        }

        if ($data["estado"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and codigo_estado in(" . $data["estado"] . ")";
            } else {
                $filtro_sql .= "where codigo_estado in(" . $data["estado"] . ")";
            }
        }

        if ($data["flags"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and flag_tecnico in(" . $data["flags"] . ")";
            } else {
                $filtro_sql .= "where flag_tecnico in(" . $data["flags"] . ")";
            }
        }

        if ($data["tecnico"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and tecnico in(" . $data["tecnico"] . ")";
            } else {
                $filtro_sql .= "where tecnico in(" . $data["tecnico"] . ")";
            }
        }

        if ($data["quiebre"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and (quiebres in(" . $data["quiebre"] . ") or (quiebres='F_PL' AND id_atc!='' ) )";
            } else {
                $filtro_sql .= "where quiebres in(" . $data["quiebre"] . ")";
            }
        }

        if ($data["area_"] != "") {
            if ($filtro_sql != "") {
                $filtro_sql .= " and area_ in(" . $data["area_"] . ")";
            } else {
                $filtro_sql .= "where area_ in(" . $data["area_"] . ")";
            }
        }

        if ($data["movistar1"] != "") {
            $pos = strpos($data["movistar1"], ",");
            if ($filtro_sql != "") {
                if ($pos !== false) {//si es array
                    $mov1 = explode(",", $data["movistar1"]);
                    $filtro_sql .= " and (averia_m1 is null or averia_m1=$mov1[1] or averia_m1='')";
                } else {
                    if ($data["movistar1"] == "'MOVISTAR UNO'") {
                        $filtro_sql .= " and averia_m1=" . $data["movistar1"];
                    } else {
                        $filtro_sql .= " and (averia_m1 is null  or averia_m1='')";
                    }
                }
            } else {
                if ($pos !== false) {//si es array
                    $mov1 = explode(",", $data["movistar1"]);
                    $filtro_sql .= " where (averia_m1 is null or averia_m1=$mov1[1] or averia_m1='')";
                } else {
                    if ($data["movistar1"] == "'MOVISTAR UNO'") {
                        $filtro_sql .= " where averia_m1=" . $data["movistar1"];
                    } else {
                        $filtro_sql .= " where (averia_m1 is null or averia_m1='')";
                    }
                }
            }
        }

        if ($data['distrito'] != '') {
            $distrito = str_replace(",", "','", $data["distrito"]);
            if ($filtro_sql != "") {
                $filtro_sql .= " and distrito in ('" . $distrito . "')";
            } else {
                $filtro_sql .= " where distrito in ('" . $distrito . "')";
            }
        }

        $esttransmision = $data['esttransmision'];
        $null = "";
        if ($data['esttransmision'] != '' and $data['esttransmision'] != 'null') {
            $esttransmision = str_replace("null,", "", $data["esttransmision"]);
            if ($esttransmision != $data['esttransmision']) {
                $null = " or estado_evento is null";
            }
            $esttransmision = str_replace(",", "','", $esttransmision);

            if ($filtro_sql != "") {
                $filtro_sql .= " and (estado_evento in ('" . $esttransmision . "')" . $null . ") ";
            } else {
                $filtro_sql .= " where (estado_evento in ('" . $esttransmision . "')" . $null . ") ";
            }
        } elseif ($data['esttransmision'] == 'null') {
            if ($filtro_sql != "") {
                $filtro_sql .= " and estado_evento is null ";
            } else {
                $filtro_sql .= " where estado_evento is null ";
            }
        }

        //para la fecha fin
        if ($data["fecha_ini"] != "" && $data["fecha_fin"] != "") {

            $fecha_f = date($data["fecha_fin"]);
            $fec_fin = new DateTime($fecha_f);
            $fec_fin->add(new DateInterval('P1D'));
            $fecha_res_fin = $fec_fin->format('Y-m-d');

            if ($filtro_sql != "") {
                $filtro_sql .= " and fecha_reg BETWEEN '" . $data["fecha_ini"] . "' and '" . $fecha_res_fin . "'";
            } else {
                $filtro_sql .= "where fecha_reg BETWEEN '" . $data["fecha_ini"] . "' and '" . $fecha_res_fin . "'";
            }
        }

        //echo $filtro_sql;
        //para filtrar estado temporal sobre toda la consulta
        //para la primera carga
        if ($liquidado == "1") {
            $filtro_sql .= " and estado<>'Temporal'";
        } else {
            $filtro_sql = $filtro_sql;
        }

        if ($data["empresa"] != "") {
        	$filtro_Averias = "and eecc_final in(" . $data["empresa"] . ")";
            $filtro_liquidado = "and a.eecc_final in(" . $data["empresa"] . ")";
            if ($liquidado == "1") {                
                //$filtro_estado = "and a.eecc_final in(".$data["empresa"].")";
                if ($filtro_sql != "") {
                    $filtro_estado = "AND codigo_estado NOT IN(3,19,4,5,6,21)";
                } else {
                    $filtro_estado = "where codigo_estado NOT IN(3,19,4,5,6,21)";
                }
            } else {
                $filtro_estado = "";
            }
        }
        /* if($data["empresa"]=="TDP"){
          $filtro_Averias="";
          if($liquidado=="1"){
          $filtro_liquidado = "AND e.estado not in('Liquidado','Cancelado','Devuelto')";
          }else{
          $filtro_liquidado = "";
          }
          }else if($data["empresa"]!="TDP" && $data["empresa"]!=""){
          $filtro_Averias="and eecc_final in(".$data["empresa"].")";
          if($liquidado=="1"){
          $filtro_liquidado = "AND e.estado<>'Liquidado' and a.eecc_final in(".$data["empresa"].")";
          }else{
          $filtro_liquidado = "and a.eecc_final in(".$data["empresa"].")";
          }
          }else{
          $filtro_Averias="";
          if($liquidado=="1"){
          //$filtro_liquidado = "AND e.estado<>'Liquidado'";
          $filtro_liquidado = "AND e.estado not in('Liquidado','Cancelado','Devuelto')";
          }else{
          $filtro_liquidado = "";
          }
          } */

        $filtroNuevoCtc = "";
        //Nuevos Casos Criticos
        if ($nuevoCritico === "averia") {
            $filtro_sql = "";
            $filtro_estado = "";
            $filtroNuevoCtc = " HAVING tipo_actividad='Averias' AND averia IN ( SELECT averia FROM webpsi_coc.averias_criticos_final_nuevos  ) ";
        }
        if ($nuevoCritico === "provision") {
            $filtro_sql = "";
            $filtro_estado = "";
            $filtroNuevoCtc = " HAVING tipo_actividad='Provision' AND averia IN ( SELECT codigo_req FROM webpsi_coc.tmp_provision_nuevos  ) ";
        }

        $sql = "SELECT * FROM(
	SELECT * FROM(
			
				SELECT c.id,a.averia,id_atc,tipo_actividad,a.nombre_cliente 'nombre',a.fecha_registro 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado,e.id AS 'codigo_estado',flag_tecnico,a.tipo_averia 'tipo_averia',a.horas_averia,a.fecha_registro,
				a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf 'mdf',a.observacion_102,a.segmento,
				a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl,
				a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias,
				a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal 'zonal',a.quiebre,a.lejano,
				a.distrito,a.eecc_final 'eecc_final',a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1,
				a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
				a.sms2,a.area2,c.fecha_creacion,a.microzona,mov.tecnico, c.nmov,
				(CASE aver.averia WHEN '' THEN '' ELSE aver.averia END) 'existe','' AS 'actuacion',
				(SELECT fecha_cambio FROM webpsi_coc.averias_criticos_final_historico WHERE averia=a.averia) fecha_cambio, 
				(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`averias_criticos_final_historico` WHERE averia=a.averia),NOW()) / 3600),2)) AS `horas_cambio`
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.averia),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_averia a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id LEFT JOIN
				schedulle_sistemas.pen_pais_total aver ON aver.averia=a.averia
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id) $filtro_liquidado 
				order by a.fecha_registro asc
			
		
			
		)AS agendas
		UNION ALL
		SELECT * FROM(
			
				SELECT  c.id,a.codigo_req AS 'averia',id_atc,tipo_actividad,a.nomcliente 'nombre',fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado AS 'estado',e.id AS 'codigo_estado',
				flag_tecnico,a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',a.
				estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',a.
				veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',a.
				tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.
				quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',a.
				fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
				a.area2,c.fecha_creacion AS 'fecha_creacion',a.microzona,mov.tecnico, c.nmov,
				IFNULL((select tmp.DATA17 FROM schedulle_sistemas.tmp_gaudi_total tmp where tmp.DATA17=a.codigo_req limit 1 ),'') 'existe',
				a.tipo_actuacion AS 'actuacion'
				,(SELECT fecha_cambio FROM webpsi_coc.tmp_provision_historico WHERE codigo_req=a.codigo_req) fecha_cambio
				,(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`tmp_provision_historico` WHERE codigo_req=a.codigo_req),NOW()) / 3600),2)) AS `horas_cambio`
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.codigo_req),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_provision a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id) $filtro_liquidado 
				order by fecha_registro asc
			
		)AS provision
		UNION ALL
		SELECT * FROM(
			
                select '' as 'id',a.averia,'' as 'id_atc','Averias' as 'tipo_actividad',a.nombre_cliente 'nombre',a.fecha_registro as 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
                '' as 'fecha_agenda','' as 'horario','' as 'motivo','' as 'submotivo','' as 'm_id','' as 's_id','Temporal' as 'estado','Temporal' as 'codigo_estado',
                '' as 'flag_tecnico',a.tipo_averia,a.horas_averia,a.fecha_registro,a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf,a.observacion_102,a.segmento,
                a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl,
                a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias,
                a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.quiebre,a.lejano,
                a.distrito,a.eecc_final,a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1,
                a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
                a.sms2,a.area2,'' AS 'fecha_creacion',a.microzona,'' as 'tecnico', 0 nmov,'' as 'existe','' as 'actuacion'
                ,acfh.fecha_cambio
				,ROUND((TIMESTAMPDIFF(SECOND,acfh.fecha_cambio,NOW()) / 3600),2) horas_cambio
                ,IFNULL(a.wu_nagendas,'0') wu_nagendas ,'' n_evento
                ,null estado_evento
				from webpsi_coc.averias_criticos_final a
				left join webpsi_coc.averias_criticos_final_historico acfh on a.averia=acfh.averia
				where a.averia not in (select averia from webpsi_criticos.gestion_averia)
                and a.averia NOT IN (SELECT averia FROM webpsi_criticos.gestion_rutina_manual) 
                $filtro_liquidado
                 order by fecha_registro asc

		)as averias_final
		UNION ALL
		SELECT * FROM(

				SELECT '' AS 'id',a.codigo_req AS 'averia','' AS 'id_atc','Provision' AS 'tipo_actividad',a.nomcliente 'nombre',a.fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				'' AS 'fecha_agenda','' AS 'horario','' AS 'motivo','' AS 'submotivo','' AS 'm_id','' AS 's_id','Temporal' AS 'estado','Temporal' AS 'codigo_estado',
				'' AS 'flag_tecnico',a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',
				a.estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',
				a.veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',
				a.tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,
				a.quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',
				a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,
				a.sms2,a.area2,'' AS 'fecha_creacion',a.microzona,'' as 'tecnico', 0 nmov,'' as 'existe',a.tipo_actuacion as 'actuacion'
				,tph.fecha_cambio 
				,ROUND((TIMESTAMPDIFF(SECOND,tph.fecha_cambio,NOW()) / 3600),2) AS `horas_cambio`
			  	,IFNULL(a.wu_nagendas,'0') wu_nagendas,'' n_evento
				,null	estado_evento					
				FROM webpsi_coc.tmp_provision a
				LEFT JOIN webpsi_coc.tmp_provision_historico tph ON (a.codigo_req=tph.codigo_req)				
				WHERE a.codigo_req NOT IN (SELECT codigo_req FROM webpsi_criticos.gestion_provision)  
				and   a.codigo_req NOT IN (SELECT codigo_req FROM webpsi_criticos.gestion_rutina_manual_provision) 
				$filtro_liquidado
				order by fecha_registro asc

		)as provision_final
		UNION ALL
		SELECT * FROM(
				SELECT c.id,'' as averia,id_atc,tipo_actividad,a.nombre_cliente 'nombre',a.fecha_registro 'fecha_reg',a.quiebre 'quiebres',
				a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico', c.fecha_agenda,h.horario,m.motivo,s.submotivo,
				m.id 'm_id',s.id 's_id',e.estado,e.id as 'codigo_estado',flag_tecnico,a.tipo_averia 'tipo_averia',a.horas_averia,
				a.fecha_registro, a.ciudad,a.averia 'codigo_averia',a.inscripcion,a.fono1,a.telefono,a.mdf 'mdf',a.observacion_102,
				a.segmento, a.area_,a.direccion_instalacion,a.codigo_distrito,a.nombre_cliente,a.orden_trabajo,a.veloc_adsl, 
				a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,a.total_averias, a.fftt,a.llave,
				a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal 'zonal',a.quiebre,a.lejano, a.distrito,a.eecc_final 'eecc_final',
				a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1, a.fecha_data_fuente,a.telefono_codclientecms,
				a.rango_dias,a.sms1, a.sms2,a.area2,c.fecha_creacion,a.microzona,mov.tecnico, c.nmov,
				'' as 'existe','' as 'actuacion','' as 'fecha_cambio','' as 'horas_cambio'
				,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.averia),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c 
				inner join webpsi_criticos.gestion_movimientos mov on c.id=mov.id_gestion
				 inner join webpsi_criticos.gestion_rutina_manual a on c.id=a.id_gestion 
				inner join webpsi_criticos.horarios h on c.id_horario=h.id 
				inner join webpsi_criticos.motivos m on c.id_motivo=m.id 
				inner join webpsi_criticos.submotivos s on c.id_submotivo=s.id 
				inner join webpsi_criticos.estados e on c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) FROM webpsi_criticos.gestion_movimientos mov2 
				WHERE mov2.id_gestion=c.id) $filtro_Averias 
				order by a.fecha_registro asc
		)AS rutina_manual
		UNION ALL
		SELECT * FROM(
			
				SELECT  c.id,a.codigo_req AS 'averia',id_atc,tipo_actividad,a.nomcliente 'nombre',fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.eecc_final 'empresa',a.telefono_codclientecms 'telefono_cliente_critico',
				c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id 'm_id',s.id 's_id',e.estado AS 'estado',e.id AS 'codigo_estado',
				flag_tecnico,a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
				a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',a.
				estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',a.
				veloc_adsl,a.servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',a.
				tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.
				quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',a.
				fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
				a.area2,c.fecha_creacion AS 'fecha_creacion',a.microzona,mov.tecnico, c.nmov,
				IFNULL((select tmp.DATA17 FROM schedulle_sistemas.tmp_gaudi_total tmp where tmp.DATA17=a.codigo_req limit 1 ),'') 'existe',
				a.tipo_actuacion AS 'actuacion'
				,(SELECT fecha_cambio FROM webpsi_coc.tmp_provision_historico WHERE codigo_req=a.codigo_req) fecha_cambio
				,(SELECT ROUND((TIMESTAMPDIFF(SECOND,(SELECT fecha_cambio FROM webpsi_coc.`tmp_provision_historico` WHERE codigo_req=a.codigo_req),NOW()) / 3600),2)) AS `horas_cambio`
			 	,IFNULL((SELECT wu_nagendas FROM webpsi_coc.averias_criticos_final WHERE averia=a.codigo_req),IF(IFNULL(a.wu_nagendas,'')='',0,a.wu_nagendas)) wu_nagendas ,c.n_evento
				,(select paso from webpsi_officetrack.tareas where task_id=c.id order by fecha_recepcion desc limit 1) estado_evento
				FROM webpsi_criticos.gestion_criticos c INNER JOIN webpsi_criticos.gestion_movimientos mov ON c.id=mov.id_gestion INNER JOIN
				webpsi_criticos.gestion_rutina_manual_provision a ON c.id=a.id_gestion INNER JOIN 
				webpsi_criticos.horarios h ON c.id_horario=h.id INNER JOIN 
				webpsi_criticos.motivos m ON c.id_motivo=m.id INNER JOIN 
				webpsi_criticos.submotivos s ON c.id_submotivo=s.id INNER JOIN 
				webpsi_criticos.estados e ON c.id_estado=e.id 
				where mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) 
											FROM webpsi_criticos.gestion_movimientos mov2
											WHERE mov2.id_gestion=c.id) $filtro_Averias  
				order by fecha_registro asc
			
		)AS rutina_manual_provision
	)AS T1 $filtro_sql $filtro_estado $filtroNuevoCtc";


		//echo $sql;
        $arr = array();
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function getGestionCritico($cnx, $id) {

        $cnx->exec("set names utf8");
        $id = trim($id);
        //faltan borrar campos en el select al final lo borras
        $sql = "SELECT a.zonal, c.id,a.eecc_final,id_atc,nombre_cliente_critico 'nombre',telefono_cliente_critico,celular_cliente_critico,
				fecha_agenda,observacion,h.horario,m.motivo,s.submotivo,c.id_estado,e.estado,flag_tecnico,a.quiebre,c.n_evento,a.paquete 
				FROM
				webpsi_criticos.gestion_criticos c,
				webpsi_criticos.gestion_averia a,
				webpsi_criticos.horarios h,
				webpsi_criticos.motivos m,
				webpsi_criticos.submotivos s,
				webpsi_criticos.estados e
				where c.id_horario=h.id and c.id_motivo=m.id and c.id_submotivo=s.id
				and c.id_estado=e.id and a.id_gestion=c.id and c.id=$id
				order by c.fecha_creacion desc";

        $arr = array();
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $cnx = NULL;
        $db = NULL;
        return $row;
    }

	public function getGestionCriticoProvision($cnx,$id){
		
		$cnx->exec("set names utf8");
		$id = trim($id);
		//faltan borrar campos en el select al final lo borras
		$sql = "SELECT p.zonal,c.id,p.eecc_final,id_atc,nombre_cliente_critico 'nombre',telefono_cliente_critico,celular_cliente_critico,
				fecha_agenda,observacion,h.horario,m.motivo,s.submotivo,c.id_estado,e.estado,flag_tecnico,p.quiebre,c.n_evento,p.paquete 
				FROM webpsi_criticos.gestion_criticos c,webpsi_criticos.gestion_provision p, webpsi_criticos.horarios h, webpsi_criticos.motivos m, webpsi_criticos.submotivos s,
				webpsi_criticos.estados e where c.id_horario=h.id and c.id_motivo=m.id and c.id_submotivo=s.id 
				and c.id_estado=e.id and p.id_gestion=c.id and c.id=$id order by c.fecha_creacion desc";

        $arr = array();
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $cnx = NULL;
        $db = NULL;
        return $row;
    }

	public function getGestionCriticoManualProvision($cnx,$id){
		
		$cnx->exec("set names utf8");
		$id = trim($id);
		//faltan borrar campos en el select al final lo borras
		$sql = "SELECT p.zonal,c.id,p.eecc_final,id_atc,nombre_cliente_critico 'nombre',telefono_cliente_critico,celular_cliente_critico,
				fecha_agenda,observacion,h.horario,m.motivo,s.submotivo,c.id_estado,e.estado,flag_tecnico,p.quiebre,c.n_evento,p.paquete 
				FROM webpsi_criticos.gestion_criticos c,webpsi_criticos.gestion_rutina_manual_provision p, webpsi_criticos.horarios h, webpsi_criticos.motivos m, webpsi_criticos.submotivos s,
				webpsi_criticos.estados e where c.id_horario=h.id and c.id_motivo=m.id and c.id_submotivo=s.id 
				and c.id_estado=e.id and p.id_gestion=c.id and c.id=$id order by c.fecha_creacion desc";

        $arr = array();
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $cnx = NULL;
        $db = NULL;
        return $row;
    }

    public function getGestionCriticoManual($cnx, $id) {

        $cnx->exec("set names utf8");
        $id = trim($id);
        //faltan borrar campos en el select al final lo borras
        $sql = "SELECT r.zonal ,c.id,r.eecc_final,id_atc,nombre_cliente_critico 'nombre',telefono_cliente_critico,celular_cliente_critico,
				fecha_agenda,observacion,h.horario,m.motivo,s.submotivo,c.id_estado,e.estado,flag_tecnico,r.quiebre,c.n_evento,r.paquete 
				FROM webpsi_criticos.gestion_criticos c,webpsi_criticos.gestion_rutina_manual r, webpsi_criticos.horarios h, webpsi_criticos.motivos m, webpsi_criticos.submotivos s,
				webpsi_criticos.estados e where c.id_horario=h.id and c.id_motivo=m.id and c.id_submotivo=s.id 
				and c.id_estado=e.id and r.id_gestion=c.id and c.id=$id order by c.fecha_creacion desc";

        $arr = array();
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $cnx = NULL;
        $db = NULL;
        return $row;
    }

    function addClienteCritico($cnx) {

        $result = array();

        //Campos Tabla GestionCriticos
        $cr_nombre = $_POST["cr_nombre"];
        $cr_telefono = $_POST["cr_telefono"];
        $cr_celular = $_POST["cr_celular"];
        $id_horario = $_POST["horario_agenda"];
        $id_dia = $_POST["dia_agenda"];

        $observacion = htmlspecialchars($_POST["cr_observacion"]);
        $tipo_averia = $_POST["tipo_averia"];
        $horas_averia = $_POST["horas_averia"];
        //$fecha_reporte = $_POST["fecha_reporte"];
        $fecha_registro = $_POST["fecha_registro"];
        $ciudad = $_POST["ciudad"];
        $averia = $_POST["averia"];
        $inscripcion = $_POST["inscripcion"];
        $fono1 = $_POST["fono1"];
        $telefono = $_POST["telefono"];
        $mdf = $_POST["mdf"];
        $observacion_102 = $_POST["observacion_102"];
        $segmento = $_POST["segmento"];
        $area_ = $_POST["area_"];
        $direccion_instalacion = $_POST["direccion_instalacion"];
        $codigo_distrito = $_POST["codigo_distrito"];
        $nombre_cliente = $_POST["nombre_cliente"];
        $orden_trabajo = $_POST["orden_trabajo"];
        $veloc_adsl = $_POST["veloc_adsl"];
        $clase_servicio_catv = $_POST["clase_servicio_catv"];
        $codmotivo_req_catv = $_POST["codmotivo_req_catv"];
        $total_averias_cable = $_POST["total_averias_cable"];
        $total_averias_cobre = $_POST["total_averias_cobre"];
        $total_averias = $_POST["total_averias"];
        $fftt = $_POST["fftt"];
        $llave = $_POST["llave"];
        $zonal = $_POST["zonal"];

        $wu_nagendas = $_POST["wu_nagendas"];
        $wu_nmovimientos = $_POST["wu_nmovimientos"];
        $wu_fecha_ult_agenda = $_POST["wu_fecha_ult_agenda"];
        $total_llamadas_tecnicas = $_POST["total_llamadas_tecnicas"];
        $total_llamadas_seguimiento = $_POST["total_llamadas_seguimiento"];
        $llamadastec15dias = $_POST["llamadastec15dias"];
        $llamadastec30dias = $_POST["llamadastec30dias"];

        $dir_terminal = $_POST["dir_terminal"];
        $fonos_contacto = $_POST["fonos_contacto"];
        $contrata = $_POST["contrata"];
        $quiebre = $_POST["quiebre"];
        $lejano = $_POST["lejano"];
        $distrito = $_POST["distrito"];
        //$eecc_zona = $_POST["eecc_zona"];
        $eecc_zona = $_POST["eecc_final"];
        $microzona = $_POST["microzona"];
        $zona_movistar_uno = $_POST["zona_movistar_uno"];
        $paquete = $_POST["paquete"];
        $data_multiproducto = $_POST["data_multiproducto"];
        $averia_m1 = $_POST["averia_m1"];
        $fecha_data_fuente = $_POST["fecha_data_fuente"];
        $telefono_codclientecms = $_POST["telefono_codclientecms"];
        $rango_dias = $_POST["rango_dias"];
        $sms1 = $_POST["sms1"];
        $sms2 = $_POST["sms2"];
        $area2 = $_POST["area2"];
        $eecc_final = $_POST["eecc_final"];
        $tecnicos_asignados = 1;
        $id_usuario = $_POST["txt_idusuario"];
        $tecnico = $_POST["nombretecnico"];
        $idtecnico = $_POST["tecnico"];
        $flag_tecnico = $_POST["flag_tecnico"];
        $tipo_actividad = $_POST["tipo_actividad"];
        $motivo_registro = $_POST["motivo_registro"];
        $tipo_actuacion = $_POST["tipo_actuacion"];

        $datosfinal = $_POST['datosfinal'];
        $contadordatosfinal = "0";

        $tecnico = ( $tecnico == "-- Seleccione --" || $tecnico == "NINGUNO NINGUNO NINGUNO" ) ? "" : $tecnico;

        if ($datosfinal == 'OK') {
            $contadordatosfinal = "1";
        }

        $idcritico = $_GET['idcritico']; // verificar si veiene con id o no!
        if ($idcritico == '0' or $idcritico == '') {
            $idcritico = '';
        }


        if ($motivo_registro == 1) {
            $id_motivo = "1";
            $id_submotivo = "1";
        } else {
            $id_motivo = "2";
            $id_submotivo = "2";
        }

        $fecha_agenda = $_POST["fecha_agenda"]; //formato
        //obteniendo fecha actual
        $fecha = date("Y/m/d H:i:s");

        try {

            //Iniciar transaccion
            $cnx->beginTransaction();

            //validando que la averia gestionada no exista para ser ingresada
            if ($tipo_actividad == 'Provision') {
                $gestProvision = new gestionProvision();
                $res_gaveria = $gestProvision->existeGestionProvision($cnx, $_POST["averia"]);
            } else {
                $gestAverias = new gestionAveria();
                $res_gaveria = $gestAverias->existeGestionAveria($cnx, $_POST["averia"]);
            }


            //SI NO EXISTE NI EN PROVISION NI EN AVERIA
            if ($res_gaveria == "") {
                //ya que pueden haber mas de dos estados a la vez para agendado entonces
                if ($motivo_registro == 1) {
                    if ($tecnico != "" && $flag_tecnico == "si") {
                        $flag_tecnico = "Tecnico Entregado";
                        $id_estado = "1";
                    } else if ( $tecnico !=  "" && $flag_tecnico == "") {
                        $flag_tecnico = "Tecnico Asignado";
                        $id_estado = "1";
                    } else {
                        $flag_tecnico = "";
                        $id_estado = "8";
                    }
                } else {
                    if ($tecnico != "" && $flag_tecnico == "si") {
                        $flag_tecnico = "Tecnico Entregado";
                        $id_estado = "2";
                    } else if ($tecnico != "" && $flag_tecnico == "") {
                        $flag_tecnico = "Tecnico Asignado";
                        $id_estado = "2";
                    } else {
                        $flag_tecnico = "";
                        $id_estado = "2";
                    }
                }

                //Para tíldes y caracteres especiales
                $cnx->exec("set names utf8");
                if ($motivo_registro == 1) {
                    if ($averia != "" && $cr_nombre != "" && $cr_telefono != "" && $fecha_agenda != "" && $id_horario != "") {
                        $sql = "INSERT INTO webpsi_criticos.`gestion_criticos` 
				        		(
			                        id, id_atc, nombre_cliente_critico,
			                        telefono_cliente_critico, celular_cliente_critico,
			                        fecha_agenda, id_horario, id_motivo,
			                        id_submotivo, id_estado, observacion,
			                        fecha_creacion, flag_tecnico, 
			                        tipo_actividad, nmov, n_evento
			                    ) 
				        		VALUES ('$idcritico','','$cr_nombre','$cr_telefono','$cr_celular','$fecha_agenda',
				        	$id_horario,$id_motivo,$id_submotivo,$id_estado,'$observacion','$fecha','$flag_tecnico','$tipo_actividad','0','$contadordatosfinal')";
                        $cnx->exec($sql);
                    } else {
                        $result["estado"] = FALSE;
                        $result["msg"] = "Ingrese el nombre de cliente,telefono,observacion y seleccione una fecha a agendar";
                        return $result;
                    }
                } else {
                    if ($averia != "") {
                        //el 2 es para id_horario cualquier numero ya que es foranea sino no inserta
                        $sql = "INSERT INTO webpsi_criticos.`gestion_criticos` 
				    	 		(
			                        id, id_atc, nombre_cliente_critico,
			                        telefono_cliente_critico, celular_cliente_critico,
			                        fecha_agenda, id_horario, id_motivo,
			                        id_submotivo, id_estado, observacion,
			                        fecha_creacion, flag_tecnico, 
			                        tipo_actividad, nmov, n_evento
			                    ) 
				    	 		VALUES ('$idcritico','','$cr_nombre','$cr_telefono','$cr_celular','$fecha_agenda',
					        	2,$id_motivo,$id_submotivo,$id_estado,'$observacion','$fecha','$flag_tecnico','$tipo_actividad','0','$contadordatosfinal')";
                        $cnx->exec($sql);
                    } else {
                        $result["estado"] = FALSE;
                        $result["msg"] = "No se ha proporcionado un código de avería";
                        return $result;
                    }
                }


                //Obteniendo el ultimo codigo de gestio crítico
                if ($idcritico != '') {
                    $id_gestion = $idcritico;
                } else {
                    $id_gestion = $cnx->lastInsertId();
                }

                //Actualizando el codigo de Atención
                $id_atc = "ATC_" . date("Y") . "_" . $id_gestion;
                $sql = "update webpsi_criticos.gestion_criticos set id_atc='$id_atc' where id=$id_gestion";
                $cnx->exec($sql);

                //Obteniendo el id de zonal y empresa para la tabla movimientos
                $empresa = new Empresa();
                $vempresa = $eecc_zona;
                $id_empresa = $empresa->getIdEmpresa($cnx, $vempresa);
                $zonales = new Zonales();
                $id_zonal = $zonales->getIdZonal($cnx, $zonal);

                if ($id_gestion != "") {
                    if ($motivo_registro == 2) {
                        $id_horario = 2;
                        $id_dia = 2;
                    }

                    $gestMovimiento = new gestionMovimientos();
                    $gestMovimiento->addGestionMovimientos($cnx, $id_gestion, $id_empresa, $id_zonal, $fecha_agenda
                            , $id_horario, $id_dia, $id_motivo, $id_submotivo, $id_estado, $tecnicos_asignados, $observacion
                            , $id_usuario, $fecha, $tecnico, '', $idtecnico);
                } else {
                    $result["estado"] = FALSE;
                    $result["msg"] = "No se ha proporcionado un código de avería";
                    return $result;
                }

                //OBS: La contrata y zona no pueden ser vacios sino la busqueda de reserva no funcionara
                if ($id_gestion != "") {
                    if ($tipo_actividad == 'Provision') {
                        $res_age = $gestProvision->addGestionProvision($cnx, $id_gestion, $tipo_averia, $horas_averia, $fecha_registro, $ciudad, $averia, $inscripcion, $fono1, $telefono, $mdf, addslashes($observacion_102), $segmento, $area_, addslashes($direccion_instalacion), $codigo_distrito, addslashes($nombre_cliente), $orden_trabajo, $veloc_adsl, $clase_servicio_catv, $codmotivo_req_catv, $total_averias_cable, $total_averias_cobre, $total_averias, addslashes($fftt), $llave, addslashes($dir_terminal), $fonos_contacto, $contrata, $zonal, $wu_nagendas, $wu_nmovimientos, $wu_fecha_ult_agenda, $total_llamadas_tecnicas, $total_llamadas_seguimiento, $llamadastec15dias, $llamadastec30dias, $quiebre, $lejano, $distrito, $eecc_zona, addslashes($zona_movistar_uno), addslashes($paquete), addslashes($data_multiproducto), $averia_m1, $fecha_data_fuente, $telefono_codclientecms, $rango_dias, addslashes($sms1), addslashes($sms2), $area2, $tipo_actuacion, $eecc_final, $microzona);
                    } else {
                        $res_age = $gestAverias->addGestionAveria($cnx, $id_gestion, $tipo_averia, $horas_averia, '', $fecha_registro, $ciudad, $averia, $inscripcion, $fono1, $telefono, $mdf, addslashes($observacion_102), $segmento, $area_, addslashes($direccion_instalacion), $codigo_distrito, addslashes($nombre_cliente), $orden_trabajo, $veloc_adsl, $clase_servicio_catv, $codmotivo_req_catv, $total_averias_cable, $total_averias_cobre, $total_averias, addslashes($fftt), $llave, addslashes($dir_terminal), $fonos_contacto, $contrata, $zonal, $wu_nagendas, $wu_nmovimientos, $wu_fecha_ult_agenda, $total_llamadas_tecnicas, $total_llamadas_seguimiento, $llamadastec15dias, $llamadastec30dias, $quiebre, $lejano, $distrito, $eecc_zona, addslashes($zona_movistar_uno), addslashes($paquete), addslashes($data_multiproducto), $averia_m1, $fecha_data_fuente, $telefono_codclientecms, $rango_dias, addslashes($sms1), addslashes($sms2), $area2, $eecc_final, $microzona);
                    }
                }

                $cnx->commit();
                $result["estado"] = TRUE;
                $result["msg"] = "Código de Atención: " . $id_atc;
                return $result;
            } else {
                $result["estado"] = FALSE;
                $result["averia"] = $_POST["averia"];
                $result["msg"] = "La averia ya se encuentra registrada actualice su bandeja";
                return $result;
            }
        } catch (PDOException $error) {
            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            //$result["msg"] = "No se registro la actuacion";
            return $result;
            exit();
        }
    }

    public function ReAgendarAveria($cnx) {

        $result = array();

        //Campos Tabla GestionCriticos
        $cr_nombre = $_POST["cr_nombre"];
        $cr_telefono = $_POST["cr_telefono"];
        $cr_celular = $_POST["cr_celular"];
        $id_horario = $_POST["horario_agenda"];
        $id_dia = $_POST["dia_agenda"];

        $observacion = htmlspecialchars($_POST["cr_observacion"]);
        $tipo_averia = $_POST["tipo_averia"];
        $horas_averia = $_POST["horas_averia"];
        //$fecha_reporte = $_POST["fecha_reporte"];
        $fecha_registro = $_POST["fecha_registro"];
        $ciudad = $_POST["ciudad"];
        $averia = $_POST["averia"];
        $inscripcion = $_POST["inscripcion"];
        $fono1 = $_POST["fono1"];
        $telefono = $_POST["telefono"];
        $mdf = $_POST["mdf"];
        $observacion_102 = $_POST["observacion_102"];
        $segmento = $_POST["segmento"];
        $area_ = $_POST["area_"];
        $direccion_instalacion = $_POST["direccion_instalacion"];
        $codigo_distrito = $_POST["codigo_distrito"];
        $nombre_cliente = $_POST["nombre_cliente"];
        $orden_trabajo = $_POST["orden_trabajo"];
        $veloc_adsl = $_POST["veloc_adsl"];
        $clase_servicio_catv = $_POST["clase_servicio_catv"];
        $codmotivo_req_catv = $_POST["codmotivo_req_catv"];
        $total_averias_cable = $_POST["total_averias_cable"];
        $total_averias_cobre = $_POST["total_averias_cobre"];
        $total_averias = $_POST["total_averias"];
        $fftt = $_POST["fftt"];
        $llave = $_POST["llave"];
        $zonal = $_POST["zonal"];

        $wu_nagendas = $_POST["wu_nagendas"];
        $wu_nmovimientos = $_POST["wu_nmovimientos"];
        $wu_fecha_ult_agenda = $_POST["wu_fecha_ult_agenda"];
        $total_llamadas_tecnicas = $_POST["total_llamadas_tecnicas"];
        $total_llamadas_seguimiento = $_POST["total_llamadas_seguimiento"];
        $llamadastec15dias = $_POST["llamadastec15dias"];
        $llamadastec30dias = $_POST["llamadastec30dias"];

        $dir_terminal = $_POST["dir_terminal"];
        $fonos_contacto = $_POST["fonos_contacto"];
        $contrata = $_POST["contrata"];
        $quiebre = $_POST["quiebre"];
        $lejano = $_POST["lejano"];
        $distrito = $_POST["distrito"];
        //$eecc_zona = $_POST["eecc_zona"];
        $eecc_zona = $_POST["eecc_final"];
        $microzona = $_POST["microzona"];
        $zona_movistar_uno = $_POST["zona_movistar_uno"];
        $paquete = $_POST["paquete"];
        $data_multiproducto = $_POST["data_multiproducto"];
        $averia_m1 = $_POST["averia_m1"];
        $fecha_data_fuente = $_POST["fecha_data_fuente"];
        $telefono_codclientecms = $_POST["telefono_codclientecms"];
        $rango_dias = $_POST["rango_dias"];
        $sms1 = $_POST["sms1"];
        $sms2 = $_POST["sms2"];
        $area2 = $_POST["area2"];
        $eecc_final = $_POST["eecc_final"];
        $tecnicos_asignados = 1;
        $id_usuario = $_POST["txt_idusuario"];
        $tecnico = $_POST["nombretecnico"];
        $idtecnico = $_POST["tecnico"];
        $flag_tecnico = $_POST["flag_tecnico"];
        $tipo_actividad = $_POST["tipo_actividad"];
        $motivo_registro = $_POST["motivo_registro"];
        $tipo_actuacion = $_POST["tipo_actuacion"];
        $tecnico = ($tecnico == "-- Seleccione --"|| $tecnico == "NINGUNO NINGUNO NINGUNO") ? "" : $tecnico;
        $datosfinal = $_POST['datosfinal'];
        $contadordatosfinal = "0";

        if ($datosfinal == 'OK') {
            $contadordatosfinal = "1";
        }

        $idcritico = $_GET['idcritico']; // verificar si veiene con id o no!
        if ($idcritico == '0' or $idcritico == '') {
            $idcritico = '';
        }


        if ($motivo_registro == 1) {
            $id_motivo = "1";
            $id_submotivo = "1";
        } else {
            $id_motivo = "2";
            $id_submotivo = "2";
        }

        $fecha_agenda = $_POST["fecha_agenda"]; //formato
        //obteniendo fecha actual
        $fecha = date("Y/m/d H:i:s");

        try {

            //Iniciar transaccion
            // $cnx->beginTransaction();

            if ($tipo_actividad == 'Provision') {
                $gestProvision = new gestionProvision();
                $res_gprovision = $gestProvision->existeGestionProvision($cnx, $_POST["averia"]);
                $id_gestion_provision = $gestProvision->getGestionProvisionbyAveria($cnx, $_POST["averia"]);
                $id_gestion_update = $id_gestion_provision;

                $id_atc = $gestProvision->get_ID_atc_By_id_gestion($cnx , $id_gestion_update);
            } else {

                $gestAverias = new gestionAveria();
                //DEVUELVE EL ID_gestion
                $res_gaveria = $gestAverias->getGestionAveriabyAveria($cnx, $_POST["averia"]);
                $id_gestion_update = $res_gaveria;
                $id_atc = $gestAverias->get_ID_atc_By_id_gestion($cnx , $id_gestion_update);
            }





            //si existe una gestion a actualizar
            if (!empty($id_gestion_update)) {
                //ya que pueden haber mas de dos estados a la vez para agendado entonces
                if ($motivo_registro == 1) {
                    if ($tecnico != "" && $flag_tecnico == "si") {
                        $flag_tecnico = "Tecnico Entregado";
                        $id_estado = "1";
                    } else if ($tecnico != "" && $flag_tecnico == "") {
                        $flag_tecnico = "Tecnico Asignado";
                        $id_estado = "1";
                    } else {
                        $flag_tecnico = "";
                        $id_estado = "8";
                    }
                } else {
                    if ($tecnico != "" && $flag_tecnico == "si") {
                        $flag_tecnico = "Tecnico Entregado";
                        $id_estado = "2";
                    } else if ($tecnico != "" && $flag_tecnico == "") {
                        $flag_tecnico = "Tecnico Asignado";
                        $id_estado = "2";
                    } else {
                        $flag_tecnico = "";
                        $id_estado = "2";
                    }
                }

                //Para tíldes y caracteres especiales
                $cnx->exec("set names utf8");
                if ($motivo_registro == 1) {
                    if ($averia != "" && $cr_nombre != "" && $cr_telefono != "" && $fecha_agenda != "" && $id_horario != "") {

                        $sql = "UPDATE webpsi_criticos.`gestion_criticos`
                        SET nombre_cliente_critico = '$cr_nombre' ,
                        telefono_cliente_critico = '$cr_telefono',
                        celular_cliente_critico = '$cr_celular',
                        fecha_agenda = '$fecha_agenda',
                        id_horario =   $id_horario,
                        id_motivo =  $id_motivo ,
                        id_submotivo =  $id_submotivo ,
                        id_estado =   $id_estado,
                        observacion =  '$observacion' ,

                        flag_tecnico =  '$flag_tecnico' ,
                        tipo_actividad = '$tipo_actividad'  ,
                        nmov =  0 ,
                        n_evento = $contadordatosfinal
                        where id = $id_gestion_update";
                        $cnx->exec($sql);
                    } else {
                        $result["estado"] = FALSE;
                        $result["msg"] = "Ingrese el nombre de cliente,telefono,observacion y seleccione una fecha a agendar";
                        return $result;
                    }
                } else {
                    if ($averia != "") {
                        //el 2 es para id_horario cualquier numero ya que es foranea sino no inserta


                        $sql = "
                        update webpsi_criticos.`gestion_criticos` set

                        ,nombre_cliente_critico = '$cr_nombre'
                        ,telefono_cliente_critico = '$cr_telefono'
                        ,celular_cliente_critico = '$cr_celular'
                        ,fecha_agenda = '$fecha_agenda'
                        ,id_horario = 2
                        ,id_motivo = $id_motivo
                        ,id_submotivo = $id_submotivo
                        ,id_estado = $id_estado
                        ,observacion = '$observacion'
                        ,fecha_creacion = '$fecha'
                        ,flag_tecnico = '$flag_tecnico'
                        ,tipo_actividad = '$tipo_actividad'
                        ,nmov = '0'
                        ,n_evento = '$contadordatosfinal
                         where id = $id_gestion_update";

                        $cnx->exec($sql);
                    } else {
                        $result["estado"] = FALSE;
                        $result["msg"] = "No se ha proporcionado un código de avería";
                        return $result;
                    }
                }

                $id_gestion = $id_gestion_update;
                //Obteniendo el ultimo codigo de gestio crítico
                //Obteniendo el id de zonal y empresa para la tabla movimientos
                $empresa = new Empresa();
                $vempresa = $eecc_zona;
                $id_empresa = $empresa->getIdEmpresa($cnx, $vempresa);
                $zonales = new Zonales();
                $id_zonal = $zonales->getIdZonal($cnx, $zonal);


                //CUAL ES EL MOTIVO 2 ???
                if ($id_gestion != "") {
                    if ($motivo_registro == 2) {
                        $id_horario = 2;
                        $id_dia = 2;
                    }

                    $gestMovimiento = new gestionMovimientos();
                    $gestMovimiento->addGestionMovimientos($cnx, $id_gestion, $id_empresa, $id_zonal, $fecha_agenda
                            , $id_horario, $id_dia, $id_motivo, $id_submotivo, $id_estado, $tecnicos_asignados, $observacion
                            , $id_usuario, $fecha, $tecnico, '', $idtecnico);
                } else {
                    $result["estado"] = FALSE;
                    $result["msg"] = "No se ha proporcionado un código de avería";
                    return $result;
                }

                $cnx->commit();
                $result["estado"] = TRUE;
                $result["msg"] = "Se actualizo el Código de Atención: " . $id_atc;
                return $result;
            } else {
                $result["estado"] = FALSE;
                $result["averia"] = $res_gaveria;
                $result["msg"] = "No se encontro averia a actualizar";

                return $result;
            }
        } catch (PDOException $error) {

            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            //$result["msg"] = "No se registro la actuacion";
            return $result;
            exit();
        }
    }

    function addClienteCriticoPendiente($cnx, $codigos, $actividad) {

        $result = array();

        //Registrando Actuaciones como Pendientes
        $eecc_final = $_POST["empresa"];
        $tecnico = $_POST["tecnico"];
        $idtecnico = $_POST["idtecnico"];
        $observacion = $_POST["observacion"];
        $flag_tecnico = $_POST["flag_tecnico"];
        $id_motivo = "2";
        $id_submotivo = "2";
        $id_estado = "2";
        $fecha = date("Y/m/d H:i:s");
        $id_usuario = $_POST["usuario"];
        $tot_codigos = count($codigos);

        try {

            //Iniciar transaccion
            $cnx->beginTransaction();

            //ya que pueden haber mas de dos estados a la vez para agendado entonces

            if ($tecnico != "" && $flag_tecnico == "si") {
                $flag_tecnico = "Tecnico Entregado";
            } else if ($tecnico != "" && $flag_tecnico == "") {
                $flag_tecnico = "Tecnico Asignado";
            } else {
                $flag_tecnico = "";
            }

            $cnx->exec("set names utf8");
            for ($i = 0; $i < $tot_codigos; $i++) {

                //validando que la averia aun no haya sido registrada
                if ($actividad[$i] == 'Provision') {
                    $gestProvision = new gestionProvision();
                    $r_averia = $gestProvision->existeGestionProvision($cnx, $codigos[$i]);
                } else {
                    $gestAverias = new gestionAveria();
                    $r_averia = $gestAverias->existeGestionAveria($cnx, $codigos[$i]);
                }
                if ($r_averia == "") {
                    //el 2 es para id_horario cualquier numero ya que es foranea sino no inserta
                    $sql = "INSERT INTO webpsi_criticos.`gestion_criticos`
				    		(
			                        id, id_atc, nombre_cliente_critico,
			                        telefono_cliente_critico, celular_cliente_critico,
			                        fecha_agenda, id_horario, id_motivo,
			                        id_submotivo, id_estado, observacion,
			                        fecha_creacion, flag_tecnico, 
			                        tipo_actividad, nmov, n_evento
			                    ) 
			                VALUES ('','','','','','',
					      	2,$id_motivo,$id_submotivo,$id_estado,'$observacion','$fecha','$flag_tecnico','$actividad[$i]','0','0')";
                    $cnx->exec($sql);

                    $id_gestion = $cnx->lastInsertId();
                    //Actualizando el codigo de Atención
                    $id_atc = "ATC_" . date("Y") . "_" . $id_gestion;
                    $sql = "update webpsi_criticos.gestion_criticos set id_atc='$id_atc' where id=$id_gestion";
                    $cnx->exec($sql);

                    if ($actividad[$i] == 'Provision') {
                        //Para obtener la zonal y guardarla en el movimiento
                        $ob_averia = new Provision();
                        $res_aver = $ob_averia->getProvision($cnx, $codigos[$i]);
                        //Obteniendo el id de zonal y empresa para la tabla movimientos
                        $id_empresa = $eecc_final;
                        $zonales = new Zonales();
                        $id_zonal = $zonales->getIdZonal($cnx, $res_aver["zonal"]);
                    } else {
                        $ob_averia = new Averias();
                        $res_aver = $ob_averia->getAverias($cnx, $codigos[$i]);

                        //Obteniendo el id de zonal y empresa para la tabla movimientos
                        $id_empresa = $eecc_final;
                        $zonales = new Zonales();
                        $id_zonal = $zonales->getIdZonal($cnx, $res_aver["zonal"]);
                    }

                    if ($id_gestion != "") {

                        $id_horario = 2;
                        $id_dia = 2;
                        $tecnicos_asignados = 0;

                        $gestMovimiento = new gestionMovimientos();
                        $gestMovimiento->addGestionMovimientos($cnx, $id_gestion, $id_empresa, $id_zonal, ''
                                , $id_horario, $id_dia, $id_motivo, $id_submotivo, $id_estado, $tecnicos_asignados, $observacion, $id_usuario, $fecha, $tecnico, '', $idtecnico);
                    } else {
                        $result["estado"] = FALSE;
                        $result["msg"] = "No se ha regsitrado la Actuación";
                        return $result;
                    }

                    //OBS: La contrata y zona no pueden ser vacios sino la busqueda de reserva no funcionara
                    if ($id_gestion != "") {
                        if ($actividad[$i] == 'Provision') {
                            $res_age = $gestProvision->addGestionProvision($cnx, $id_gestion, $res_aver["tipo_averia"], $res_aver["horas_averia"], $res_aver["fecha_registro"], $res_aver["ciudad"], $res_aver["averia"], addslashes($res_aver["inscripcion"]), $res_aver["fono1"], $res_aver["telefono"], $res_aver["mdf"], addslashes($res_aver["observacion_102"]), $res_aver["segmento"], $res_aver["area_"], addslashes($res_aver["direccion_instalacion"]), $res_aver["codigo_distrito"], addslashes($res_aver["nombre_cliente"]), $res_aver["orden_trabajo"], $res_aver["veloc_adsl"], $res_aver["clase_servicio_catv"], $res_aver["codmotivo_req_catv"], $res_aver["total_averias_cable"], $res_aver["total_averias_cobre"], $res_aver["total_averias"], addslashes($res_aver["fftt"]), $res_aver["llave"], addslashes($res_aver["dir_terminal"]), $res_aver["fonos_contacto"], $res_aver["contrata"], $res_aver["zonal"], $res_aver["wu_nagendas"], $res_aver["wu_nmovimientos"], $res_aver["wu_fecha_ult_agenda"], $res_aver["total_llamadas_tecnicas"], $res_aver["total_llamadas_seguimiento"], $res_aver["llamadastec15dias"], $res_aver["llamadastec30dias"], $res_aver["quiebre"], $res_aver["lejano"], $res_aver["distrito"], $res_aver["eecc_zona"], addslashes($res_aver["zona_movistar_uno"]), addslashes($res_aver["paquete"]), $res_aver["data_multiproducto"], $res_aver["averia_m1"], $res_aver["fecha_data_fuente"], $res_aver["telefono_codclientecms"], $res_aver["rango_dias"], addslashes($res_aver["sms1"]), addslashes($res_aver["sms2"]), $res_aver["area2"], $res_aver["tipo_actuacion"], $res_aver["eecc_final"], $res_aver["microzona"]);
                        } else {
                            $res_age = $gestAverias->addGestionAveria($cnx, $id_gestion, $res_aver["tipo_averia"], $res_aver["horas_averia"], '', $res_aver["fecha_registro"], $res_aver["ciudad"], $res_aver["averia"], addslashes($res_aver["inscripcion"]), $res_aver["fono1"], $res_aver["telefono"], $res_aver["mdf"], addslashes($res_aver["observacion_102"]), $res_aver["segmento"], $res_aver["area_"], addslashes($res_aver["direccion_instalacion"]), $res_aver["codigo_distrito"], addslashes($res_aver["nombre_cliente"]), $res_aver["orden_trabajo"], $res_aver["veloc_adsl"], $res_aver["clase_servicio_catv"], $res_aver["codmotivo_req_catv"], $res_aver["total_averias_cable"], $res_aver["total_averias_cobre"], $res_aver["total_averias"], addslashes($res_aver["fftt"]), $res_aver["llave"], addslashes($res_aver["dir_terminal"]), $res_aver["fonos_contacto"], $res_aver["contrata"], $res_aver["zonal"], $res_aver["wu_nagendas"], $res_aver["wu_nmovimientos"], $res_aver["wu_fecha_ult_agenda"], $res_aver["total_llamadas_tecnicas"], $res_aver["total_llamadas_seguimiento"], $res_aver["llamadastec15dias"], $res_aver["llamadastec30dias"], $res_aver["quiebre"], $res_aver["lejano"], $res_aver["distrito"], $res_aver["eecc_zona"], addslashes($res_aver["zona_movistar_uno"]), addslashes($res_aver["paquete"]), $res_aver["data_multiproducto"], $res_aver["averia_m1"], $res_aver["fecha_data_fuente"], $res_aver["telefono_codclientecms"], $res_aver["rango_dias"], addslashes($res_aver["sms1"]), addslashes($res_aver["sms2"]), $res_aver["area2"], $res_aver["eecc_final"], $res_aver["microzona"]);
                        }
                    }
                } else {
                    $result["estado"] = FALSE;
                    $result["msg"] = "La averia ya se encuentra registrada actualice su bandeja";
                    return $result;
                }
            }

            $cnx->commit();
            $result["estado"] = TRUE;
            $result["msg"] = "Se registraron las Actuaciones como Pendientes correctamente";
            return $result;
        } catch (PDOException $error) {
            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            //$result["msg"] = "No se registraron los pendientes";
            return $result;
            exit();
        }
    }

    function addMovimientoCritico($cnx) {

        $result = array();

        //Campos Tabla GestionCriticos
        $id_gestion = $_POST["id_gestion"];
        $vempresa = $_POST["id_empresa"];
        $zonal = $_POST["id_zonal"];
        $id_motivo = $_POST["motivo"];
        $id_submotivo = $_POST["submotivo"];
        $id_estado = $_POST["estado"];
        $cr_nombre = $_POST["cr_nombre"];
        $cr_telefono = $_POST["cr_telefono"];
        $cr_celular = $_POST["cr_celular"];
        $observacion = $_POST["cr_observacion"];
        $id_usuario = $_POST["txt_idusuario"];
        $fecha_agenda = $_POST["fecha_agenda"];
        $tecnico = $_POST["nombretecnico"];
        $idtecnico = $_POST['tecnico'];
        $tecnico_movimiento = $_POST["nombretecnico_movimiento"];
        $idtecnico_movimiento = $_POST["tecnico_movimiento"];
        $fecha_agenda_ini = $_POST["fecha_agenda_ini"]; //para los estados que requieren llevar a agenda
        $horario_agenda_ini = $_POST["horario_agenda_ini"]; //para los estados que requieren llevar a agenda
        $ultimo_estado = $_POST["ultimo_estado"]; //para los estados que requieren llevar a agenda
        $datosfinal = $_POST['datosfinal'];
        $contadordatosfinal = "";

        if ($datosfinal == 'OK') {
            $contadordatosfinal = ", n_evento='1' ";
        }

        if ($_POST['tecnico'] == '') {
            $tecnico = $_POST['TecnicoDefault'];
            $idtecnico = $_POST['IdTecnicoDefault'];
        }
        if ($_POST['tecnico_movimiento'] == '') {
            $tecnico_movimiento = $_POST['TecnicoDefault'];
            $idtecnico_movimiento = $_POST['IdTecnicoDefault'];
        }


        if ($_POST["estado"] == "1" && $id_motivo == 1) {//ya que si el motivo es observacion saldria error
            $id_horario = $_POST["horario_agenda"];
            $id_dia = $_POST["dia_agenda"];
        } else {
            $id_horario = $_POST["horario_agenda_ini"];
            $id_dia = $_POST["dia_agenda_ini"];
        }

        if ($_POST["estado"] == "1" || $_POST["estado"] == "8" || $_POST["estado"] == "9" || $_POST["estado"] == "10" ||
                $_POST["estado"] == "20") {
            $tecnicos_asignados = 1;
        } else {
            $tecnicos_asignados = 0;
        }

        if (($id_motivo == "1" && $id_submotivo == "1" && $_POST["flag_tecnico"] == "si") ||
                ($id_motivo == "2" && $id_submotivo == "2" && $_POST["flag_tecnico"] == "si")) {
            $flag_tecnico = "Tecnico Entregado";
        } else if (($id_motivo == "1" && $id_submotivo == "1" && !isset($_POST["flag_tecnico"])) ||
                ($id_motivo == "2" && $id_submotivo == "2" && !isset($_POST["flag_tecnico"]))) {
            $flag_tecnico = "Tecnico Asignado";
        } else {
            $flag_tecnico = "";
            $tecnico = "";
        }

        if (isset($_POST["ck_penalizable"])) {
            $penalizable = $_POST["penalizable"];
        } else {
            $penalizable = "";
        }

        try {
            $fecha = date("Y/m/d H:i:s");
            //Iniciar transaccion
            $cnx->beginTransaction();

            $cnx->exec("set names utf8");

            $empresa = new Empresa();
            $id_empresa = $empresa->getIdEmpresa($cnx, $vempresa);
            $zonales = new Zonales();
            $id_zonal = $zonales->getIdZonal($cnx, $zonal);

            if ($id_gestion != "" && $id_motivo != "" && $id_submotivo != "") {
                /* codigo de estado para tecnico en sitio si eliges poner una observacion            	
                  o para el quiebre tecnico por su motivo o quiebre sistemas
                 */
                if (($_POST["estado"] == "9" || $_POST["estado"] == "10" || $_POST["estado"] == "20") ||
                        ($id_motivo == "5" && $_POST["estado"] == "9") || ($id_motivo == "5" && $_POST["estado"] == "10") ||
                        ($id_motivo == "5" && $_POST["estado"] == "20") || $id_motivo == "6" || $id_motivo == "11" || $id_motivo == "10") {

                    $gestMovimiento = new gestionMovimientos();
                    $res = $gestMovimiento->addGestionMovimientos($cnx, $id_gestion, $id_empresa, $id_zonal, $fecha_agenda_ini
                            , $horario_agenda_ini, $id_dia, $id_motivo, $id_submotivo, $id_estado, $tecnicos_asignados, $observacion, $id_usuario, $fecha, $tecnico_movimiento, '', $idtecnico_movimiento);
                } else {

                    if ($id_motivo == "5") {//para observacion todo es igual  la agenda y demas
                        $tecnico = $tecnico_movimiento;
                        $fecha_agenda = $fecha_agenda_ini;
                    }

                    if ($_POST["estado"] == "3" || $_POST["estado"] == "19") {//para registrar el tecnico tambien al liquidar
                        $tecnico = $tecnico_movimiento;
                    }
                    $gestMovimiento = new gestionMovimientos();

                    $res = $gestMovimiento->addGestionMovimientos($cnx, $id_gestion, $id_empresa, $id_zonal, $fecha_agenda
                            , $id_horario, $id_dia, $id_motivo, $id_submotivo, $id_estado, $tecnicos_asignados, $observacion, $id_usuario, $fecha, $tecnico, '', $idtecnico);
                }
            } else {
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione motivo y submotivo";
                return $result;
            }

            //para mantenimiento liquidados
            if ($_POST["estado"] == "3" || $_POST["estado"] == "19") {


                $contacto = $_POST["contacto"];
                $pruebas = $_POST["pruebas"];
                $feedback = $_POST["feedback"];
                $solucion_comercial = trim($_POST["solucion_comercial"]);
                $critico = $_POST["critico"];
                $cump_agenda = $_POST["cump_agenda"];
                $fecha_liquidacion = $_POST["h_fecha_liquidacion"];
                $fecha_liquidacion = substr($fecha_liquidacion, 6, 4) . "-" . substr($fecha_liquidacion, 3, 2) . "-" . substr($fecha_liquidacion, 0, 2) . " ";
                $estado = "1";
                $accion_liquidado = $_POST["accion_liquidado"];
                $ob_liquidado = new Liquidados();
                $res = $ob_liquidado->addLiquidados($cnx, $id_gestion, $contacto, $pruebas, $feedback, $cump_agenda, $solucion_comercial, $critico, $estado, $fecha_liquidacion, $penalizable);
            }

            if ($res) {

                if ($_POST["estado"] == "3" || $_POST["estado"] == "19") {

                    $sql = "update webpsi_criticos.gestion_criticos set fecha_agenda='$fecha_agenda',id_motivo=$id_motivo,id_submotivo=$id_submotivo,
		            id_estado=$id_estado,nombre_cliente_critico='$cr_nombre',telefono_cliente_critico='$cr_telefono',
		            celular_cliente_critico='$cr_celular',observacion='$observacion',flag_tecnico='$flag_tecnico' $contadordatosfinal
					where id=$id_gestion";
                    $cnx->exec($sql);

                    $sql_mov = "SELECT id FROM webpsi_criticos.gestion_movimientos order by id desc limit 0,1";
                    $res_mov = $cnx->query($sql_mov);
                    $row = $res_mov->fetch(PDO::FETCH_ASSOC);
                    $id_mov = $row["id"];

                    $sql2 = "update webpsi_criticos.gestion_movimientos set fecha_consolidacion='$fecha_liquidacion'
		            where id=$id_mov";
                    $cnx->exec($sql2);
                } else {
                    //para los estados que no registran movimientos
                    if ($_POST["estado"] == "9" || $_POST["estado"] == "10" || $_POST["estado"] == "20" || $id_motivo == "5" || $id_motivo == "6" || $id_motivo == "11" || $id_motivo == "10") {
                        $fecha_agenda = $fecha_agenda_ini; //ya viene preformateada no la vuelvas a formatear
                        $id_horario = $horario_agenda_ini;

                        $sql = "update webpsi_criticos.gestion_criticos set fecha_agenda='$fecha_agenda',id_horario=$id_horario,id_motivo=$id_motivo,id_submotivo=$id_submotivo,
					            id_estado=$id_estado,nombre_cliente_critico='$cr_nombre',telefono_cliente_critico='$cr_telefono',
					            celular_cliente_critico='$cr_celular',observacion='$observacion' $contadordatosfinal 					            
					            where id=$id_gestion";
                        $cnx->exec($sql);
                    } else {
                        $fecha_agenda = $fecha_agenda;
                        $id_horario = $id_horario;
                        //lo separo para que no sobreescriba el flag_tecnico
                        $sql = "update webpsi_criticos.gestion_criticos set fecha_agenda='$fecha_agenda',id_horario=$id_horario,id_motivo=$id_motivo,id_submotivo=$id_submotivo,
					            id_estado=$id_estado,nombre_cliente_critico='$cr_nombre',telefono_cliente_critico='$cr_telefono',
					            celular_cliente_critico='$cr_celular',observacion='$observacion',flag_tecnico='$flag_tecnico' $contadordatosfinal 
					            where id=$id_gestion";
                        $cnx->exec($sql);
                    }
                }
            }

            $cnx->commit();
            $result["estado"] = TRUE;
            $result["msg"] = "Movimiento Registrado Correctamente";
            return $result;
        } catch (PDOException $error) {
            $cnx->rollback();
            $result["estado"] = FALSE;
            $result["msg"] = $error->getMessage();
            //$result["msg"] = "No se pudo registrar el movimiento";
            return $result;
            exit();
        }
    }

    function getNmovGestionCriticos($cnx, $id) {

        $sql = "select nmov from webpsi_criticos.gestion_criticos where id=$id";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["nmov"];
    }

    /* function getFlagTecnicoxId($cnx,$id){

      $sql = "select flag_tecnico from webpsi_criticos.gestion_criticos where id=$id";
      //echo $sql;
      $arr = array();
      $cnx->exec("set names utf8");
      $res = $cnx->query($sql);
      $row = $res->fetch(PDO::FETCH_ASSOC);
      return $row["flag_tecnico"];

      } */

    function updateEstadoFlagCritico($cnx, $codigos, $tecnico, $estado, $flag, $idtecnico) {

        try {

            $flag_tecnico = "";
            if ($flag == "si") {
                $flag_tecnico = "Tecnico Entregado";
            } else {
                $flag_tecnico = "Tecnico Asignado";
            }

            $cnx->beginTransaction();
            $cnx->exec("set names utf8");

            if ($codigos != "" && $tecnico != "") {
                $gestMovimiento = new gestionMovimientos();
                //$gestMovimiento->updateTecnicoMovimientos($cnx,$tecnico,$codigos,$estado);
                $gestMovimiento->updateTecnicoMovimientos($cnx, $tecnico, $codigos, $idtecnico);
            } else {
                $result["estado"] = FALSE;
                $result["msg"] = "Seleccione un técnico";
                return $result;
            }

            if ($codigos != "" && $tecnico != "") {
                //$cad = "update webpsi_criticos.`gestion_criticos` set id_motivo=1,id_submotivo=1,id_estado=$estado,flag_tecnico='$flag_tecnico' where id in($codigos)";

                $cad = "update webpsi_criticos.`gestion_criticos` set flag_tecnico='$flag_tecnico' where id in($codigos)";
                $res = $cnx->exec($cad);

                //si en caso tiene como el estado sin técnico hay q actualizarlo a con técnico

                $pos = strpos($estado, ",");
                if ($pos !== false) {//si es array
                    $estado = explode(",", $estado);
                    $codigos = explode(",", $codigos);
                    $tot = sizeof($estado);
                    for ($i = 0; $i < $tot; $i++) {
                        if ($estado[$i] == "8") {
                            $cad = "update webpsi_criticos.`gestion_criticos` set id_estado=1 where id=" . $codigos[$i];
                            $res = $cnx->exec($cad);

                            //Actualizando el ultimo estado del movimiento
                            $cad1 = "SELECT max(fecha_movimiento) 'fecha' FROM webpsi_criticos.gestion_movimientos
									 group by id_gestion having id_gestion=" . $codigos[$i];
                            $res1 = $cnx->query($cad1);
                            $row = $res1->fetch(PDO::FETCH_ASSOC);
                            $fecha = $row["fecha"];

                            $cad = "update webpsi_criticos.`gestion_movimientos` set id_estado=1 where fecha_movimiento='$fecha'";
                            $res = $cnx->exec($cad);
                        }
                    }
                } else {
                    if ($estado == "8") {//estado sin tecnico
                        $cad = "update webpsi_criticos.`gestion_criticos` set id_estado=1 where id=" . $codigos;
                        $res = $cnx->exec($cad);

                        //Actualizando el ultimo estado del movimiento
                        $cad1 = "SELECT max(fecha_movimiento) 'fecha' FROM webpsi_criticos.gestion_movimientos
									 group by id_gestion having id_gestion=" . $codigos;
                        $res1 = $cnx->query($cad1);
                        $row = $res1->fetch(PDO::FETCH_ASSOC);
                        $fecha = $row["fecha"];

                        $cad = "update webpsi_criticos.`gestion_movimientos` set id_estado=1 where fecha_movimiento='$fecha'";
                        $res = $cnx->exec($cad);
                    }
                }
            }

            $cnx->commit();
            $result["estado"] = TRUE;
            $result["msg"] = "Se asigno el técnico correctamente";
            return $result;
        } catch (PDOException $error) {

            $cnx->rollback();
            $result["estado"] = FALSE;
            //$result["msg"] = $error->getMessage();
            $result["msg"] = "No se asignaron los tecnicos";
            return $result;
            exit();
        }
    }

    function getGenerarIDCritico($cnx, $id) {

        $sql = "SELECT GenerarIDGestionCritico() as id";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id"];
    }

}

?>