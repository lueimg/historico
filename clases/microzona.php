<?php

class Microzona{

	/*function getIdZonal($cnx,$zonal){

        $sql = "select * from webpsi_criticos.zonales where abreviatura='$zonal'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id"];
        
	}*/
	
	function getMicrozonalAll($cnx){

        $sql = "select * from webpsi_criticos.microzona order by microzona";
		//echo $sql;
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

}

?>