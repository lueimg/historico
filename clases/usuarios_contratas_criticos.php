<?php

class Usuarios_contratas_criticos{

	function getUsuarios_contratas_criticos_id($cnx,$id){

        $sql = "select id_usuario,id_empresa,e.nombre from webpsi_criticos.usuarios_contratas_criticos c,webpsi_criticos.empresa e where 
                c.id_empresa=e.id and id_usuario=$id
                group by id_usuario,id_empresa order by e.nombre";
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