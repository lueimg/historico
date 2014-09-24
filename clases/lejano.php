<?php

class Lejano{

	function getLejanoAll($cnx){

        $sql = "select * from webpsi_criticos.lejano order by lejano";
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