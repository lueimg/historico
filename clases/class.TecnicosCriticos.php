<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . '/webpsi/clases/');

require_once ("../../../clases/class.Conexion.php");

class TecnicosCriticos{

    protected $tamano = 10;
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



    public function ListarTecnicosTodos() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $filtro = $this->Filtros;
        //FILTROS
        $where = "";

        if(!empty($filtro)){
            if(!empty($filtro["idempresa"])){
                $where .= " and a.id_empresa = ".$filtro["idempresa"];
            }
            if(!empty($filtro["idcelula"])){
                $where .= " and a.idcedula = ".$filtro["idcelula"];
            }
            elseif(!empty($filtro["busqueda"])){
                if($filtro["tipo"] == "filtro_nombre")
                    $where .= " and  CONCAT_WS(' ',a.ape_paterno, a.ape_materno, a.nombres)  like '%".$filtro["busqueda"]."%' ";
                elseif($filtro["tipo"] == "filtro_carnet")
                    $where .= " and a.carnet like  '%".$filtro["busqueda"]."%' ";
                elseif($filtro["tipo"] == "filtro_carnet_critico")
                    $where .= " and a.carnet_critico like  '%".$filtro["busqueda"]."%' ";


            }
        }


        //paginacion
        $this->inicio = ($this->pagina - 1) * $this->tamano;


        $cad = "SELECT a.id, a.nombre_tecnico, a.id_empresa, a.ape_paterno, a.ape_materno, a.nombres, 
				e.nombre as empresa, a.activo, a.carnet, a.carnet_critico, a.dni, a.idcedula, c.nombre as cedula, 
				a.officetrack
                FROM webpsi_criticos.tecnicos a, webpsi_criticos.empresa e, webpsi_criticos.cedula c
				WHERE a.id_empresa=e.id AND c.idcedula=a.idcedula
				$where
				ORDER BY ape_paterno ASC
				Limit ".$this->inicio.",".$this->tamano;


