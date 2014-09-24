<?php

class LiquidadoFeedback{

	function getFeedbackAll($cnx){

        $sql = "select * from webpsi_criticos.liquidado_feedback order by feedback";
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

}

?>