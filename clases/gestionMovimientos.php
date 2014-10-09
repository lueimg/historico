<?php

class gestionMovimientos{

	function addGestionMovimientos($cnx,$id_gestion,$id_empresa,$id_zonal,$fecha_agenda
        	,$id_horario,$id_dia,$id_motivo,$id_submotivo,$id_estado,$tecnicos_asignados,$observacion,$id_usuario
        	,$fecha,$tecnico,$fecha_consolidacion,$idtecnico){
		
			$sql = "INSERT INTO webpsi_criticos.gestion_movimientos values  ('',$id_gestion,$id_empresa,$id_zonal,'$fecha_agenda',$id_horario,$id_dia,$id_motivo,$id_submotivo,$id_estado,$tecnicos_asignados,
			'$observacion',$id_usuario,'$fecha','$tecnico','$fecha_consolidacion')";
			//echo $sql;
			$res = $cnx->exec($sql);

			$id = $cnx->lastInsertId();

			if($idtecnico!='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){

			$sqlgmt="INSERT INTO webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						 VALUES ('$id','$idtecnico')";
			$res = $cnx->exec($sqlgmt);

			}
			elseif($idtecnico=='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){	

			$sqlgmt= "	insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						select gm.id,tt.id
						from webpsi_criticos.gestion_movimientos gm
						left join 
					 		(select t.id,t.nombre_tecnico 
					 		from webpsi_criticos.tecnicos t 
					 		GROUP by t.nombre_tecnico) tt on trim(gm.tecnico)=trim(tt.nombre_tecnico)
						where tt.id is not null
						and gm.tecnico!=''
						and gm.id='$id'";
			$res = $cnx->exec($sqlgmt);
			
			}

			//echo $sql;
			

