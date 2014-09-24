<?php

class Estados{

	function getEstado($cnx,$id_motivo,$id_submotivo){

        $sql = "select * from webpsi_criticos.estados where id_motivo=$id_motivo and id_submotivo=$id_submotivo";
        $cnx->exec("set names utf8");
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

    function getEstadoxId($cnx,$id){

        $sql = "select * 
                from webpsi_criticos.estados 
                where id=$id";
        $cnx->exec("set names utf8");
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr[0];
        
    }

    function getEstadoAll($cnx){

        $sql = "select id,estado from webpsi_criticos.estados order by estado";
        $cnx->exec("set names utf8");
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

    //Este método es por el motivo observaciones que no genera ningún estado solo obtiene el anterior
    function getEstadoxCritico($cnx,$id_gestion){

        $sql = "select id_estado as 'id',es.estado from webpsi_criticos.gestion_criticos c,webpsi_criticos.estados es
        where c.id_estado=es.id and c.id=$id_gestion";
        $cnx->exec("set names utf8");
        $res = $cnx->query($sql);
		while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

}

?>