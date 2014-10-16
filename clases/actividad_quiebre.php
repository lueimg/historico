<?php

class ActividadQuiebre{

    function ValidarActividadQuiebre($cnx,$array){
        $sql="  SELECT count(q.apocope) cant
                FROM webpsi.tb_quiebre q
                INNER JOIN webpsi_criticos.actividad_quiebre aq ON (aq.id_quiebre=q.id_quiebre)
                INNER JOIN webpsi_criticos.actividad a ON (aq.id_actividad=a.id_actividad)
                AND q.apocope='$array[quiebre]'
                AND a.nombre='$array[actividad]'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $r="NoVa";
        if($row['cant']>0){
            $r="SiVa";
        }
        return $r;
    }

}

?>