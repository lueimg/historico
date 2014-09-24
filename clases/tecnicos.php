<?php

class Tecnicos{

	function getTecnico($cnx,$id){
        $cnx->exec("set names utf8");
        $sql = "select id,CASE ape_paterno WHEN 'Ninguno' THEN ape_paterno WHEN 'TECNICO REGULAR' THEN ape_paterno WHEN 'GESTOR HDC' THEN ape_paterno
ELSE concat(ape_paterno,' ',ape_materno,' ',nombres) END as 'nombre_tecnico',id_empresa 
from webpsi_criticos.tecnicos where id=$id";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id"];
        
	}

    function getTecnicoCarnet($cnx,$id){
        //$nombre_tecnico=trim($nombre_tecnico);
        $cnx->exec("set names utf8");
        $sql = "select carnet_critico,
                CASE ape_paterno 
                WHEN 'Ninguno' THEN ape_paterno 
                WHEN 'TECNICO REGULAR' THEN ape_paterno 
                WHEN 'GESTOR HDC' THEN ape_paterno
                ELSE concat(ape_paterno,' ',ape_materno,' ',nombres) END as 'nombre_tecnico'
                ,id_empresa 
                from webpsi_criticos.tecnicos where id='$id'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["carnet_critico"];
        
    }

	function getTecnicoAllxEmpresa($cnx,$vempresa){
        $cnx->exec("set names utf8");
        $sql = "select id,concat(ape_paterno,' ',ape_materno,' ',nombres) as 'nombre_tecnico',id_empresa 
                from webpsi_criticos.tecnicos where id_empresa=$vempresa 
                order by ape_paterno ";

        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}

    function getTecnicoAllxEmpresaxQuiebre($cnx,$vempresa,$quiebre,$cedula){
        $cnx->exec("set names utf8");
        $sql = "select t.id id,concat(ape_paterno,' ',ape_materno,' ',nombres) as 'nombre',id_empresa 
                from webpsi_criticos.tecnicos t 
                inner join webpsi_criticos.tecnico_quiebre tq on t.id=tq.idtecnico 
                inner join webpsi.tb_quiebre q on tq.idquiebre=q.id_quiebre 
                where t.activo='1' 
				AND t.id_empresa=$vempresa
				AND q.apocope='".$quiebre."'
                AND t.idcedula='".$cedula."'
                order by ape_paterno ";

        //echo $sql;
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

    function getTecnicoAll($cnx){
        $cnx->exec("set names utf8");
        $sql = "SELECT * FROM(
 SELECT * FROM(
    SELECT id,CONCAT(ape_paterno,' ',ape_materno,' ',nombres) AS 'nombre_tecnico',id_empresa 
                    FROM webpsi_criticos.tecnicos WHERE ape_paterno NOT IN('GESTOR HDC','TECNICO REGULAR','Ninguno') GROUP BY nombre_tecnico ORDER BY ape_paterno
)AS m1
UNION ALL
SELECT id,nombre_tecnico,id_empresa FROM webpsi_criticos.tecnicos WHERE ape_paterno IN('GESTOR HDC','TECNICO REGULAR','Ninguno') 
GROUP BY ape_paterno
)AS t1";
        
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

    function getTecnicoAllxCedula($cnx,$idcedula){
        $cnx->exec("set names utf8");
        $sql = "select id,concat(ape_paterno,' ',ape_materno,' ',nombres) as nombre
                from webpsi_criticos.tecnicos where idcedula=$idcedula and activo = 1
                order by ape_paterno ";

        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;

    }


    function getTecnicosAllxCedulaSelectOptions($cnx,$idcedula,$ids= "")
    {
        $registros = $this->getTecnicoAllxCedula($cnx,$idcedula);
        $options = "";
        if(count($registros))
        {
            foreach($registros as $row)
            {
                if(!empty($ids)){
                    $tecs = explode(",",$ids);
                    if(in_array($row["id"],$tecs)){
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                }
                $options .= "<option class='added' value='".$row["id"]."' $selected>". $row["nombre"]. "</option>";
            }
        }


        return $options;

    }

}

?>