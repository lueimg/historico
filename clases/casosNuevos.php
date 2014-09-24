<?php

/**
 * 
 */
class CasosNuevos
{

    private $_db = "webpsi_coc";
    private $_table = "casos_nuevos";

    public function getResumenAll($cnx) {

        $sql = "SELECT 
                    tipo, cantidad, 
                    DATE_FORMAT(fecha_registro, '%d/%m/%Y %H:%i:%s') fecreg,quiebre
                FROM 
                    " . $this->_db . "." . $this->_table;
        $res = $cnx->query($sql);

        $arr = array();
		$iA=0;
        $iP=0;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            if ( $row["tipo"]=="averias" ) {
                $arr["averias"]["cantidad"] += $row["cantidad"];
                $arr["averias"]["fecreg"] = $row["fecreg"];
				
				$arr["averias"]["quiebre"]["total"][$iA]  = $row["cantidad"];
				$arr["averias"]["quiebre"]["desc"][$iA]  = $row["quiebre"];
				$iA++;
            }
            if ( $row["tipo"]=="provision" ) {
                $arr["provision"]["cantidad"] += $row["cantidad"];
                $arr["provision"]["fecreg"] = $row["fecreg"];
				/*$arr["provision"]["quiebre"][$i] = $row["cantidad"];
				$arr["provision"]["quiebre_tot"][$i] = $row["quiebre"];*/
				
				$arr["provision"]["quiebre"]["total"][$iP]  = $row["cantidad"];
			    $arr["provision"]["quiebre"]["desc"][$iP]  = $row["quiebre"];
                $iP++;
            }
			
			//$i++;
        }
		//print_r($arr);
        return $arr;
    }

}

?>