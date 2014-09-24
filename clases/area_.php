<?php

class Area_{

	function getArea_All($cnx){

        $sql = "select * from webpsi_criticos.area_ order by area_";
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