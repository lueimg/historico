<?php

class Nodos{

	//function getNodos($cnx,$zonal){
    function getNodos($cnx){

        //$sql = "select * from webpsi_fftt.nodos_eecc_regiones where ZONAL=$zonal";
        $sql = "select * from webpsi_fftt.nodos_eecc_regiones order by NODO asc";
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