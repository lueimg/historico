<?php

class Provision{
	
	function getProvision($cnx, $averia){

		$sql = "SELECT codigo_req AS 'averia',nomcliente 'nombre',fecha_Reg AS 'fecha_reg',quiebre 'quiebres',telefono_codclientecms 'telefono_cliente_critico',
										origen AS 'tipo_averia',horas_pedido AS 'horas_averia',fecha_Reg AS 'fecha_registro',ciudad,codigo_req AS 'codigo_averia',
					codigo_del_cliente AS 'inscripcion',fono1,telefono,mdf,obs_dev AS 'observacion_102',codigosegmento AS 'segmento',
					estacion AS 'area_',direccion AS 'direccion_instalacion',distrito AS 'codigo_distrito',nomcliente AS 'nombre_cliente',orden AS 'orden_trabajo',
					veloc_adsl,servicio AS 'clase_servicio_catv',tipo_motivo AS 'codmotivo_req_catv',tot_aver_cab AS 'total_averias_cable',
					tot_aver_cob AS 'total_averias_cobre',tot_averias AS total_averias,fftt,llave,dir_terminal,fonos_contacto,contrata,zonal,
					wu_nagendas,wu_nmovimient as 'wu_nmovimientos',wu_fecha_ult_age as 'wu_fecha_ult_agenda',tot_llam_tec as 'total_llamadas_tecnicas',
					tot_llam_seg as 'total_llamadas_seguimiento',llamadastec15d as 'llamadastec15dias',llamadastec30d as 'llamadastec30dias',
					quiebre,lejano,des_distrito AS 'distrito',eecc_zon as 'eecc_zona',eecc_final,zona_movuno AS 'zona_movistar_uno',paquete,data_multip AS 'data_multiproducto',aver_m1 AS 'averia_m1',
					fecha_data_fuente,telefono_codclientecms,rango_dias,sms1,tipo_actuacion,
					sms2,area2,microzona FROM webpsi_coc.`tmp_provision` where codigo_req = '$averia'";
		$cnx->exec("set names utf8");
		$res = $cnx->query($sql);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
	
	function getProvisionxFono($cnx, $fono){
		$cad = "SELECT averia FROM webpsi_coc.`tmp_provision` where telefono_codclientecms = '$fono'";
		
		$cnx->exec("set names utf8");
		$res = $cnx->query($cad);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row["averia"];
	}

}

?>