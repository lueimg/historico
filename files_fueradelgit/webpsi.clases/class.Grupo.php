<?php

include_once "class.Conexion.php";

class Grupo {
    private $id = '';
    private $id_persona = '';
    private $id_grupo = '';
    private $estado = '';

    public function getId() {
        return $this->id;
    }

    public function setId($x) {
        $this->id = $x;
    }

    public function getIdPersona() {
        return $this->id_persona;
    }

    public function setIdPersona($x) {
        $this->id_persona = $x;
    }

    public function getIdGrupo() {
        return $this->id_grupo;
    }

    public function setIdGrupo($x) {
        $this->id_grupo = $x;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($x) {
        $this->estado = $x;
    }


    public function ListadoGrupos() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT id, grupo,estado FROM `tb_grupo`  ORDER BY grupo ASC;";

        $res = mysql_query($cad, $cnx);
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function ListadoGruposActivos() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT id, grupo,estado FROM `tb_grupo` where estado = 1 ORDER BY grupo ASC;";

        $res = mysql_query($cad, $cnx);
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

	public function ListadoGruposUsuario($idUsuario) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
				
		$cad = "SELECT b.id, b.`grupo` FROM `tb_usuario_grupo` a , `tb_grupo` b 
				WHERE a.`id_grupo`=b.`id`
				AND id_usuario=$idUsuario ORDER BY id_grupo ASC;";
				
		//ECHO $cad;
        $res = mysql_query($cad, $cnx);
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function InsertarRegistro($data)
    {

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{

            $cnx->beginTransaction();

            $sql = "INSERT into webpsi.tb_grupo set grupo = :nombre , estado = :estado";
            $stmt = $cnx->prepare($sql);
            $stmt->execute(array(
                ":nombre"=>$data["grupo"],
                ":estado"=>$data["estado"],
            ));

            $cnx->commit();
            return json_encode(array("error"=>0,"msj"=>"Grupo Registrada Correctamente",));

        }catch (PDOException $e){
            $cnx->rollBack();
            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }

    }

    public function EditarRegistro($data)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{

            $cnx->beginTransaction();

            $sql = "update  webpsi.tb_grupo set  grupo = :nombre,estado = :estado  where id= :id";
            $stmt = $cnx->prepare($sql);
            $stmt->execute(array(
                ":nombre"=>$data["grupo"],
                ":estado"=>$data["estado"],
                ":id"=>$data["id"],
            ));

            $cnx->commit();
            return json_encode(array("error"=>0,"msj"=>"Persona Actualizada Correctamente",));

        }catch (PDOException $e){
            $cnx->rollBack();
            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }
    }

    public function cambiarEstado($id,$valor)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{
            $stmt = $cnx->prepare('update webpsi.tb_grupo set estado = :valor where id =:id');
            $stmt->execute(array(
                ':id'   => $id,
                ':valor' => $valor
            ));
            return "1";
        }catch (PDOException $e){

            return $e->getMessage();
        }
    }

    public function getDataRegistro($id)
    {
        if(empty($id))
            return json_encode(array("error"=> 1 , "msj"=> "Hay un problema con el ID del grupo en consulta"));

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{
            $sql = "
            select   id,grupo,estado  from webpsi.tb_grupo where id = $id;";
            $stmt = $cnx->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(count($result)<1)
                return json_encode(array("error"=>1,"msj"=>"No se encontro registros del grupo",));

            return json_encode(array("error"=>0,"data"=>$result,));

        }catch (PDOException $e){
            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }
    }
	
}