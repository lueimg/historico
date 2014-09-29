<?php

include_once "class.Conexion.php";

class HistoricoCliente {

	public static $BD_MAIN = "webpsi";
	public static $BD_FFTT = "webpsi_fftt";

    public static $dias_liquidadas = 120;

	public function array_mysql_datesort($a, $b)
	{
	    if ($a == $b) {
	        return 0;
	    }

	    return ($a->date_field < $b->date_field) ? -1 : 1; //custom check here
	}


    public function getAveriasTbaPendientes($tipoBusqueda, $valorBuscar) {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='fono') { 
			if (substr($valorBuscar, 0, 1) == "1") 
				$valorBuscar = ltrim($valorBuscar, "1");
			$filtroBusqueda = " AND a.telefono = '$valorBuscar'";
		}
		else
			$filtroBusqueda = "";
			
        
        $cad = "SELECT a.telefono, a.averia, a.cliente, a.direccion, cod_distrito, des_distrito, cod_ciudad,
                date_format(fecreg, '%d-%m-%Y %H:%i:%s') as fecreg_format, a.mdf, carea,
                fecreg,
                  CASE WHEN HOUR(TIMEDIFF(NOW(), fecreg)) < 31 AND DATEDIFF(NOW(), fecreg)>0 
                    THEN CONCAT('V:', HOUR(fecreg) ) ELSE '' END as marca,       
                (SELECT FECHA_AGENDA_FIN FROM ".self::$BD_MAIN.".`rpt_agendadas_averias` WHERE AVERIA=a.averia) AS fec_agenda,
                a.zonal, carmario as armario, ccable as cable, cbloque as bloque, terminal,
                IF (carmario='', CONCAT(a.zonal, a.mdf, ccable,LPAD(terminal, 3, '0')),
                    CONCAT(a.zonal, a.mdf, carmario,LPAD(terminal, 3, '0'))) AS llavexy,
                (SELECT `x` FROM webunificada_fftt.`fftt_terminales` t WHERE llavexy =  t.mtgespktrm  ) AS coordX,
                (SELECT `y` FROM webunificada_fftt.`fftt_terminales` t WHERE llavexy =  t.mtgespktrm  ) AS coordY,
                csegmento as segmentoY
                ,ga.id_gestion , gc.fecha_agenda,gc.id_atc
                FROM schedulle_sistemas.pen_pais_tba a
                	LEFT JOIN webpsi_criticos.gestion_averia ga on ga.averia = a.averia
					left join webpsi_criticos.gestion_criticos gc on gc.id = ga.id_gestion
                WHERE  negocio='STB' ".$filtroBusqueda."
                ORDER BY fecreg ASC; "; 
        $deb = 1;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }  
	
	
	
    public function getAveriasAdslPendientes($tipoBusqueda, $valorBuscar) {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";

        //echo "Tipo busqueda = ".$tipoBusqueda."</br>";

		if ($tipoBusqueda=="fono") { 
			if (substr($valorBuscar, 0, 1) == "1") {   // LIMA
				$valorBuscar = ltrim($valorBuscar, "1");
				$filtroBusqueda = " AND telefono = '$valorBuscar'";
			}
            else if (substr($valorBuscar, 0, 1) != "1") {  // PROVINCIA
                $valorBuscar = substr($valorBuscar, 2 );
                $filtroBusqueda = " AND telefono = '$valorBuscar'";
            }
            

		}
		else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND numero_osiptel = '$valorBuscar'";
        }  
		else
			$filtroBusqueda = "";

        $cad = "SELECT ciudad, numero_osiptel AS averia, inscripcion, CONCAT(zona_telefonica, telefono) AS telefono2, 
				telefono, segmento, subsegmento, cable, par_alimentador, sector, nro_caja, par_distribuidor, borne,
				fecha_instlacion as fec_inst, zonal, coddep, centro_ejecucion, mdf, adsl, par_adsl, pos_adsl, 
				fecha_registro, fecha_des, `area`, direccion_instalacion, codigo_distrito, 
				estado_ult as area,
				CONCAT(ape_paterno, ' ', ape_materno, ' ', nombre) AS nombre_cliente, indicador_vip,
				direccion_instalacion
				FROM `schedulle_sistemas`.`aver_pen_adsl_pais`
                WHERE 1=1 ".$filtroBusqueda." 
                ORDER BY fecha_registro DESC ";
		//echo $cad;
		
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }  

    public function getAveriasCatvPendientes($tipoBusqueda, $valorBuscar) {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='cliente') { 
			$filtroBusqueda = " AND codigodelcliente = '$valorBuscar'";
		}
		else
			$filtroBusqueda = "";
        
			
		$cad = "SELECT codigo_req AS averia, 'CATV' AS negocio, codigodelcliente AS cod_cliente, ot AS ot, 
				CONCAT(apellidopaterno, ' ', apellidomaterno, ' ', nombres) AS cliente, direccion_facturacion AS direccion, 
				indicador_vip AS csegmento, '' AS clase, 
				nodo, troba,tap, borne, codigotiporeq AS tipo_req, codigomotivoreq, oficina_administrativa, contrata,
				DATE_FORMAT(fecharegistro, '%d-%m-%Y %H:%i:%s') AS fecha_registro, '' AS estado, 
				(SELECT FECHA_AGENDA_FIN FROM ".self::$BD_MAIN.".`rpt_agendadas_averias` 
				WHERE AVERIA=a.codigo_req) AS fec_agenda 
				FROM schedulle_sistemas.aver_pen_catv_pais a WHERE 1=1 ".$filtroBusqueda." 
				ORDER BY fecharegistro ASC 	";
			
		
		//echo $cad;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    } 	
	
	
    public function getAveriasTbaLiquidadasLima($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='fono') { 
			if (substr($valorBuscar, 0, 1) == "1") 
				$valorBuscar = ltrim($valorBuscar, "1");
			$filtroBusqueda = " AND telefono = '$valorBuscar'";
		}
        else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND numero_osiptel = '$valorBuscar'";
        }
		else
			$filtroBusqueda = "";

        $cad = "SELECT numero_osiptel as averia, inscripcion, telefono, mdf, area_sig , cabecera, armario, cable,
				par_cable, bloque, numero_par_bloque, terminal, borne, tecnico, fecha_reporte, fecha_registro,
				observacion_102, otra_observacion, numero_comprobacion, tecnico_comprobacion, fecha_de_comprobacion,
				liquidacion_, detalle, tecnico_liquidacion, fecha_de_liquidacion, segmento, subsegmento, 
				direccion_instalacion, ape_paterno, ape_materno, nombre, 
				concat(ape_paterno, ' ', ape_materno, ' ', nombre) as cliente
                FROM schedulle_sistemas.`aver_liq_bas_lima_hist` 
                WHERE 1=1 ".$filtroBusqueda." 
                AND DATEDIFF( NOW(), fecha_registro ) < ".self::$dias_liquidadas."
                ORDER BY fecha_de_liquidacion DESC ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }	
	
	
	
    public function getAveriasTbaLiquidadasProvincia($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='fono') { 
			if (substr($valorBuscar, 0, 1) == "1") 
				$valorBuscar = ltrim($valorBuscar, "1");
			$filtroBusqueda = " AND telefono = '$valorBuscar'";
		}
		else
			$filtroBusqueda = "";

        $cad = "SELECT ciudad, boleta, inscripcion, telefono, codigo_cip, fecha_hora_boleta, fecha_hora_franqueo,
				comentario_de_boletin, fecha_de_sistema, correlativo as averia, mdf, segmento, subsegmento,
				direccion_instalacion, codigo_distrito, ape_paterno, ape_materno, nombre,
				concat(ape_paterno, ' ', ape_materno, ' ', nombre) as cliente,
				contrata, codigo_de_liquidacion, codigo_detalle_liq, desc_detalle_liq
                FROM schedulle_sistemas.`aver_liq_bas_prov_pedidos` 
                WHERE 1=1 ".$filtroBusqueda." 
                AND DATEDIFF( NOW(), fecha_hora_franqueo ) < ".self::$dias_liquidadas."
                ORDER BY fecha_hora_franqueo DESC ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }		
	
	
    public function getAveriasAdslLiquidadas($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='fono') { 
			if (substr($valorBuscar, 0, 1) == "1") 
				$valorBuscar = ltrim($valorBuscar, "1");
			$filtroBusqueda = " AND telefono = '$valorBuscar'";
		}
        else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND numero_osiptel = '$valorBuscar'";
        }		
		else
			$filtroBusqueda = "";

        $cad = "SELECT ciudad, numero_osiptel as averia, inscripcion, zona_telefonica, telefono,
				segmento, subsegmento, nombre_con as nombre_contacto, fecha_instalacion, estado_liq,
				tecnico_reg, tecnico_pro, tecnico_rep_4, zonal, cod_dep_, centro_ejecucion,
				mdf, adsl, numero_par_adsl, dslam, codigo_liquidacion, franqueo, observacion_liquidacion,
				cable, nro_dsa, par_alimentador, sector, nro_caja, par_distribuidor, borne,
				fecha_registro, fecha_liquidacion_ as fecha_liquidacion, fecha_des as fecha_despacho, 
				fecha_pro_tec, fecha_pro_con, ciudad, direccion_instalacion,
				codigo_distrito, ape_paterno, ape_materno, nombre,
				concat(ape_paterno, ' ', ape_materno, ' ', nombre) as cliente,
				contrata_ as contrata
                FROM schedulle_sistemas.`aver_liq_adsl_pais_hist`
                WHERE 1=1 ".$filtroBusqueda." 
                AND DATEDIFF( NOW(), fecha_registro ) < ".self::$dias_liquidadas."
                ORDER BY fecha_liquidacion_ DESC ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }		
	
	
    public function getAveriasCatvLiquidadas($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='cliente') { 
			$filtroBusqueda = " AND codigodelcliente = '$valorBuscar'";
		}
		else if ($tipoBusqueda=='codServicio') { 
			$filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND codigoreq = '$valorBuscar'";
        }        
        else
            $filtroBusqueda = " ";

        $cad = "SELECT codigoreq as averia, codigotiporeq, codigomotivoreq, codigodelcliente as cod_cliente,
				apellidopaterno, apellidomaterno, nombres,
				concat(apellidopaterno, ' ', apellidomaterno, ' ', nombres) as cliente,
				categoria_cliente, codigodelservicio as cod_servicio, clasedeservicio, oficinaadministrativa,
				departamento, provincia, distrito,
				concat(tipodevia, ' ', nombredelavia, ' ', numero, ' ', piso, ' ', interior, ' ', manzana, ' ', lote) as direccion,
				nodo, troba, lex, tap, borne, estado, fecharegistro, ot, fechaasignacion, estadoot, contrata, tecnico,
				fecha_liquidacion, codigodeliquidacion, detalle_liquidacion
                FROM schedulle_sistemas.`aver_liq_catv_pais_hist`
                WHERE 1=1 ".$filtroBusqueda." 
                AND DATEDIFF( NOW(), fecharegistro ) < ".self::$dias_liquidadas."
                ORDER BY fecha_liquidacion DESC ";
        //echo $cad;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }		
	
	
    public function getLlamadasCliente($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

		$filtroBusqueda = "";
		if ($tipoBusqueda=='fono') { 
			$filtroBusqueda = " AND telefono = '$valorBuscar'";
		}
		else if ($tipoBusqueda=='codserv') { 
			$filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        }        
        else
            $filtroBusqueda = " ";

        $cad = "SELECT servicio, fecha_llamada as fecha_llamada2, 
				DATE_FORMAT(fecha_llamada, '%d-%m-%Y %H:%i:%s') AS fecha_llamada1,
				telefono, codcli, codserv, dni_ruc, ges_producto,
				ges_servicio, ges_accion, observacion, mes, anho
                FROM webpsi_coc.llamadas
                WHERE 1=1 ".$filtroBusqueda." 
                ORDER BY fecha_llamada DESC ";
        //echo $cad;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }		
	
	public function getCliente($telefono='',$codcliatis='',$codsercms='',$codclicms='') {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        if($telefono!=''){
            $qry_telefono = " l.telefono='".$telefono."' and ";
        }else{
            $qry_telefono = "";
        }

        if($codcliatis!=''){
            $qry_codcliatis = " l.codclie='".$codcliatis."' and ";
        }else{
            $qry_codcliatis = "";
        }

        if($codsercms!=''){
            $qry_codsercms = " l.codservcms='".$codsercms."' and ";
        }else{
            $qry_codsercms = "";
        }

        if($codclicms!=''){
            $qry_codclicms = " l.codclicms='".$codclicms."' and ";
        }else{
            $qry_codclicms = "";
        }

        $cad = "SELECT l.*,q.telefono as c1, q.inscripcion as c2, pr.telefono as c1_tmp
                from webpsi_coc.tb_lineas_servicio_total l
                left JOIN webpsi_coc.averias_criticos_final q
                  ON l.telefono=q.telefono OR l.codclicms=q.inscripcion
				left join webpsi_coc.tmp_provision pr
				  on pr.telefono = l.telefono
                WHERE $qry_telefono $qry_codcliatis $qry_codsercms $qry_codclicms l.telefono!=' '";
        $deb = 1;
        $res = $cnx->query($cad); 

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }

        //var_dump($arr);

        $cnx = NULL;
        $db = NULL;
        return $arr;
    }




    public function getRegistroAtis($tipoBusqueda, $valorBuscar) { 
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        }        
        else
            $filtroBusqueda = " ";

        $cad = "SELECT re.* , cr.id_atc
                from webpsi_coc.tb_registradastotal_  re
                LEFT join webpsi_criticos.gestion_provision pr on pr.telefono= re.telefono
                left join webpsi_criticos.gestion_criticos cr on cr.id = pr.id_gestion
                LEFT JOIN webpsi_criticos.gestion_movimientos gm on gm.id_gestion = cr.id
                WHERE 1=1 $filtroBusqueda  ORDER BY fecreg DESC ";

        $deb = 1;
        $res = $cnx->query($cad); 

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }

        $cnx = NULL;
        $db = NULL;
        return $arr;
    }


    public function getFechaUpdate_AveriasBasicaPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT nombre_tabla, date_format(fecha_archivo, '%d-%m-%Y %H:%i:%s') as fechaUpdate  
				FROM schedulle_sistemas.`aver_pen_bas_lima_logs` LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	    

    public function getFechaUpdate_AveriasAdslPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT nombre_tabla, date_format(fecha_archivo, '%d-%m-%Y %H:%i:%s') as fechaUpdate 
			FROM schedulle_sistemas.`aver_pen_adsl_pais_logs` LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	

    public function getFechaUpdate_AveriasCatvPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT nombre_tabla, date_format(fecha_archivo, '%d-%m-%Y %H:%i:%s') as fechaUpdate 
			FROM schedulle_sistemas.`aver_pen_catv_logs` LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 
	
    public function getLastAveria_BasicaPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecha_reporte, '%d-%m-%Y %H:%i') as fechaUpdate, numero_osiptel 
				FROM schedulle_sistemas.aver_pen_bas_lima ORDER BY fecha_reporte DESC LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	
	
    public function getLastAveria_AdslPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecha_registro, '%d-%m-%Y %H:%i') as fechaUpdate, numero_osiptel 
				FROM schedulle_sistemas.aver_pen_adsl_pais ORDER BY fecha_registro DESC LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    }
	
    public function getLastAveria_CatvPendiente() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecharegistro, '%d-%m-%Y %H:%i') as fechaUpdate, codigo_req 
				FROM schedulle_sistemas.aver_pen_catv_pais ORDER BY fecharegistro DESC LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    }
	
    public function getListadoCriticosCobre($tipoBusqueda, $valorBuscar) {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
		
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        }        
        else
            $filtroBusqueda = " ";		

        $cad = "SELECT tipo_averia, fecha_registro, averia, telefono, mdf, tipo_servicio, tipo_actuacion, 
				fecha_subida, DATE_FORMAT(fecha_subida, '%d-%m-%Y %H:%i:%s') as fecha_subida2, quiebre,
				CASE (SELECT COUNT(*) FROM webpsi_coc.`averias_criticos_final` b WHERE b.telefono=a.telefono )
					WHEN 0 THEN 'NO' ELSE 'SI' END AS esta_pendiente
				FROM webpsi_coc.`averias_criticos_final_historico` a
				WHERE 1=1 ".$filtroBusqueda." ORDER BY fecha_subida DESC  ; ";
		//echo $cad;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr;
        $cnx = NULL;
        $db = NULL;
    }	
	
	
    public function esPosibleCritico($tipoBusqueda, $valorBuscar) {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
		
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        }
        else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        }        
        else
            $filtroBusqueda = " ";		

        $cad = "SELECT COUNT(*) as contador FROM webpsi_coc.`clientes_criticos_posibles`
				WHERE 1=1 ".$filtroBusqueda." ; ";
		//echo $cad;
        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["contador"];
        $cnx = NULL;
        $db = NULL;
    }		
	
	
	function esAveriaCritica($averia) {
		$db = new Conexion();
        $cnx = $db->conectarPDO();
		$cad = "SELECT COUNT(*) as contar FROM webpsi_coc.averias_criticos_final_historico WHERE averia='$averia' ";
		$res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
			$arr[] = $row;
		}
		//echo $arr[0]["contar"];
		if ($arr[0]["contar"] == 0)
			return false;
		else
			return true;
	}
	
    public function getFechaUpdate_Llamadas() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
			
		$cad = "SELECT date_format(MAX(f_act), '%d-%m-%Y %H:%i:%s') as fechaUpdate FROM webpsi_coc.llamadas ";

        $res = $cnx->query($cad) ; 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	
	
    public function getFechaUpdate_AgendasWU_Averias() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecha_archivo, '%d-%m-%Y %H:%i:%s') as fechaUpdate 
			FROM webpsi_coc.rpt_detallado_averia_log LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 
		
    public function getFechaUpdate_AgendasWU_Provision() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecha_archivo, '%d-%m-%Y %H:%i:%s') as fechaUpdate 
			FROM webpsi_coc.rpt_detallado_provision_log LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	

    public function getFechaUpdate_CriticosAverias() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        $cad = "SELECT date_format(fecha_subida, '%d-%m-%Y %H:%i:%s') as fechaUpdate 
			FROM webpsi_coc.averias_criticos_final_historico ORDER BY fecha_subida DESC LIMIT 1; ";

        $res = $cnx->query($cad); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
		//print_r($arr);
		return $arr[0]["fechaUpdate"];
        $cnx = NULL;
        $db = NULL;
    } 	

	
}