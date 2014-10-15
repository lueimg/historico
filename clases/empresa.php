<?php
$PATH = $_SERVER['DOCUMENT_ROOT'] . "/webpsi/";
require_once($PATH . '/clases/class.Conexion.php');

class Empresa{

    protected $cnx;

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


    public function __construct() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
        $this->cnx = $cnx;
    }


    function getIdEmpresa($cnx,$vempresa){

        $sql = "select * from webpsi_criticos.empresa where nombre='$vempresa'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row["id"];
        
	}

    function getEmpresaxID($cnx,$vempresa){

        $sql = "select * from webpsi_criticos.empresa where id='$vempresa'";
        $res = $cnx->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
        
    }

	function getEmpresaAll($cnx){

        if(empty($cnx)){
            $cnx = $this->cnx;
        }

        $sql = "select * from webpsi_criticos.empresa order by nombre";
        $res = $cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }
        return $arr;
        
	}
    function getEmpresaAllSelectOptions($idseleted = ""){

        $empresas = $this->getEmpresaAll($this->getCnx());

        $options = "";
        if(count($empresas))
        {
            foreach($empresas as $empresa)
            {
                $selected = "";
                if($empresa["id"] == $idseleted)
                {
                    $selected = "selected";
                }
                $options .= "<option value='".$empresa["id"]."' $selected>". $empresa["nombre"]. "</option>";
            }
        }


        return $options;

    }

}

?>