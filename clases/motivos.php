<?php

class Motivos{

	function getMotivos($cnx){

        $sql = "select * from webpsi_criticos.motivos order by motivo";
        $arr = array();
		$res = $cnx->query($sql); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

}

?>