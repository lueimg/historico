<?php
/*
 * GESTION HORARIOS export excel
 * Creado en Telefonica San Felipe
 *
 * Revisiones y cambios
 * |------------------|-------------------|--------------------------------------------------|
 * |____Fecha_________|_Responsable_______|___Concepto/descripcion___________________________|
 * |  17/09/14          Luis Mori             Exportar en excel filtro de reiterados         |
 * |                                                                                         |
 * |------------------|---------------------|------------------------------------------------|
 */

//
if($_GET["html"]!= 1){

    header("Content-Type:  application/x-msexcel");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Disposition: attachment; filename=averias_criticos.xls");
    header("Expires: 0");
}


include ("../../clases/class.Conexion.php");

print "\xEF\xBB\xBF"; // UTF-8 BOM



function limpia_cadena_rf($value) {
    $input = trim( preg_replace( '/\s+/', ' ', $value ) );
    return $input;
}

function limpia_campo_mysql_text($text)
{
    $text = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $text);
    return str_replace("<br />","\n",$text);
}

function writeRow($val) {
    echo '<td>'.limpia_campo_mysql_text($val).'</td>';
}
function limpia_cadena($value)
{
    $nopermitidos = array("'",'\\','<','>',"\"",";");
    $nueva_cadena = str_replace($nopermitidos, "", $value);
    return $nueva_cadena;
}

$objCnx = new Conexion();

$cnx = $objCnx->conectarPDO();


$finicio = $_GET["finicio"];
$ffin  = $_GET["ffin"];
$quiebre = $_GET["quiebre"];
$empresa = $_GET["empresa"];
$cedula = $_GET["cedula"];
$tecnico = $_GET["tecnico"];
$wheres = "";
if($quiebre!=''){
    $wheres.=" and g.quiebre in ('".$quiebre."') ";
}

if($empresa!=''){
    $wheres.=" and gm.id_empresa = $empresa ";
}

if($cedula!=''){
    $wheres.=" and t.idcedula = $cedula ";
}

if($tecnico!=''){
    $wheres.=" and t.id in($tecnico) ";
}

$cad="
SELECT final.*

FROM (
        SELECT *
        FROM (
                SELECT 'A' tipo , r.*
                from (
                        select *
                        from(
                                select emp.nombre empresa_nombre, 'a' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,gc.flag_tecnico,gm.id_horario
, g.eecc_zona, h.horario, gc.id_atc , g.quiebre, g.averia_m1, g.area2, gc.n_evento,g.averia,g.mdf,g.lejano
,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
,t.carnet,t.carnet_critico ,g.fecha_registro,ced.nombre
                                from webpsi_criticos.gestion_averia g
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                                and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'
                                ".$wheres."
                                ) av
                                union all
                        select *
                        from(
                                select emp.nombre empresa_nombre,'p' ide,t.id idtecnico ,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,gc.flag_tecnico,gm.id_horario
,g.eecc_zon, h.horario, gc.id_atc, g.quiebre, g.aver_m1, g.area2,gc.n_evento, '' averia,g.mdf,g.lejano
,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
,t.carnet,t.carnet_critico ,g.fecha_Reg,ced.nombre
                                from webpsi_criticos.gestion_provision g
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                  inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                  inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                               and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'
                                ".$wheres."
                        ) pr
                                UNION ALL
                        select *
                        from(
                                select emp.nombre empresa_nombre,'r' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm.fecha_agenda,gc.flag_tecnico,gm.id_horario
,g.eecc_zona, h.horario, gc.id_atc, g.quiebre, g.averia_m1,g.area2, gc.n_evento,g.averia,g.mdf,g.lejano
,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
,t.carnet,t.carnet_critico ,g.fecha_registro,ced.nombre
                                from webpsi_criticos.gestion_rutina_manual g
                                inner join webpsi_criticos.gestion_criticos gc on gc.id=g.id_gestion
                                inner join webpsi_criticos.gestion_movimientos gm on g.id_gestion=gm.id_gestion
                                inner join webpsi_criticos.gestion_movimientos_tecnicos mt on mt.idmovimiento=gm.id
                                inner join webpsi_criticos.estados e on e.id=gm.id_estado
                                inner join webpsi_criticos.tecnicos t on t.id=mt.idtecnico
                                inner join webpsi_criticos.horarios h on h.id=gm.id_horario
                                  inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                  inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
                                where gm.fecha_movimiento in (select max(gm2.fecha_movimiento) from webpsi_criticos.gestion_movimientos gm2 where gm2.id_gestion=g.id_gestion and gm2.id_estado not in ('20','10','9') GROUP BY gm2.id_gestion)
                                and gm.tecnico!=''
                                and gm.id_estado='1'
                               and gm.fecha_agenda BETWEEN '".$finicio."' and '".$ffin."'
                                ".$wheres."
                                ) ru
                ) r

        ) q1
                UNION ALL
        SELECT *
        FROM (
                SELECT 'L' tipo , r.*
                from (
                        select *
                        from(
                                select emp.nombre empresa_nombre,'a' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda,
                                'Liquidado' flag_tecnico,gm_aux.id_horario
                                           ,g.eecc_zona, h.horario, gc.id_atc, g.quiebre, g.averia_m1, g.area2, gc.n_evento,g.averia,g.mdf,g.lejano
,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
,t.carnet,t.carnet_critico ,g.fecha_registro,ced.nombre
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
                                   inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                   inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
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
                                ) av
                                union all
                        select *
                        from(
                                select emp.nombre empresa_nombre,'p' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda
                                ,'Liquidado' flag_tecnico,gm_aux.id_horario
                                               ,g.eecc_zon, h.horario, gc.id_atc, g.quiebre, g.aver_m1, g.area2, gc.n_evento, ''  averia,g.mdf,g.lejano
,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
,t.carnet,t.carnet_critico ,g.fecha_Reg,ced.nombre
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
                                  inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                  inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
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
                        ) pr
                                UNION ALL
                        select *
                        from(
                                select emp.nombre empresa_nombre,'r' ide,t.id idtecnico,trim(gm.tecnico) tecnico,t.idcedula,gm_aux.fecha_agenda
                                                                            ,'Liquidado' flag_tecnico,gm_aux.id_horario
																  ,g.eecc_zona, h.horario, gc.id_atc, g.quiebre, g.averia_m1, g.area2, gc.n_evento,g.averia,g.mdf,g.lejano
					,gc.nombre_cliente_critico,gc.celular_cliente_critico,gc.observacion,gc.tipo_actividad
					,t.carnet,t.carnet_critico ,g.fecha_registro,ced.nombre


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
                                   inner join webpsi_criticos.empresa emp on emp.id = gm.id_empresa
                                inner join webpsi_criticos.horarios h on h.id=gm_aux.id_horario
                                inner join webpsi_criticos.cedula ced on ced.idcedula = t.idcedula
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
                        ) ru
                ) r

        ) q2
) final

