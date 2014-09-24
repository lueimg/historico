<?php
require_once('clases/empresa.php');
require_once('clases/zonales.php');

class capacidadHorarios{

	function getHorarios($cnx,$vempresa,$vzonal,$actividad){

		$empresa = new Empresa();
		$id_empresa = $empresa->getIdEmpresa($cnx,$vempresa);
		$zonales = new Zonales();
		$id_zonal= $zonales->getIdZonal($cnx,$vzonal);

        $sql = "select dia,c.horario,d.dias,h.horario 'hora',h.hora_fin,capacidad from webpsi_criticos.capacidad_horarios c,
		webpsi_criticos.horarios h, webpsi_criticos.dias d
        where d.id=c.dia and d.id=c.dia and h.id=c.horario and empresa=$id_empresa and zona=$id_zonal";
        
        $res = $cnx->query($sql);
        $horarios = array();
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $horarios[] = $row;
        }
		
        //Obteniendo Fecha Actual
        $dia = date("d");
		$fecha_ini=date("Y/n/".$dia);

 		//Aumentandole 7 dias
		$fecha = new DateTime($fecha_ini);
		$fecha->add(new DateInterval('P7D'));
		$fecha_fin = $fecha->format('Y-m-d');
		
		/*$sql2 = "select id_dia,gm.fecha_agenda,gm.id_horario,SUM(tecnicos_asignados) 'total_tecnicos' 
        from webpsi_criticos.gestion_criticos c,webpsi_criticos.gestion_movimientos gm, webpsi_criticos.estados e 
		where c.id=gm.id_gestion and gm.id_estado=e.id and id_empresa=$id_empresa and id_zonal=$id_zonal and c.tipo_actividad='$actividad' and 
        gm.fecha_agenda between '$fecha_ini' and '$fecha_fin' AND e.id in(1,8,9,10,20) 
		AND gm.fecha_movimiento=(SELECT MAX(fecha_movimiento)
		FROM webpsi_criticos.gestion_movimientos WHERE id_gestion=gm.id_gestion) GROUP BY gm.fecha_agenda,gm.id_horario";
		//echo $sql2;

        $res2 = $cnx->query($sql2);
        $horarios_agendados = array();
        while ($row = $res2->fetch(PDO::FETCH_ASSOC))
        {
            $horarios_agendados[] = $row;
        }*/

        //creando la cabecera
        $id_mes = date("m");
		$table = '<table id="horario"><thead>';
		$table .= '<tr><th colspan=8>'.date("Y").' - '.capacidadHorarios::nombreMes($id_mes-1).'</th></tr>';

		$table .= '<tr><th>Hora</th>';
		
		for($i=0;$i<7;$i++){
			//sumando dias a la fecha
			$fec = new DateTime($fecha_ini);
	        $fec->add(new DateInterval('P'.$i.'D'));
	        $fecha_res = $fec->format('Y-m-d');
	        $dia = substr($fecha_res, 8, 2);
			$table  .= "<th>".capacidadHorarios::diasSemana($fecha_res)."(".$dia.")</th>";
		}

		$table .= '</tr></thead><tbody>';
		
		//Obteniendo la cantidad de horarios
		$sql3 = "select count(*) 'total' from webpsi_criticos.horarios";
		$res3 = $cnx->query($sql3);
        $row3 = $res3->fetch(PDO::FETCH_ASSOC);
        $cantidadHorarios = $row3["total"];

        $num_celda_hora=0;
        $num_celda_valor=0;
        for($i=1;$i<=$cantidadHorarios;$i++){
        	
			$table .= '<tr><td title="'.$num_celda_hora.'" style="background:#c4e0f2">HORA</td>';
			$cont_dias=0;
			
			$num_celda_valor++;
	        foreach($horarios as $data1){
	        	if($data1["horario"]==$i){
	        		$fec = new DateTime($fecha_ini);
        			$fec->add(new DateInterval('P'.$cont_dias.'D'));
        			$fecha_res = $fec->format('Y-m-d');
        			$nombre_dia = capacidadHorarios::diasSemana($fecha_res);
        			$codigo_dia = capacidadHorarios::codigoDia($fecha_res);
        			$codigo_dia = ($codigo_dia==0)? 7:$codigo_dia;
					$estado="";
					$libres="Total:".$data1["capacidad"]."<br>Libres:".$data1["capacidad"];
					//Horarios reservados
					/*foreach($horarios_agendados as $data2){
						if($data2["fecha_agenda"]==$fecha_res && $data2["id_horario"]==$data1["horario"]){
							$libres="";
							$libres = $data1["capacidad"] - $data2["total_tecnicos"];
							$estado=($libres==0)? "background:#ff0000;color:#fff":"";
							$libres= "Total:".$data1["capacidad"]."<br>Libres:".$libres;
							break;
						}
					}*/

					$hoy = date("Y-m-d");
					if($estado=="" && $hoy==$fecha_res){
						$hora = date("H")+2;
							if($hora>=$data1["hora_fin"]){
								$estado="background:#ffff00;color:#000";
							}
					}

					//Imprimiendo estado del horario
					$hora = $data1["hora"];
					$table=str_replace("HORA",$hora,$table);
					if($nombre_dia=="domingoSSS"){ // DOMINGO SE TOMARA EN CUENTA
						$table  .= '<td title="'.$num_celda_valor.'" data-fec="'.$fecha_res.'" data-horario="'.$data1["horario"].'" data-dia="'.$codigo_dia.'" data-hora="'.$data1["hora"].'" data-total="'.$data1["capacidad"].'" style="background:#ffff00;color:#000">0</td>';
					}else{
						$table  .= '<td title="'.$num_celda_valor.'" data-fec="'.$fecha_res.'" data-horario="'.$data1["horario"].'" data-dia="'.$codigo_dia.'" data-hora="'.$data1["hora"].'" data-total="'.$data1["capacidad"].'" style="'.$estado.'">'.$libres.'</td>';
					}
					$cont_dias++;
					$num_celda_valor++;
				}
				
	        }
	        $table .= '</tr>';
	        $num_celda_hora+=8;
	    }
        echo $table;
	}

	public static function diasSemana($fecha){

		$dia = capacidadHorarios::codigoDia($fecha);
		$dias = array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
		return $dias[$dia];
	}

	public static function nombreMes($id_mes){

		$mesesNombre =  array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		return  $mesesNombre[$id_mes];
	}

	public static function codigoDia($fecha){
		$anio = substr($fecha, 0, 4);
		$mes = substr($fecha, 5, 2);
		$dia = substr($fecha, 8, 2);
		$id_dia= date("w",mktime(0, 0, 0, $mes, $dia, $anio));
		return $id_dia;
	}

}

?>