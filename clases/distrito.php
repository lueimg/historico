<?php

class Distrito{

	function getDistrito($cnx,$dis){
        $cnx->exec("set names utf8");
        $sql = "select distrito
                from webpsi_criticos.gestion_averia
                WHERE distrito='$dis'
                GROUP BY distrito
                order by 1";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["distrito"];
        
	}

    function getDistritoAll($cnx){
        $cnx->exec("set names utf8");
        $sql = "select distrito
                from webpsi_criticos.gestion_averia
                GROUP BY distrito
                order by 1;";
        
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

}

?>