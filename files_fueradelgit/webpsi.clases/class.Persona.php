<?php

include_once "class.Conexion.php";

class Persona {
    private $id = '';
    private $Nombre = '';
    private $Apellido_P = '';
    private $Apellido_M = '';
    private $Dni = '';
    private $Id_EECC = '';

    protected $tamano = 14;
    protected $pagina = 1;
    protected $inicio = 0;
    protected $Filtros =array();

    /**
     * @return array
     */
    public function getFiltros()
    {
        return $this->Filtros;
    }

    /**
     * @param array $Filtros
     */
    public function setFiltros($Filtros)
    {
        $this->Filtros = $Filtros;
    }

    /**
     * @return int
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * @param int $inicio
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    /**
     * @return int
     */
    public function getPagina()
    {
        return $this->pagina;
    }

    /**
     * @param int $pagina
     */
    public function setPagina($pagina)
    {
        if(!empty($pagina))
            $this->pagina = $pagina;
        else
            $this->pagina = 1;
    }

    /**
     * @return int
     */
    public function getTamano()
    {
        return $this->tamano;
    }

    /**
     * @param int $tamano
     */
    public function setTamano($tamano)
    {
        $this->tamano = $tamano;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($x) {
        $this->id = $x;
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setNombre($x) {
        $this->Nombre = $x;
    }

    public function getApellido_P() {
        return $this->Apellido_P;
    }

    public function setApellido_P($x) {
        $this->Apellido_P = $x;
    }

    public function getApellido_M() {
        return $this->Apellido_M;
    }

    public function setApellido_M($x) {
        $this->Apellido_M = $x;
    }
    
    public function getDni() {
        return $this->Dni;
    }

    public function setDni($x) {
        $this->Dni = $x;
    }
    
    public function getId_EECC() {
        return $this->Id_EECC;
    }

    public function setId_EECC($x) {
        $this->Id_EECC = $x;
    }

    public function LlenarDatosPersona() {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        $idPersona = $this->id;
        
        $cad = "SELECT * FROM `tb_persona` WHERE id=$idPersona";
        $res = mysql_query($cad, $cnx);
        while ($row = mysql_fetch_row($res))
        {
            $this->Apellido_M = $row["apellido_m"];
            $this->Apellido_P = $row["apellido_p"];
            $this->Nombre = $row["nombre"];
            $this->Dni = $row["dni"];
            $this->Id_EECC = $row["id_eecc"];
            //$arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    
    public function BuscarCelular($idPersona) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "SELECT contacto FROM `tb_persona_contacto` 
                WHERE id_tipo_contacto=1 and id_persona=$idPersona 
                AND defecto=1 LIMIT 1;";
		//echo "<br/>".$cad;
        $res = mysql_query($cad, $cnx);
        $row = mysql_fetch_array($res);
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        //echo "celu=".$arr["contacto"];
        return $row["contacto"];
    }

    private function ConditionWhere()
    {
        //FILTROS
        $filtros = $this->Filtros;
        $where = " where 1 = 1 ";
        if($filtros["filter"]== "ape"){
            $where  .=  "  and CONCAT_WS(' ',pe.nombre,pe.apellido_p,pe.apellido_m,pe.nombre) like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "dni"){
            $where  .=  "  and pe.dni like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "empresa_principal"){
            $where  .=  "  and pe.id_eecc =".$filtros["value"]."  ";
        }elseif($filtros["filter"]== "grupo"){
            $where  .=  "  and FIND_IN_SET(".$filtros["value"].",pg.grupos)   ";
        }elseif($filtros["filter"]== "numero"){
            $where  .=  "  and n.numeros like '%".$filtros["value"]."%'  ";
        }

        return $where;
    }

    public function ListadoPersonas() {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
        $deb = 1;
        try{
            $where = $this->ConditionWhere();
            //paginacion
            $this->inicio = ($this->pagina - 1) * $this->tamano;

            $cad = "select  pe.id,pe.nombre,pe.apellido_p,pe.apellido_m,pe.dni , pe.estado , ee.eecc
                from webpsi.tb_persona pe
                inner join webpsi.tb_eecc ee on ee.id = pe.id_eecc
                LEFT JOIN
                ( select  id_persona, GROUP_CONCAT(id_grupo) grupos from webpsi.tb_persona_grupo pg
                GROUP BY id_persona )pg on pg.id_persona = pe.id
                LEFT JOIN(
                select id_persona,GROUP_CONCAT(CONCAT_WS('-',contacto,defecto) SEPARATOR '|') numeros
                from webpsi.tb_persona_contacto GROUP BY id_persona) n on n.id_persona = pe.id
                $where
                order by pe.apellido_p asc
                 Limit ".$this->inicio.",".$this->tamano;
$deb = 1;

            $stmt = $cnx->query($cad);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $cnx = NULL;
            $db = NULL;
            return $result;


        }catch (PDOException $e){
            $deb = 1;
            return $e->getMessage();
        }




    }

    public function paginacion($filtro = array()){

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        //FILTROS
        $where = $this->ConditionWhere();

        $cad = "select 1
                from webpsi.tb_persona pe
                inner join webpsi.tb_eecc ee on ee.id = pe.id_eecc
                LEFT JOIN
                ( select  id_persona, GROUP_CONCAT(id_grupo) grupos from webpsi.tb_persona_grupo pg
                GROUP BY id_persona )pg on pg.id_persona = pe.id
                LEFT JOIN(
                select id_persona,GROUP_CONCAT(CONCAT_WS('-',contacto,defecto) SEPARATOR '|') numeros
                from webpsi.tb_persona_contacto GROUP BY id_persona) n on n.id_persona = pe.id
				$where
                ";
        $rs = $cnx->query($cad);
        $num_total_registros = $rs->rowCount();

        //calculo el total de páginas
        $total_paginas = ceil($num_total_registros / $this->tamano);
        $pagina = $this->pagina;
        $url = "";
        $html = "";

        if ($total_paginas > 1) {
            if ($pagina != 1)
                $html .= '<a href="'.$url.'?pagina='.($pagina-1).'"> << </a>';
            for ($i=1;$i<=$total_paginas;$i++) {
                if ($pagina == $i)
                    //si muestro el índice de la página actual, no coloco enlace
                    $html .=  $pagina;
                else
                    //si el índice no corresponde con la página mostrada actualmente,
                    //coloco el enlace para ir a esa página
                    $html .=  '  <a href="'.$url.'?pagina='.$i.'">'.$i.'</a>  ';
            }
            if ($pagina != $total_paginas)
                $html .=  '<a href="'.$url.'?pagina='.($pagina+1).'"> >> </a>';
        }
        return $html;

    }

    public function cambiarEstado($id, $valor ){

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{
            $stmt = $cnx->prepare('update webpsi.tb_persona set estado = :valor where id =:id');
            $stmt->execute(array(
                ':id'   => $id,
                ':valor' => $valor
            ));
            return "1";
        }catch (PDOException $e){

            return $e->getMessage();
        }
    }

    public function crearPersona($data)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{

            $cnx->beginTransaction();


            $sql = "INSERT into webpsi.tb_persona set
                    nombre = :nombre , apellido_p = :ape_pa , apellido_m = :ape_ma,
                    dni = :dni , id_eecc= :eecc , estado = :estado";
            $stmt = $cnx->prepare($sql);
            $stmt->execute(array(
                ":nombre"=>$data["nombre"],
                ":ape_pa"=>$data["ape_pa"],
                ":ape_ma"=>$data["ape_ma"],
                ":dni"=>$data["dni"],
                ":eecc"=>$data["ideecc"],
                ":estado"=>$data["estado"],
            ));

            $id_persona_new = $cnx->lastInsertId();

            //INSERTAR GRUPO
            $grupos = explode(",",$data["grupo"]);
            if(count($grupos)> 0){
                foreach($grupos as $grupo ){
                    $sql = "INSERT into webpsi.tb_persona_grupo set
                   id_persona = :id_persona , id_grupo = :id_grupo, estado = 1";
                    $stmt = $cnx->prepare($sql);
                    $stmt->execute(array(
                        ":id_persona"=>$id_persona_new,
                        ":id_grupo"=>$grupo,
                    ));
                }
            }

            //INSERTAR NUMERO DE CONTACTO
            if($data["numeros"] != "") {
                $numeros = explode(",", $data["numeros"]);
                if (count($numeros) > 0) {
                    foreach ($numeros as $num) {
                        list($contact, $selected) = explode("-", $num);
                        $stmt = $cnx->prepare('Insert into  webpsi.tb_persona_contacto set
                                          id_persona = :id_persona , id_tipo_contacto = 1 , contacto=:contacto , defecto=:defecto ');
                        $stmt->execute(array(':id_persona' => $id_persona_new, ':contacto' => $contact, ":defecto" => $selected));

                    }
                }
            }

            $cnx->commit();
            return json_encode(array("error"=>0,"msj"=>"Persona Registrada Correctamente",));

        }catch (PDOException $e){
            $cnx->rollBack();
            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }


    }

    public function editarPersona($data)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try{

            $cnx->beginTransaction();

            $sql = "update  webpsi.tb_persona set
                    nombre = :nombre , apellido_p = :ape_pa , apellido_m = :ape_ma,
                    dni = :dni , id_eecc= :eecc , estado = :estado where id= :id";
            $stmt = $cnx->prepare($sql);
            $stmt->execute(array(
                ":nombre"=>$data["nombre"],
                ":ape_pa"=>$data["ape_pa"],
                ":ape_ma"=>$data["ape_ma"],
                ":dni"=>$data["dni"],
                ":eecc"=>$data["ideecc"],
                ":estado"=>$data["estado"],
                ":id"=>$data["id"],
            ));

            $id_persona_new = $data["id"];

            //INSERTAR GRUPO
            $del = "Delete from webpsi.tb_persona_grupo where id_persona = $id_persona_new";
            $cnx->exec($del);

            $grupos = explode(",",$data["grupo"]);
            if(count($grupos)> 0){
                foreach($grupos as $grupo ){
                    $sql = "INSERT into webpsi.tb_persona_grupo set
                   id_persona = :id_persona , id_grupo = :id_grupo, estado = 1";
                    $stmt = $cnx->prepare($sql);
                    $stmt->execute(array(
                        ":id_persona"=>$id_persona_new,
                        ":id_grupo"=>$grupo,
                    ));
                }
            }

            //INSERTAR NUMERO DE CONTACTO
            $del = "Delete from webpsi.tb_persona_contacto where id_persona = $id_persona_new";
            $cnx->exec($del);

            if($data["numeros"] != ""){
                $numeros = explode(",",$data["numeros"]);
                if(count($numeros)> 0){
                    foreach($numeros as $num ){
                        list($contact, $selected) = explode("-",$num);
                        $stmt = $cnx->prepare('Insert into  webpsi.tb_persona_contacto set
                                      id_persona = :id_persona , id_tipo_contacto = 1 , contacto=:contacto , defecto=:defecto ');
                        $stmt->execute(array( ':id_persona'   => $id_persona_new,':contacto' => $contact , ":defecto"=>$selected));

                    }
                }
            }


            $cnx->commit();
            return json_encode(array("error"=>0,"msj"=>"Persona Actualizada Correctamente",));

        }catch (PDOException $e){
            $cnx->rollBack();
            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }
    }

    public function getDataUsuarioAll($id)
    {
        if(empty($id)){
            return json_encode(array("error"=> 1 , "msj"=> "El id de usuario esta vacio"));
        }

        $db = new Conexion();
        $cnx = $db->conectarPDO();


        try{
            $sql = "
            select   pe.id, pe.nombre ,pe.apellido_p, pe.apellido_m, pe.dni , pe.id_eecc, pe.estado, pg.grupos, n.numeros
                from webpsi.tb_persona pe
                inner join webpsi.tb_eecc ee on ee.id = pe.id_eecc
                LEFT JOIN
                ( select  id_persona, GROUP_CONCAT(id_grupo) grupos from webpsi.tb_persona_grupo pg
                GROUP BY id_persona )pg on pg.id_persona = pe.id
                LEFT JOIN(
                select id_persona,GROUP_CONCAT(CONCAT_WS('-',contacto,defecto) SEPARATOR '|') numeros
                from webpsi.tb_persona_contacto GROUP BY id_persona) n on n.id_persona = pe.id
            where pe.id = $id;";
            $stmt = $cnx->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(count($result)<1){
                return json_encode(array("error"=>1,"msj"=>"No se encontro registros del usuario",));
            }

            return json_encode(array("error"=>0,"data"=>$result,));


        }catch (PDOException $e){

            return json_encode(array("error"=>1,"msj"=>$e->getMessage(),));
        }

    }

	
}