order by final.tecnico,final.fecha_agenda
";


$campos = array(
//    "tipo"=> "TIPO",
//    "ide" => "IDE",
//    "idcedula"=> "ID CEDULA",
    "nombre"=> "CEDULA",
    "flag_tecnico" => "Flag TECNICO",
    "empresa_nombre"=> "EMPRESA",
//    "idtecnico"=> "ID TECNICO",
    "tecnico" => "TECNICO",
    "carnet"=> "TECNICO CARNET",
    "carnet_critico"=> "TECNICO CARNET CRITICO",
    "fecha_agenda" => "FECHA AGENDA",
    "fecha_registro"=> "FECHCA REGISTRO",
//    "id_horario" =>"ID HORARIO",
    "horario" => "HORARIO",
    "eecc_zona" => "EECC ZONA",
    "id_atc " => "ID ATC",
    "quiebre" => "QUIEBRE",
    "averia_m1" => "AVERIA M1",
    "area2" => "AREA 2",
    "n_evento" => "OFFICETRACK",
    "averia" => "AVERIA",
    "mdf" => "MDF",
    "lejano" => "LEJANO",
    "nombre_cliente_critico" => "CLIENTE",
    "celular_cliente_critico" => "CLIENTE CELULAR",
    "observacion" => "OBSERVACION",
    "tipo_actividad" => "TIPO ACTIVIDAD",

);
//,t.carnet,t.carnet_critico ,g.fecha_registro,ced.nombre

$res = $cnx->query($cad) ;
$array = $res->fetchAll(PDO::FETCH_ASSOC);
$table = "";
foreach($array as $row){

    $table .= "<tr>";
//    foreach($row as $key => $value){
//        $table .= "<td style='width:200px'>";
//        $table .= $key . "-".$value;
//        $table .= "</td>";
//    }

    foreach($campos as $key => $value){
        $table .= "<td style='width:200px'>";
        $table .= $row[trim($key)];
        $table .= "</td>";
    }

    $table .= "</tr>";


}


//CABECERA

$excel = "";
$excel .= "<table border='1'>";
$excel .= "<tr style='background: #000;color:#fff'>";

foreach($campos as $value){
    $excel .= "<th>";
    $excel .= $value;
    $excel .= "</th>";
}

$excel .= $table;
$excel .= "</tr>";
$excel .= "</table>";


print $excel;