			$sql2 = "UPDATE webpsi_criticos.gestion_criticos gc, (SELECT gc.id, COUNT(gm.id) AS nmov FROM webpsi_criticos.gestion_criticos gc, webpsi_criticos.gestion_movimientos gm
					 WHERE gc.id=gm.id_gestion AND gc.id=$id_gestion GROUP BY gc.id) t 
					 SET gc.nmov = t.nmov WHERE gc.id=t.id";
			$res = $cnx->exec($sql2);
        	return $res;
        
	}

	function getGestionMovimiento($cnx,$id){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT c.observacion,c.id_gestion,tipo_actividad,nombre_cliente_critico,telefono_cliente_critico,celular_cliente_critico,c.id_horario,c.id_estado,c.id_dia,e.nombre,z.zonal,z.abreviatura,c.fecha_agenda,h.horario,m.motivo,s.submotivo,m.id as 'm_id',s.id as 's_id',es.estado,tecnico,fecha_movimiento,t.usuario 
		FROM webpsi_criticos.gestion_movimientos c,webpsi_criticos.gestion_criticos cri, webpsi_criticos.empresa e, webpsi_criticos.horarios h, 
		webpsi_criticos.zonales z, webpsi_criticos.motivos m, webpsi_criticos.submotivos s, webpsi_criticos.estados es,
		webpsi.tb_usuario t
				WHERE c.id_gestion=cri.id AND e.id=c.id_empresa AND z.id=c.id_zonal AND h.id=c.id_horario AND m.id=c.id_motivo AND s.id=c.id_submotivo
				AND es.id=c.id_estado and c.id_usuario=t.id AND c.id_gestion=$id ORDER BY fecha_movimiento DESC";

		$arr = array();
		$res = $cnx->query($sql); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

	function getTecnicoUltimoMovimiento($cnx,$id_gestion){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT tecnico FROM webpsi_criticos.gestion_criticos c,webpsi_criticos.gestion_movimientos mov 
				where c.id=mov.id_gestion and c.id=$id_gestion and 
				mov.fecha_movimiento=(SELECT MAX(mov2.fecha_movimiento) FROM webpsi_criticos.gestion_movimientos mov2
  								WHERE mov2.id_gestion=$id_gestion)";
		
		$arr = array();
		$res = $cnx->query($sql); 
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["tecnico"];
        
	}

	function getTecnico_UltimoMovimiento($cnx,$id_gestion){
		
		$cnx->exec("set names utf8");
		$sql = "SELECT gmt.idtecnico as id,mov.tecnico
				FROM webpsi_criticos.gestion_criticos c
				inner join webpsi_criticos.gestion_movimientos mov on mov.id_gestion=c.id
				inner join webpsi_criticos.gestion_movimientos_tecnicos gmt on gmt.idmovimiento=mov.id 
				where c.id=$id_gestion and 
				mov.fecha_movimiento=(	SELECT MAX(mov2.fecha_movimiento) 
										FROM webpsi_criticos.gestion_movimientos mov2
										WHERE mov2.id_gestion=$id_gestion)";
		
		$arr = array();
		$res = $cnx->query($sql); 
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
	}

	function getCriticoMovimiento($cnx,$emp,$reporte,$fecha_ini,$fecha_fin){
                               
       $cnx->exec("set names utf8");
       $filtro = "";
       if($emp!=''){
                       $filtro =" where eecc_final in($emp)";
       }

       //sumando un dia a la fecha fin por el between 
       if($fecha_ini!="" && $fecha_fin!=""){
                       $fecha_f=date($fecha_fin);
                       $fec_fin = new DateTime($fecha_f);
$fec_fin->add(new DateInterval('P1D'));
$fecha_res_fin = $fec_fin->format('Y-m-d');

if($reporte=="act"){
        if($filtro!=""){
                                                      $filtro .=" and fecha_registro between '$fecha_ini' and '$fecha_res_fin'";
                                       }else{
                                                      $filtro =" where fecha_registro between '$fecha_ini' and '$fecha_res_fin'";
                                       }
                       }else{
                                       if($filtro!=""){
                                                      $filtro .=" and fecha_creacion between '$fecha_ini' and '$fecha_res_fin'";
                                       }else{
                                                      $filtro =" where fecha_creacion between '$fecha_ini' and '$fecha_res_fin'";
                                       }
                       }
       }

       $sql = "	SELECT * FROM(
						SELECT * FROM( 
								SELECT c.id,cri.id_atc,a.eecc_final
									,(CASE c.id WHEN (	SELECT MAX(mov2.id) 
														FROM webpsi_criticos.gestion_movimientos mov2
														WHERE mov2.id_gestion=c.id_gestion
													 ) 
									  THEN 'X' ELSE '' END) 'ultimo_movimiento',tipo_actividad,a.averia,a.codmotivo_req_catv
								,a.quiebre,nombre_cliente_critico,telefono_cliente_critico,celular_cliente_critico,c.observacion
								,c.fecha_agenda,horario,d.dias, e.nombre,z.zonal,m.motivo,s.submotivo,es.estado,tecnico
								,a.fecha_registro,fecha_creacion, fecha_movimiento,t.usuario,es.id AS 'e_id',c.fecha_consolidacion
								,tipo_averia,averia_m1,l.penalizable
								,(SELECT fecha_cambio 
									FROM webpsi_coc.`averias_criticos_final_historico` ach 
									WHERE ach.averia=a.`averia` 
								 ) AS fecha_cambio
								FROM webpsi_criticos.gestion_averia a 
								INNER JOIN webpsi_criticos.gestion_criticos cri ON a.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.gestion_movimientos c ON c.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.empresa e ON e.id=c.id_empresa 
								INNER JOIN webpsi_criticos.horarios h ON h.id=c.id_horario 
								INNER JOIN webpsi_criticos.zonales z ON z.id=c.id_zonal 
								INNER JOIN webpsi_criticos.dias d ON d.id=c.id_dia 
								INNER JOIN webpsi_criticos.motivos m ON m.id=c.id_motivo 
								INNER JOIN webpsi_criticos.submotivos s ON s.id=c.id_submotivo 
								INNER JOIN webpsi_criticos.estados es ON es.id=c.id_estado 
								INNER JOIN webpsi.tb_usuario t ON c.id_usuario=t.id 
								LEFT JOIN webpsi_criticos.liquidados l on c.id_gestion=l.id_gestion
								WHERE l.id in (	SELECT max(l2.id) 
												 FROM webpsi_criticos.liquidados l2 
												 WHERE l2.id_gestion=c.id_gestion
											  )
						)AS averias 
				UNION ALL
						SELECT * FROM( 
								SELECT c.id,cri.id_atc,p.eecc_final
									,(CASE c.id WHEN (	SELECT MAX(mov2.id) 
														FROM webpsi_criticos.gestion_movimientos mov2
														WHERE mov2.id_gestion=c.id_gestion
													 ) 					
									 THEN  'X' ELSE '' END) 'ultimo_movimiento',tipo_actividad,p.codigo_req AS 'averia'
								,p.tipo_motivo as 'codmotivo_req_catv',p.quiebre,nombre_cliente_critico,telefono_cliente_critico
								,celular_cliente_critico,c.observacion, c.fecha_agenda,horario,d.dias, e.nombre,z.zonal,m.motivo
								,s.submotivo,es.estado,tecnico,p.fecha_Reg AS 'fecha_registro',fecha_creacion,fecha_movimiento,t.usuario
								,es.id AS 'e_id',c.fecha_consolidacion,origen AS 'tipo_averia',aver_m1 AS 'averia_m1',l.penalizable
								,(SELECT fecha_cambio 
								 FROM webpsi_coc.`tmp_provision_historico` pch 
								 WHERE pch.codigo_req=p.codigo_req ) AS fecha_cambio
								FROM webpsi_criticos.gestion_provision p 
								INNER JOIN webpsi_criticos.gestion_criticos cri ON p.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.gestion_movimientos c ON c.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.empresa e ON e.id=c.id_empresa 
								INNER JOIN webpsi_criticos.horarios h ON h.id=c.id_horario 
								INNER JOIN webpsi_criticos.zonales z ON z.id=c.id_zonal 
								INNER JOIN webpsi_criticos.dias d ON d.id=c.id_dia 
								INNER JOIN webpsi_criticos.motivos m ON m.id=c.id_motivo 
								INNER JOIN webpsi_criticos.submotivos s ON s.id=c.id_submotivo 
								INNER JOIN webpsi_criticos.estados es ON es.id=c.id_estado 
								INNER JOIN webpsi.tb_usuario t ON c.id_usuario=t.id 
								LEFT JOIN	webpsi_criticos.liquidados l on c.id_gestion=l.id_gestion
								WHERE l.id in (	SELECT max(l2.id) 
											 	FROM webpsi_criticos.liquidados l2 
												WHERE l2.id_gestion=c.id_gestion
											  )
						)AS provision
				UNION ALL
						SELECT * FROM( 
								SELECT c.id,cri.id_atc,a.eecc_final
									,(CASE c.id WHEN (	SELECT MAX(mov2.id) 
														FROM webpsi_criticos.gestion_movimientos mov2
														WHERE mov2.id_gestion=c.id_gestion
													 ) 
									THEN 'X' ELSE '' END) 'ultimo_movimiento',tipo_actividad,a.averia,a.codmotivo_req_catv,a.quiebre
								,nombre_cliente_critico,telefono_cliente_critico,celular_cliente_critico,c.observacion,c.fecha_agenda
								,horario,d.dias, e.nombre,z.zonal,m.motivo,s.submotivo,es.estado,tecnico,a.fecha_registro,fecha_creacion
								,fecha_movimiento,t.usuario,es.id AS 'e_id',c.fecha_consolidacion,tipo_averia,averia_m1,l.penalizable
								,'' AS fecha_cambio
								FROM webpsi_criticos.gestion_rutina_manual a 
								INNER JOIN webpsi_criticos.gestion_criticos cri ON a.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.gestion_movimientos c ON c.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.empresa e ON e.id=c.id_empresa 
								INNER JOIN webpsi_criticos.horarios h ON h.id=c.id_horario 
								INNER JOIN webpsi_criticos.zonales z ON z.id=c.id_zonal 
								INNER JOIN webpsi_criticos.dias d ON d.id=c.id_dia 
								INNER JOIN webpsi_criticos.motivos m ON m.id=c.id_motivo 
								INNER JOIN webpsi_criticos.submotivos s ON s.id=c.id_submotivo 
								INNER JOIN webpsi_criticos.estados es ON es.id=c.id_estado 
								INNER JOIN webpsi.tb_usuario t ON c.id_usuario=t.id 
								LEFT JOIN webpsi_criticos.liquidados l on c.id_gestion=l.id_gestion
								WHERE l.id in (	SELECT max(l2.id) 
												FROM webpsi_criticos.liquidados l2 
												WHERE l2.id_gestion=c.id_gestion
											  )
						)AS rutinas 
				UNION ALL 
						SELECT * FROM( 
								SELECT c.id,cri.id_atc,p.eecc_final
									,(CASE c.id WHEN (	SELECT MAX(mov2.id) 
														FROM webpsi_criticos.gestion_movimientos mov2
														WHERE mov2.id_gestion=c.id_gestion
													 ) 					
									 THEN  'X' ELSE '' END) 'ultimo_movimiento',tipo_actividad,p.codigo_req AS 'averia'
								,p.tipo_motivo as 'codmotivo_req_catv',p.quiebre,nombre_cliente_critico,telefono_cliente_critico
								,celular_cliente_critico,c.observacion, c.fecha_agenda,horario,d.dias, e.nombre,z.zonal,m.motivo
								,s.submotivo,es.estado,tecnico,p.fecha_Reg AS 'fecha_registro',fecha_creacion,fecha_movimiento,t.usuario
								,es.id AS 'e_id',c.fecha_consolidacion,origen AS 'tipo_averia',aver_m1 AS 'averia_m1',l.penalizable
								,(SELECT fecha_cambio 
								 FROM webpsi_coc.`tmp_provision_historico` pch 
								 WHERE pch.codigo_req=p.codigo_req ) AS fecha_cambio
								FROM webpsi_criticos.gestion_rutina_manual_provision p 
								INNER JOIN webpsi_criticos.gestion_criticos cri ON p.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.gestion_movimientos c ON c.id_gestion=cri.id 
								INNER JOIN webpsi_criticos.empresa e ON e.id=c.id_empresa 
								INNER JOIN webpsi_criticos.horarios h ON h.id=c.id_horario 
								INNER JOIN webpsi_criticos.zonales z ON z.id=c.id_zonal 
								INNER JOIN webpsi_criticos.dias d ON d.id=c.id_dia 
								INNER JOIN webpsi_criticos.motivos m ON m.id=c.id_motivo 
								INNER JOIN webpsi_criticos.submotivos s ON s.id=c.id_submotivo 
								INNER JOIN webpsi_criticos.estados es ON es.id=c.id_estado 
								INNER JOIN webpsi.tb_usuario t ON c.id_usuario=t.id 
								LEFT JOIN	webpsi_criticos.liquidados l on c.id_gestion=l.id_gestion
								WHERE l.id in (	SELECT max(l2.id) 
											 	FROM webpsi_criticos.liquidados l2 
												WHERE l2.id_gestion=c.id_gestion
											  )
						)AS rutina_provision
				)AS t1  $filtro
				ORDER BY id_atc,id ";

//echo $sql;
       $arr = array();
       $res = $cnx->query($sql); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
                }


	function updateTecnicoMovimientos($cnx,$tecnico,$codigos,$idtecnico){
		
            $cnx->exec("set names utf8");

			//ya que solo quiero actualizar el ultimo movimiento y no se puede hacer una subconsulta en el where
			//de un update lo hago por separado
			$cad1 = "SELECT max(fecha_movimiento) 'fechas' FROM webpsi_criticos.gestion_movimientos
					 group by id_gestion having id_gestion in ($codigos)";
					 
			$res1 = $cnx->query($cad1);
			$fechas = "";
			while ($row = $res1->fetch(PDO::FETCH_ASSOC))
	        {
	            $fechas .= "'".$row["fechas"]."',";
	        }
	        $fechas = substr($fechas,0,strlen($fechas)-1);
			$cad = "update webpsi_criticos.`gestion_movimientos` set 
					tecnico='$tecnico' where fecha_movimiento in ($fechas)";
			$res = $cnx->exec($cad);

			$sqlgmt2= "	DELETE FROM webpsi_criticos.gestion_movimientos_tecnicos
						where idmovimiento in (
									SELECT id 
									FROM webpsi_criticos.gestion_movimientos
									where fecha_movimiento in ($fechas)
									) ";
			$res2 = $cnx->exec($sqlgmt2);

			if($idtecnico!='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){

			$sqlgmt= "	insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						select gm.id,'$idtecnico'
						from webpsi_criticos.gestion_movimientos gm						
						where gm.tecnico!=''
						and gm.fecha_movimiento in ($fechas)";

			$res2 = $cnx->exec($sqlgmt);

			}
			elseif($idtecnico=='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){	

			
			$sqlgmt= "	insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						select gm.id,tt.id
						from webpsi_criticos.gestion_movimientos gm
						left join 
					 		(select t.id,t.nombre_tecnico 
					 		from webpsi_criticos.tecnicos t 
					 		GROUP by t.nombre_tecnico) tt on trim(gm.tecnico)=trim(tt.nombre_tecnico)
						where tt.id is not null
						and gm.tecnico!=''
						and gm.fecha_movimiento in ($fechas)";

			$res2 = $cnx->exec($sqlgmt);

			}

            return $res;
	}

	function updateEmpresaMovimientos($cnx,$codigos,$empresa,$tecnico,$idtecnico){
		
            $cnx->exec("set names utf8");
			$cad = "update webpsi_criticos.`gestion_movimientos` set id_empresa='$empresa',tecnico='$tecnico' where id_gestion in($codigos)";
			$res = $cnx->exec($cad);


			$sqlgmt2= "	DELETE FROM webpsi_criticos.gestion_movimientos_tecnicos
						where idmovimiento in (
									SELECT id 
									FROM webpsi_criticos.gestion_movimientos
									where id_gestion in ($codigos)
									) ";
			$res2 = $cnx->exec($sqlgmt2);

			if($idtecnico!='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){

			$sqlgmt= "	insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						select gm.id,'$idtecnico'
						from webpsi_criticos.gestion_movimientos gm						
						where gm.tecnico!=''
						and gm.id_gestion in ($codigos)";

			$res2 = $cnx->exec($sqlgmt);

			}
			elseif($idtecnico=='' and trim($tecnico)!='' and trim($tecnico)!='-- Seleccione --'){	

			
			$sqlgmt= "	insert into webpsi_criticos.gestion_movimientos_tecnicos (idmovimiento,idtecnico)
						select gm.id,tt.id
						from webpsi_criticos.gestion_movimientos gm
						left join 
					 		(select t.id,t.nombre_tecnico 
					 		from webpsi_criticos.tecnicos t 
					 		GROUP by t.nombre_tecnico) tt on trim(gm.tecnico)=trim(tt.nombre_tecnico)
						where tt.id is not null
						and gm.tecnico!=''
						and gm.id_gestion in ($codigos)";

			$res2 = $cnx->exec($sqlgmt);			
			
			}

			
			

            return $res;
	}

}

?>