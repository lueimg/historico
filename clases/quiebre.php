<?php

class Quiebre{

	function getQuiebre($cnx,$usu){
        $cnx->exec("set names utf8");

        $sql=" select *
                from tb_usuario_quiebre
                where id_usuario='$usu'
                and cestado='1'";
        $res = $cnx->query($sql);
        $r=array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $r[] = $row;
        }

            if(count($r)>0){
                $sql = "select q.apocope as id,q.nombre
                        from tb_usuario_quiebre uq 
                        inner join tb_quiebre q on uq.id_quiebre=q.id_quiebre
                        WHERE uq.id_usuario='$usu'
                        and q.cestado='1'
                        and uq.cestado='1'
                        order by 1";
            }
            else{
                $sql = "select apocope as id,nombre
                        from tb_quiebre 
                        where cestado='1'
                        order by 1";
            }
        
        
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
	}

}

?>