<?php

class gestionProvision{

	function addGestionProvision($cnx,$id_gestion,$tipo_averia,$horas_averia,$fecha_registro,$ciudad,$averia,
		            $inscripcion,$fono1,$telefono,$mdf,$observacion_102,$segmento,$area_,$direccion_instalacion,
		            $codigo_distrito,$nombre_cliente,$orden_trabajo,$veloc_adsl,$clase_servicio_catv,$codmotivo_req_catv,
		            $total_averias_cable,$total_averias_cobre,$total_averias,$fftt,$llave,$dir_terminal,$fonos_contacto,
		            $contrata,$zonal,
		            $wu_nagendas,$wu_nmovimientos,$wu_fecha_ult_agenda,$total_llamadas_tecnicas,
		            $total_llamadas_seguimiento,$llamadastec15dias,$llamadastec30dias,
		            $quiebre,$lejano,$distrito,$eecc_zona,$zona_movistar_uno,$paquete,$data_multiproducto,
		            $averia_m1,$fecha_data_fuente,$telefono_codclientecms,$rango_dias,$sms1,$sms2,
		            $area2,$tipo_actuacion,$eecc_final,$microzona){

        $sql = "INSERT INTO webpsi_criticos.`gestion_provision` VALUES ('',$id_gestion,'$tipo_averia','$horas_averia','$fecha_registro','$ciudad','$averia',
		            '$inscripcion','$fono1','$telefono','$mdf','$observacion_102','$segmento','$area_','$direccion_instalacion',
		            '$codigo_distrito','$nombre_cliente','$orden_trabajo','$veloc_adsl','$clase_servicio_catv','$codmotivo_req_catv',
		            '$total_averias_cable','$total_averias_cobre','$total_averias','$fftt','$llave','$dir_terminal','$fonos_contacto',
		            '$contrata','$zonal',
		            '$wu_nagendas','$wu_nmovimientos','$wu_fecha_ult_agenda','$total_llamadas_tecnicas',
		            '$total_llamadas_seguimiento','$llamadastec15dias','$llamadastec30dias',
		            '$quiebre','$lejano','$distrito','$eecc_zona','$zona_movistar_uno','$paquete','$data_multiproducto',
		            '$averia_m1','$fecha_data_fuente','$telefono_codclientecms','$rango_dias','$sms1','$sms2',
		            '$area2','$tipo_actuacion','$eecc_final','$microzona')";
        $res = $cnx->exec($sql);
        return $res;
        
	}

	function updateEmpresa($cnx,$vempresa,$vtecnico,$codigo,$idtecnico){

        try{

            $cnx->beginTransaction();

            if($vempresa!=""){
                $cnx->exec("set names utf8");
                $cad = "update webpsi_criticos.`gestion_provision` set eecc_final='$vempresa' where id_gestion=$codigo";//sin contrarta o zonal
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

	function getGestionProvision($cnx){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT id_gestion,a.codigo_req AS 'averia',a.nomcliente 'nombre',a.fecha_Reg AS 'fecha_reg',a.quiebre 'quiebres',a.telefono_codclientecms 'telefono_cliente_critico',
					   a.origen AS 'tipo_averia',a.horas_pedido AS 'horas_averia',a.fecha_Reg AS 'fecha_registro',a.ciudad,a.codigo_req AS 'codigo_averia',
					a.codigo_del_cliente AS 'inscripcion',a.fono1,a.telefono,a.mdf,a.obs_dev AS 'observacion_102',a.codigosegmento AS 'segmento',
					a.estacion AS 'area_',a.direccion AS 'direccion_instalacion',a.distrito AS 'codigo_distrito',a.nomcliente AS 'nombre_cliente',a.orden AS 'orden_trabajo',
					a.veloc_adsl,servicio AS 'clase_servicio_catv',a.tipo_motivo AS 'codmotivo_req_catv',a.tot_aver_cab AS 'total_averias_cable',
					a.tot_aver_cob AS 'total_averias_cobre',a.tot_averias AS total_averias,a.fftt,a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,
					a.quiebre,a.lejano,a.des_distrito AS 'distrito',a.eecc_final,a.zona_movuno AS 'zona_movistar_uno',a.paquete,a.data_multip AS 'data_multiproducto',a.aver_m1 AS 'averia_m1',
					a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.wu_fecha_ult_age as 'wu_fecha_ult_agenda',
					a.sms2,a.area2,microzona FROM webpsi_criticos.gestion_provision a, webpsi_criticos.gestion_criticos c, webpsi_criticos.estados e
				where a.id_gestion=c.id and c.id_estado=e.id and e.estado<>'Liquidado' 
				ORDER BY a.id_gestion desc";
		
		$arr = array();
		$res = $cnx->query($sql); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

	function getGestionProvisionId($cnx,$id){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT codigo_req AS 'averia',nomcliente 'nombre',fecha_Reg AS 'fecha_reg',quiebre 'quiebres',telefono_codclientecms 'telefono_cliente_critico',
					   origen AS 'tipo_averia',horas_pedido AS 'horas_averia',fecha_Reg AS 'fecha_registro',ciudad,codigo_req AS 'codigo_averia',
					codigo_del_cliente AS 'inscripcion',fono1,telefono,mdf,obs_dev AS 'observacion_102',codigosegmento AS 'segmento',
					estacion AS 'area_',direccion AS 'direccion_instalacion',distrito AS 'codigo_distrito',nomcliente AS 'nombre_cliente',orden AS 'orden_trabajo',
					veloc_adsl,servicio AS 'clase_servicio_catv',tipo_motivo AS 'codmotivo_req_catv',tot_aver_cab AS 'total_averias_cable',
					tot_aver_cob AS 'total_averias_cobre',tot_averias AS total_averias,fftt,llave,dir_terminal,fonos_contacto,contrata,zonal,
					quiebre,lejano,des_distrito AS 'distrito',eecc_final,zona_movuno AS 'zona_movistar_uno',paquete,data_multip AS 'data_multiproducto',aver_m1 AS 'averia_m1',
					fecha_data_fuente,telefono_codclientecms,rango_dias,sms1,wu_fecha_ult_age as 'wu_fecha_ult_agenda',
					sms2,area2,microzona FROM webpsi_criticos.gestion_provision where id_gestion=$id";
		$res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
	}

	function existeGestionProvision($cnx,$averia){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT codigo_req FROM webpsi_criticos.gestion_provision where codigo_req='$averia'";
		$res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["codigo_req"];
        
	}

    function getGestionProvisionbyAveria($cnx,$averia){

        $cnx->exec("set names utf8");
        $sql = "SELECT id_gestion FROM webpsi_criticos.gestion_provision where codigo_req='$averia'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id_gestion"];

    }

    public function get_ID_atc_By_id_gestion($cnx,$id_gestion){

        $cnx->exec("set names utf8");
        $sql = "SELECT id_atc FROM webpsi_criticos.gestion_criticos where id='$id_gestion'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id_atc"];

    }

}

?>