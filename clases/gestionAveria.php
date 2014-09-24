<?php

class gestionAveria{

	function addGestionAveria($cnx,$id_gestion,$tipo_averia,$horas_averia,$fecha_reporte,$fecha_registro,$ciudad,$averia,
		            $inscripcion,$fono1,$telefono,$mdf,$observacion_102,$segmento,$area_,$direccion_instalacion,
		            $codigo_distrito,$nombre_cliente,$orden_trabajo,$veloc_adsl,$clase_servicio_catv,$codmotivo_req_catv,
		            $total_averias_cable,$total_averias_cobre,$total_averias,$fftt,$llave,$dir_terminal,$fonos_contacto,
		            $contrata,$zonal,
		            $wu_nagendas,$wu_nmovimientos,$wu_fecha_ult_agenda,$total_llamadas_tecnicas,
		            $total_llamadas_seguimiento,$llamadastec15dias,$llamadastec30dias,
		            $quiebre,$lejano,$distrito,$eecc_zona,$zona_movistar_uno,$paquete,$data_multiproducto,
		            $averia_m1,$fecha_data_fuente,$telefono_codclientecms,$rango_dias,$sms1,$sms2,
		            $area2,$eecc_final,$microzona){

        $sql = "INSERT INTO webpsi_criticos.`gestion_averia` VALUES ('',$id_gestion,'$tipo_averia','$horas_averia','$fecha_reporte','$fecha_registro','$ciudad','$averia',
		            '$inscripcion','$fono1','$telefono','$mdf','$observacion_102','$segmento','$area_','$direccion_instalacion',
		            '$codigo_distrito','$nombre_cliente','$orden_trabajo','$veloc_adsl','$clase_servicio_catv','$codmotivo_req_catv',
		            '$total_averias_cable','$total_averias_cobre','$total_averias','$fftt','$llave','$dir_terminal','$fonos_contacto',
		            '$contrata','$zonal',
		            '$wu_nagendas','$wu_nmovimientos','$wu_fecha_ult_agenda','$total_llamadas_tecnicas',
		            '$total_llamadas_seguimiento','$llamadastec15dias','$llamadastec30dias',
		            '$quiebre','$lejano','$distrito','$eecc_zona','$zona_movistar_uno','$paquete','$data_multiproducto',
		            '$averia_m1','$fecha_data_fuente','$telefono_codclientecms','$rango_dias','$sms1','$sms2',
		            '$area2','$eecc_final','$microzona')";
        $res = $cnx->exec($sql);

        return $res;
        
	}

	function updateEmpresa($cnx,$vempresa,$vtecnico,$codigo,$idtecnico){

		try{

			$cnx->beginTransaction();

			if($vempresa!=""){
	            $cnx->exec("set names utf8");
				$cad = "update webpsi_criticos.`gestion_averia` set eecc_final='$vempresa' where id_gestion=$codigo";//sin contrarta o zonal
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

	function getGestionAveria($cnx){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT a.* FROM webpsi_criticos.gestion_averia a, webpsi_criticos.gestion_criticos c, webpsi_criticos.estados e
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

	function getGestionAveriaId($cnx,$id){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT * FROM webpsi_criticos.gestion_averia where id_gestion=$id";
		$res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
	}

	function getGestionAveriaxFono($cnx, $fono){
		$cad = "SELECT averia FROM webpsi_criticos.gestion_averia where telefono_codclientecms = '$fono'";
		
		$cnx->exec("set names utf8");
		$res = $cnx->query($cad);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row["averia"];
	}

	function existeGestionAveria($cnx,$averia){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT averia FROM webpsi_criticos.gestion_averia where averia='$averia'";
		$res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["averia"];
        
	}

}

?>