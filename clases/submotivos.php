<?php

class Submotivos{

	function getSubmotivos($cnx,$id_motivo){

        $sql = "select * from webpsi_criticos.submotivos where motivo=$id_motivo order by submotivo";
        $arr = array();
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