<?php

class Cedula{

    protected $cnx;
    protected $idempresa;

    /**
     * @return mixed
     */
    public function getCnx()
    {
        return $this->cnx;
    }

    /**
     * @param mixed $cnx
     */
    public function setCnx($cnx)
    {
        $this->cnx = $cnx;
    }
	function getCedula($cnx,$dis){
        $cnx->exec("set names utf8");
        $sql = "select idcedula id,nombre
                from webpsi_criticos.cedula
                WHERE id='$dis'
                and estado='1'
                order by nombre";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["cedula"];
        
	}

    function getCedulaAll($cnx,$id_empresa,$quiebre){
        $cnx->exec("set names utf8");
        $sql = "SELECT c.idcedula id,c.nombre
                FROM webpsi_criticos.cedula c
                INNER JOIN webpsi_criticos.celula_quiebre cq ON (cq.id_celula=c.idcedula AND cq.estado='1')
                INNER JOIN webpsi.tb_quiebre q ON (q.id_quiebre=cq.id_quiebre)
                WHERE c.estado='1'
                AND c.idempresa='$id_empresa'
                AND q.apocope='$quiebre'
                ORDER BY nombre";
        //echo $sql;
        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
    }

    function getCedulaAllByEmpresa(){

        $cnx = $this->getCnx();
        $idempresa = $this->getIdempresa();

        $where = "";
        if(!empty($idempresa))
        {
         $where = " and idempresa = $idempresa";
        }

        $cnx->exec("set names utf8");
        $sql = "select idcedula id,nombre , idempresa
                from webpsi_criticos.cedula
                where estado='1'
                $where
                order by nombre";

        $res = $cnx->query($sql);

        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;

    }

    function getCedulaAllByEmpresaSelectOptions($idcedula = ""){

        $cedulas = $this->getCedulaAllByEmpresa();

        $options = "";
        if(count($cedulas))
        {
            foreach($cedulas as $row)
            {
                $selected = "";
                if($row["id"] == $idcedula)
                {
                    $selected = "selected";
                }
                $options .= "<option class='added' idemp='".$row["idempresa"]."' value='".$row["id"]."' $selected>". $row["nombre"]. "</option>";
            }
        }


        return $options;

    }

    /**
     * @return mixed
     */
    public function getIdempresa()
    {
        return $this->idempresa;
    }

    /**
     * @param mixed $idempresa
     */
    public function setIdempresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }


}

?>