		$res = mysql_query($cad, $cnx) or die(mysql_error()) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }

    public function paginacion($filtro = array()){

        $db = new Conexion();
        $cnx = $db->conectarBD();



        $filtro = $this->Filtros;
        //FILTROS
        $where = "";

        if(!empty($filtro)){

            if(!empty($filtro["idempresa"])){
                $where .= " and a.id_empresa = ".$filtro["idempresa"];
            }
            if(!empty($filtro["idcelula"])){
                $where .= " and a.idcedula = ".$filtro["idcelula"];
            }

            elseif(!empty($filtro["busqueda"])){
                if($filtro["tipo"] == "filtro_nombre")
                    $where .= " and  CONCAT_WS(' ',a.ape_paterno, a.ape_materno, a.nombres)  like '%".$filtro["busqueda"]."%' ";
                elseif($filtro["tipo"] == "filtro_carnet")
                    $where .= " and a.carnet like  '%".$filtro["busqueda"]."%' ";
                elseif($filtro["tipo"] == "filtro_carnet_critico")
                    $where .= " and a.carnet_critico like  '%".$filtro["busqueda"]."%' ";


            }
        }


        $cad = "SELECT a.id, a.nombre_tecnico, a.id_empresa, a.ape_paterno, a.ape_materno, a.nombres,
				e.nombre as empresa, a.activo, a.carnet, a.carnet_critico, a.dni, a.idcedula, c.nombre as cedula,
				a.officetrack
                FROM webpsi_criticos.tecnicos a, webpsi_criticos.empresa e, webpsi_criticos.cedula c
				WHERE a.id_empresa=e.id AND c.idcedula=a.idcedula
				$where
                ";
        $rs = mysql_query($cad, $cnx);
        $num_total_registros = mysql_num_rows($rs);

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
	
	public function Deshabilitar($idTecnico) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "UPDATE webpsi_criticos.tecnicos SET activo=0 WHERE id=$idTecnico ";
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        if ($res)
			$x = 1;
		else
			$x = 0;
        $cnx = NULL;
        $db = NULL;
        return $x;
    }

	public function Habilitar($idTecnico) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "UPDATE webpsi_criticos.tecnicos SET activo=1 WHERE id=$idTecnico ";
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        if ($res)
			$x = 1;
		else
			$x = 0;
        $cnx = NULL;
        $db = NULL;
        return $x;
    }	

    public function BuscarTecnico($id) {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT a.id, a.nombre_tecnico, a.id_empresa, a.ape_paterno, a.ape_materno, a.nombres, 
				e.nombre as empresa, a.activo, a.carnet, a.carnet_critico, a.dni, a.idcedula, a.officetrack,
				c.nombre as cedula
                FROM webpsi_criticos.tecnicos a, webpsi_criticos.empresa e, webpsi_criticos.cedula c
				WHERE a.id_empresa=e.id and a.id=$id AND c.idcedula=a.idcedula
				ORDER BY ape_paterno ASC; ";
		//echo $cad;
		$res = mysql_query($cad, $cnx) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }		
	
	public function EditarTecnico( $idTecnico, $nombre, $apellidoP, $apellidoM,
                                   $idEmpresa, $carnet, $carnetCritico, $officetrack, $idCedula, $quiebres ) {

        if(empty($idTecnico)){
            return false;
        }

		try {
			$db = new Conexion();
			$cnx = $db->conectarPDO();
			
			$nombreTecnico = $apellidoP." ".$apellidoM." ".$nombre;
			$cad = "UPDATE webpsi_criticos.tecnicos 
					SET id_empresa=?, ape_paterno=?, ape_materno=?, nombres=?, nombre_tecnico=?,
					carnet=?, carnet_critico=?, officetrack=?, idcedula=?
					WHERE id=? ";
			$res = $cnx->prepare($cad);
			$res->bindParam("1", $idEmpresa);
			$res->bindParam("2", $apellidoP);
			$res->bindParam("3", $apellidoM);
			$res->bindParam("4", $nombre);
			$res->bindParam("5", $nombreTecnico);
			$res->bindParam("6", $carnet);
			$res->bindParam("7", $carnetCritico);
			$res->bindParam("8", $officetrack);
			$res->bindParam("9", $idCedula);
			$res->bindParam("10", $idTecnico);
			$res->execute();

            //REGISTRO DE QUIEBRES
            $list_quiebres = explode(",",$quiebres);
            //LIMPIAR ANTERIORES QUIEBRES
            $cad = "DELETE FROM webpsi_criticos.tecnico_quiebre where
                        idtecnico = ? ";
            $res = $cnx->prepare($cad);
            $res->bindParam("1", $idTecnico);
            $res->execute();


            //Registrar Nuevos quiebres
            foreach($list_quiebres as $quiebre)
            {
                $cad = "INSERT INTO webpsi_criticos.tecnico_quiebre SET
                        idtecnico = ? , idquiebre=? , estado=1";
                $res = $cnx->prepare($cad);
                $res->bindParam("1", $idTecnico);
                $res->bindParam("2", $quiebre);
                $res->execute();

            }



			return "1";
		}
        catch (PDOException $e)
        {
			echo $e->getMessage () ;
			return "0";
        }
        $cnx = null;
        $db = null;		
	}

	public function NuevoTecnico( $nombre, $apellidoP, $apellidoM, $idEmpresa,
                                  $carnet, $carnetCritico, $officetrack, $idCedula, $quiebres) {

		try {
			$db = new Conexion();
			$cnx = $db->conectarPDO();
			
			$nombreTecnico = $apellidoP." ".$apellidoM." ".$nombre;
			$activo="1";

			$cad = "INSERT INTO webpsi_criticos.tecnicos (nombre_tecnico, id_empresa, ape_paterno,
					ape_materno, nombres, activo, carnet, carnet_critico, officetrack, idcedula)
					VALUES (?,?,?,?,?,?,?,?,?,?) ";
			$res = $cnx->prepare($cad);
			$res->bindParam("1", $nombreTecnico);
			$res->bindParam("2", $idEmpresa);
			$res->bindParam("3", $apellidoP);
			$res->bindParam("4", $apellidoM);
			$res->bindParam("5", $nombre);
			$res->bindParam("6", $activo);
			$res->bindParam("7", $carnet);
			$res->bindParam("8", $carnetCritico);
			$res->bindParam("9", $officetrack);
			$res->bindParam("10", $idCedula);
			$res->execute();

            $idtecnico = $cnx->lastInsertId();

            //REGISTRO DE QUIEBRES
            $list_quiebres = explode(",",$quiebres);
            foreach($list_quiebres as $quiebre)
            {
                $cad = "INSERT INTO webpsi_criticos.tecnico_quiebre SET
                        idtecnico = ? , idquiebre=? , estado=1";
                $res = $cnx->prepare($cad);
                $res->bindParam("1", $idtecnico);
                $res->bindParam("2", $quiebre);
                $res->execute();


            }



			return "1";
		}
        catch (PDOException $e)
        {
			echo $e->getMessage () ;
			return "0";
        }
        $cnx = null;
        $db = null;		
	}


	
    public function ListarCelulas($idEmpresa) {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT c.idcedula, c.nombre as cedula, c.estado, c.idempresa
                FROM webpsi_criticos.cedula c
				WHERE c.idempresa='$idEmpresa'
				ORDER BY c.nombre ASC; ";

		$res = mysql_query($cad, $cnx) or die(mysql_error()) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        //var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }
	
	public  function TecnicosOfficetrackAll()
    {

        try{


        $db = new Conexion();


        $cnx = $db->conectarPDO();
        $cnx->exec("set names utf8");

        $sql = "select id, nombre_tecnico, id_empresa, idcedula from webpsi_criticos.tecnicos t
                where t.activo = 1 and t.officetrack = 1";

        $stmt = $cnx->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($result) < 1){
            $result = array();
        }

        return json_encode($result);

        }catch (PDOException $e){

            return json_encode(array("error"=>1 ,"msj"=>$e->getMessage()));
        }

    }

    public function asistenciaTecnicosOfficetrack($fecha,$ids)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();

        list($dia,$mes,$anio) = explode("/",$fecha);
        $fecha = "$anio-$mes-$dia";


        $sql = "
        select  t.id , asi.fecha_asistencia , en.entrada , asi.numero_tecnico carnet , asi.direccion, asi.descripcion
        ,CONCAT_WS(' ',t.ape_paterno,t.ape_materno,t.nombres) nombre
        ,ce.nombre celula
        from  webpsi_criticos.tecnicos t
         inner join webpsi_criticos.cedula ce on ce.idcedula = t.idcedula
         left join webpsi_officetrack.asistencia_tecnico asi   on t.carnet_critico = asi.numero_tecnico
         left join webpsi_officetrack.asistencia_entradas en on en.id = asi.id_entrada
        where asi.fecha_asistencia >= '$fecha' and asi.fecha_asistencia < DATE_ADD('$fecha',INTERVAL 1 DAY)
         and t.activo = 1 and t.officetrack = 1
        and  t.id in ($ids)
        order by asi.id_entrada
        ";

        $stmt = $cnx->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result))
            $error = 1;
        else
            $error = 0 ;

        return json_encode(array("error"=>$error ,"data"=>$result));

    }

    public function asistenciaTecnicosCompacto($fecha,$ids)
    {
        $db = new Conexion();
        $cnx = $db->conectarPDO();
        $deb = 1;
        try{

        list($dia,$mes,$anio) = explode("/",$fecha);
        $fecha = "$anio-$mes-$dia";

        $sql = "select * from webpsi_officetrack.asistencia_entradas";

        $entradas_sql = "";
            $td_registros = "";
        foreach( $cnx->query($sql) as $entrada)
        {
            $entradas_sql .= " ,(
                            select MAX(fecha_asistencia) from webpsi_officetrack.asistencia_tecnico asi
                            WHERE asi.numero_tecnico = tec.carnet_critico and asi.id_entrada = " . $entrada["id"] . "
                            and asi.fecha_asistencia >= '$fecha' and asi.fecha_asistencia < DATE_ADD('$fecha',INTERVAL 1 DAY)
                            ) ". $entrada["entrada"];

            $td_registros .= "<th class='th_res_grupal2'>". $entrada["entrada"]."</th>";

        }

        $sql = "
                SELECT
                tec.carnet_critico carnet
                ,ce.nombre celula
                 ,CONCAT_WS(' ',tec.ape_paterno,tec.ape_materno,tec.nombres) nombre
                  $entradas_sql
                  ,IFNULL(lo.estado,'Inactivo') estado
                 FROM webpsi_criticos.tecnicos tec
                 inner join webpsi_criticos.cedula ce on ce.idcedula = tec.idcedula
                 LEFT  join (
                 SELECT
                  a.id,a.EmployeeNum carnet,DATE_FORMAT(a.TIMESTAMP, '%Y-%m-%d %H:%i:%s') t ,
                IF(DATE_ADD(DATE_FORMAT(a.TIMESTAMP, '%Y-%m-%d %H:%i:%s'),INTERVAL 1 HOUR) >= NOW() ,'Activo','Inactivo') estado
                FROM   webpsi_officetrack.locations a
                 JOIN (  SELECT   MAX(id) id   FROM  webpsi_officetrack.locations  WHERE DATE(TIMESTAMP)='$fecha' GROUP BY EmployeeNum   )
                mx ON a.id = mx.id
                 ) lo on lo.carnet = tec.carnet_critico
                where tec.activo = 1 and tec.officetrack = 1
                and tec.id in ($ids)
                ";

        $stmt = $cnx->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)) {
            $error = 1;
            $msj = "No encontraron registros";
        }else{
            $error = 0 ;
            $td_cabecera = "<th class='th_res_grupal2'>Carnet</th>
                            <th class='th_res_grupal2'>Celula</th>
                            <th class='th_res_grupal2'>Tecnico</th>".$td_registros
                            ."<th class='th_res_grupal2'>Estado</th>  "
                            ;

            $table ="<table class='tabla_res_grupal'><tr> $td_cabecera </tr>";
                foreach($result as $row){
                    $table .= "<tr>";
                        foreach($row as $field){
                            if($field== "Activo"){
                                $field ="<img src='../../../img/estado_habilitado.png'>";
                            }elseif($field== "Inactivo"){
                                $field ="<img src='../../../img/estado_deshabilitado.png'>";
                            }
                            $table .= "<td class='td_res_grupal2'>".$field."</td>"; }
                    $table .="</tr>";
                }
            $table .=" </table>";


        }

        return $table;

        }catch (PDOException $e){

            return json_encode(array("error"=>1 ,"msj"=>$e->getMessage()));
        }

    }

    public function MostrarAsistenciaRangoFechas($fecha, $fechaFin, $ids_tecnicos)
    {

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        list($dia,$mes,$anio) = explode("/",$fecha);
        $fechaIni = "$anio-$mes-$dia";

        list($dia,$mes,$anio) = explode("/",$fechaFin);
        $fechaFin = "$anio-$mes-$dia";

        //CUANTAS FECHAS MOSTRAR
        $dias	= (strtotime($fechaFin)-strtotime($fechaIni))/86400;

        if($dias < 0 ){
            return "1";

        }

        $dias 	= abs($dias);
        $cant_dias = floor($dias);

        $sql_fechas = "";
        $th_fechas ="";
        $deb =1;
        for($i = 0; $i<= $cant_dias; $i++)
        {
            $fechaNew = date('Y-m-d',strtotime('+'.$i.' days', strtotime($fechaIni)));
            $sql_fechas.="
            ,(  select  GROUP_CONCAT(DISTINCT SUBSTRING(en.entrada,1,1))
                from webpsi_officetrack.asistencia_tecnico asis
                inner join webpsi_officetrack.asistencia_entradas en  on en.id = asis.id_entrada
                where  DATE(asis.fecha_asistencia)  = '$fechaNew' and asis.numero_tecnico = tec.carnet_critico ) '$fechaNew'
            ";
            list($anio,$mes,$dia) = explode("-",$fechaNew);
            $th_fechas .= "<th class='th_res_grupal2'>". $dia . "/". $mes."</th>";
        }

        try{

            $sql = "
            select

                tec.carnet_critico
                ,tec.nombre_tecnico
                $sql_fechas
                from webpsi_criticos.tecnicos tec
                where tec.activo = 1 and tec.officetrack = 1
                and tec.id in ($ids_tecnicos)
            ";
            $deb = 1;
            $stmt = $cnx->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $deb = 1;
            $error = 0 ;
            $td_cabecera = "<th class='th_res_grupal2'>Carnet</th>
                            <th class='th_res_grupal2'>Tecnico</th>".$th_fechas

                            ;

            $table ="<table class='tabla_res_grupal'><tr> $td_cabecera </tr>";
            foreach($result as $row){
                $table .= "<tr>";
                foreach($row as $field){
                    $table .= "<td class='td_res_grupal2'>" .$field."</td>"; }
                $table .="</tr>";
            }

            $table .=" </table>";

            return $table;

        }catch (PDOException $e){
                return json_encode(array("error"=>1 ,"msj"=>$e->getMessage()));
        }

    }


}

?>