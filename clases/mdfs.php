<?php

class Mdfs{

	function getMdfsAll($cnx){

        $sql = "select * from webpsi_fftt.mdfs_eecc_regiones order by MDF";
        $arr = array();
		$res = $cnx->query($sql); 
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            foreach ($row as $key=>$val) {
                $row[$key] = utf8_encode($val);
            }
            $arr[] = $row;
        }
        return $arr;
        
	}

	function getMdfs($cnx,$zonal){

        $sql = "select * from webpsi_fftt.mdfs_eecc_regiones where ZONAL in ($zonal) order by MDF";
        $arr = array();
		$res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            foreach ($row as $key=>$val) {
                $row[$key] = utf8_encode($val);
            }
            $arr[] = $row;
        }
        return $arr;
        
	}
        
        //Para CATV
        public function getMdfCatv($cnx,$zonal){

            $sql = "SELECT 
                        nodo MDF, eecc EECC, EECC_CRITICO, ZONA_CRITICO, LEJANO 
                    FROM 
                        webpsi_fftt.nodos_eecc_regiones 
                    WHERE 
                        ZONAL in ($zonal) 
                    ORDER BY MDF";
            $arr = array();
            $res = $cnx->query($sql);
            while ($row = $res->fetch(PDO::FETCH_ASSOC))
            {
                foreach ($row as $key=>$val) {
                    $row[$key] = utf8_encode($val);
                }
                $arr[] = $row;
            }
            return $arr;
        
	}

}

?>