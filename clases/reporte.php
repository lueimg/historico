<?php

class Reporte{

	function ListarHorariosTecnicos($cnx,$finicio,$ffin,$filtros = array()){

        $sql_inserta_restantes  = "SELECT webpsi.GenerarIdMovIdTec() as cant";
        $r_restantes            = $cnx->query($sql_inserta_restantes);

        $wheres = "";
        if(count($filtros) > 0){
            //CREO EN VARIABLES LOS FILTROS ENVIADOS
            extract($filtros);

            if($filtro_quiebre!=''){
                $wheres.=" and g.quiebre in ('".$filtro_quiebre."') ";
            }

            if($idempresa!=''){
                $wheres.=" and gm.id_empresa = $idempresa ";
            }

            if($idcedula!=''){
                $wheres.=" and t.idcedula = $idcedula ";
            }

            if($ids_tecnico!=''){
                $wheres.=" and t.id in($ids_tecnico) ";
            }

        }



        $cnx->exec("set names utf8");      
        $sql = "
SELECT idtecnico,tecnico,idcedula,fecha_agenda
             ,group_concat(DISTINCT(ids) SEPARATOR '_') as ids
             ,sum(total) total,sum(h1) h1,sum(h2) h2,sum(h3) h3,sum(h4) h4,sum(h5) h5
             ,GROUP_CONCAT(estados SEPARATOR '|') estados
FROM (
        SELECT *
        FROM (
                SELECT 'A' ide,idtecnico,tecnico,idcedula,fecha_agenda
                                         ,group_concat(DISTINCT(ids) SEPARATOR '_') as ids
                                         ,sum(total) total,sum(h1) h1,sum(h2) h2,sum(h3) h3,sum(h4) h4,sum(h5) h5
                                         ,GROUP_CONCAT(estados SEPARATOR '|') estados
                from (
                        select *
                        from(   
                                select 'a' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,group_concat(DISTINCT(gm.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total
                                                         ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                                         ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,gc.flag_tecnico,gm.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_averia g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm.id_horario and gm.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm.id_horario and gm.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm.id_horario and gm.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm.id_horario and gm.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm.id_horario and gm.id_horario=5)
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                                and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'  
                                ".$wheres."     
                                GROUP BY gm.tecnico,gm.fecha_agenda
                                ) av
                                union all
                        select *
                        from(
                                select 'p' ide,t.id idtecnico ,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,group_concat(DISTINCT(gm.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total
                                                         ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                                         ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,gc.flag_tecnico,gm.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_provision g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm.id_horario and gm.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm.id_horario and gm.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm.id_horario and gm.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm.id_horario and gm.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm.id_horario and gm.id_horario=5)
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                                and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'
                                ".$wheres."
                                GROUP BY gm.tecnico,gm.fecha_agenda
                        ) pr
                                UNION ALL
                        select *
                        from(
                                select 'r' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,group_concat(DISTINCT(gm.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total
                                                         ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                                        ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,gc.flag_tecnico,gm.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_rutina_manual g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm.id_horario and gm.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm.id_horario and gm.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm.id_horario and gm.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm.id_horario and gm.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm.id_horario and gm.id_horario=5)
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                                and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'
                                ".$wheres."
                                GROUP BY gm.tecnico,gm.fecha_agenda
                                ) ru
                ) r
                GROUP BY tecnico,fecha_agenda
        ) q1
                UNION ALL
        SELECT *
        FROM (
                SELECT 'L' ide,r.idtecnico,r.tecnico,r.idcedula,r.fecha_agenda
                                         ,group_concat(DISTINCT(r.ids) SEPARATOR '_') as ids
                                         ,sum(r.total) total,sum(r.h1) h1,sum(r.h2) h2,sum(r.h3) h3,sum(r.h4) h4,sum(r.h5) h5
                                         ,GROUP_CONCAT(r.estados SEPARATOR '|') estados
                from (
                        select *
                        from(
                                select 'a' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda
                                             ,group_concat(DISTINCT(gm_aux.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total          
                                                ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                             ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,'L',gm_aux.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_averia g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join 
                                        (   select gm_2.id_gestion,gm_2.fecha_agenda,gm_2.id_horario,gm_2.id_estado
                                            from webpsi_criticos.gestion_movimientos gm_2
                                            where gm_2.fecha_movimiento in 
                                                            (
                                                                select max(gm2_2.fecha_movimiento) 
                                                                from webpsi_criticos.gestion_movimientos gm2_2 
                                                                where gm_2.id_gestion=gm2_2.id_gestion 
                                                                and gm2_2.id_estado not in ('9','10','20','3','19') 
                                                                GROUP BY gm2_2.id_gestion
                                                            )
                                            and gm_2.tecnico!=''
                                            and gm_2.id_estado='1'
                                        ) gm_aux on gm_aux.id_gestion=gm.id_gestion 
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm_aux.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm_aux.id_horario and gm_aux.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm_aux.id_horario and gm_aux.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm_aux.id_horario and gm_aux.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm_aux.id_horario and gm_aux.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm_aux.id_horario and gm_aux.id_horario=5)
                                where gm.fecha_movimiento in 
                                                (
                                                    select max(gm2.fecha_movimiento) 
                                                    from webpsi_criticos.gestion_movimientos gm2 
                                                    where gm2.id_gestion=g.id_gestion 
                                                    GROUP BY gm2.id_gestion
                                                )
                                and gm.tecnico!=''
                                and gm.id_estado in ('3','19')
                                and gm_aux.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'   
                                ".$wheres."    
                                GROUP BY gm.tecnico,gm_aux.fecha_agenda 
                                ) av
                                union all
                        select *
                        from(
                                select 'p' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda
                                             ,group_concat(DISTINCT(gm_aux.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total          
                                                ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                             ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,'L',gm_aux.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_provision g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join 
                                        (   select gm_2.id_gestion,gm_2.fecha_agenda,gm_2.id_horario,gm_2.id_estado
                                            from webpsi_criticos.gestion_movimientos gm_2
                                            where gm_2.fecha_movimiento in 
                                                            (
                                                                select max(gm2_2.fecha_movimiento) 
                                                                from webpsi_criticos.gestion_movimientos gm2_2 
                                                                where gm_2.id_gestion=gm2_2.id_gestion 
                                                                and gm2_2.id_estado not in ('9','10','20','3','19') 
                                                                GROUP BY gm2_2.id_gestion
                                                            )
                                            and gm_2.tecnico!=''
                                            and gm_2.id_estado='1'
                                        ) gm_aux on gm_aux.id_gestion=gm.id_gestion 
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm_aux.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm_aux.id_horario and gm_aux.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm_aux.id_horario and gm_aux.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm_aux.id_horario and gm_aux.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm_aux.id_horario and gm_aux.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm_aux.id_horario and gm_aux.id_horario=5)
                                where gm.fecha_movimiento in 
                                                (
                                                    select max(gm2.fecha_movimiento) 
                                                    from webpsi_criticos.gestion_movimientos gm2 
                                                    where gm2.id_gestion=g.id_gestion 
                                                    GROUP BY gm2.id_gestion
                                                )
                                and gm.tecnico!=''
                                and gm.id_estado in ('3','19')
                                and gm_aux.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'   
                                ".$wheres."    
                                GROUP BY gm.tecnico,gm_aux.fecha_agenda 
                        ) pr
                                UNION ALL
                        select *
                        from( 
                                select 'r' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda
                                             ,group_concat(DISTINCT(gm_aux.id_horario) SEPARATOR '_') as ids,count(gm.tecnico) as total          
                                                ,count(h1.horario) h1,count(h2.horario) h2,count(h3.horario) h3,count(h4.horario) h4,count(h5.horario) h5
                                             ,GROUP_CONCAT(concat_ws('_',h1.horario,h2.horario,h3.horario,h4.horario,h5.horario,'L',gm_aux.id_horario) SEPARATOR '|') estados
                                from webpsi_criticos.gestion_rutina_manual g 
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join 
                                        (   select gm_2.id_gestion,gm_2.fecha_agenda,gm_2.id_horario,gm_2.id_estado
                                            from webpsi_criticos.gestion_movimientos gm_2
                                            where gm_2.fecha_movimiento in 
                                                            (
                                                                select max(gm2_2.fecha_movimiento) 
                                                                from webpsi_criticos.gestion_movimientos gm2_2 
                                                                where gm_2.id_gestion=gm2_2.id_gestion 
                                                                and gm2_2.id_estado not in ('9','10','20','3','19') 
                                                                GROUP BY gm2_2.id_gestion
                                                            )
                                            and gm_2.tecnico!=''
                                            and gm_2.id_estado='1'
                                        ) gm_aux on gm_aux.id_gestion=gm.id_gestion 
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado 
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm_aux.id_horario
                                left join webpsi_criticos.horarios h1 on (h1.id=gm_aux.id_horario and gm_aux.id_horario=1)
                                left join webpsi_criticos.horarios h2 on (h2.id=gm_aux.id_horario and gm_aux.id_horario=2)
                                left join webpsi_criticos.horarios h3 on (h3.id=gm_aux.id_horario and gm_aux.id_horario=3)
                                left join webpsi_criticos.horarios h4 on (h4.id=gm_aux.id_horario and gm_aux.id_horario=4)
                                left join webpsi_criticos.horarios h5 on (h5.id=gm_aux.id_horario and gm_aux.id_horario=5)
                                where gm.fecha_movimiento in 
                                                (
                                                    select max(gm2.fecha_movimiento) 
                                                    from webpsi_criticos.gestion_movimientos gm2 
                                                    where gm2.id_gestion=g.id_gestion 
                                                    GROUP BY gm2.id_gestion
                                                )
                                and gm.tecnico!=''
                                and gm.id_estado in ('3','19')
                                and gm_aux.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'     
                                ".$wheres."  
                                GROUP BY gm.tecnico,gm_aux.fecha_agenda 
                        ) ru
                ) r
                GROUP BY r.tecnico,r.fecha_agenda   
        ) q2
) final
GROUP BY final.tecnico,final.fecha_agenda
order by final.tecnico,final.fecha_agenda
";
//        echo $sql;
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        //print_r($arr);
        return $arr;
        
	}

    function ListarHorarios($cnx){
        $sql="  select *
                from webpsi_criticos.horarios
                order by hora_inicio,hora_fin";
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
    }

}

?>