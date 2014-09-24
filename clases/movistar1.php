<?php

class Movistar1{

	function getMovistar1All($cnx){

        $sql = "select * from webpsi_criticos.movistar1 order by averia_m1";
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