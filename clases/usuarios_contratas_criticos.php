<?php

class Usuarios_contratas_criticos{

	function getUsuarios_contratas_criticos_id($cnx,$id){

        $sql = "SELECT id_usuario,id_empresa,e.nombre,c.visible 
                FROM webpsi_criticos.usuarios_contratas_criticos c
                INNER JOIN webpsi_criticos.empresa e ON c.id_empresa=e.id 
                WHERE id_usuario=$id
                GROUP BY id_usuario,id_empresa 
                ORDER BY e.nombre";
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