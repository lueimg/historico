<?php

include_once "../../../class.Conexion.php";


class EmpresasCriticos{

    public function ListarEmpresas() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT * FROM webpsi_criticos.empresa WHERE activo=1 ORDER BY nombre ASC; ";
		//echo $cad;
		$res = mysql_query($cad, $cnx) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }	
	
    public function ListarEmpresasTotal() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT * FROM webpsi_criticos.empresa ORDER BY nombre ASC; ";
		//echo $cad;
		$res = mysql_query($cad, $cnx) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }	

}

?>