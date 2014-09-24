<?php

class Area2{

	/*function getIdZonal($cnx,$zonal){

        $sql = "select * from webpsi_criticos.zonales where abreviatura='$zonal'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id"];
        
	}*/
	
	function getArea2All($cnx){

        $sql = "select * from webpsi_criticos.area2 order by area2";
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