<?php

$PATH = $_SERVER['DOCUMENT_ROOT'] . "/webpsi/";
require_once($PATH . '/clases/class.Conexion.php');


class Quiebre{

    protected $cnx;

    public function __construct() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
        $this->cnx = $cnx;
    }

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

    public function getQuiebresAll()
    {
        $this->cnx->exec("set names utf8");

        $sql=" select *  from webpsi.tb_quiebre where cestado = 1";

        $res = $this->cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row;
        }

        return $arr;
    }

    public function quiebresPorTecnico($idtecnico = "")
    {
        if(empty($idtecnico))
            return false;

        $this->cnx->exec("set names utf8");

        $sql=" select idquiebre from webpsi_criticos.tecnico_quiebre
              where idtecnico = '$idtecnico' and estado = 1";

        $res = $this->cnx->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC))
        {
            $arr[] = $row["idquiebre"];
        }

        if(count($arr)<= 0){
            return false;
        }

        return $arr;
    }

    public function comboQuiebres($idtecnico = "")
    {
        $quiebres = $this->getQuiebresAll();

        $options = "";
        $selected_quiebres = $this->quiebresPorTecnico($idtecnico);


        foreach($quiebres as $quiebre)
        {
            if($selected_quiebres)
            {   $selected = false;
                if(in_array($quiebre["id_quiebre"] ,$selected_quiebres )){
                    $selected = true;
                }
            }
            $deb = 1;
            $options.= $this->comboQuiebreOption($quiebre["id_quiebre"],$quiebre["nombre"],$selected);
        }
        return $options;
    }

    public function comboQuiebreOption($key,$value,$selected = false)
    {
        $selected  = ($selected)?"selected": "";
        return "<option value='$key' $selected>" . $value . "</option>";

    }


}

?>