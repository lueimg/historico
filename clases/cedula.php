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

    function getCedulaAll($cnx,$id_empresa){
        $cnx->exec("set names utf8");
        $sql = "select idcedula id,nombre
                from webpsi_criticos.cedula
                where estado='1'
                and idempresa='$id_empresa'
                order by nombre";
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
        $sql = "select idcedula id,nombre
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
                $options .= "<option class='added' value='".$row["id"]."' $selected>". $row["nombre"]. "</option>";
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