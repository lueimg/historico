<?php

/**
 * 
 */
class SolucionesComerciales
{

    private $_db = "webpsi_criticos";
    private $_table = "soluciones_comerciales";

    public function getSolucionesAll($cnx) {

        $sql = "SELECT 
                    id, nombre 
                FROM 
                    " . $this->_db . "." . $this->_table
                . " ORDER BY nombre";
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }
        return $arr;
    }

}

?>