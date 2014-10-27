<?php

include_once "class.Conexion.php";

class Usuario {
    private $id = '';
    private $Nombre = '';
    private $Apellido= '';
    private $Usuario= '';
	private $Password='';
    private $Dni= '';
	private $Online= '';
	private $Id_Perfil= '';
    private $Id_Area = '';

    protected $cnx;

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


    public function __construct() {

        $db = new Conexion();
        $cnx = $db->conectarBD();
        $this->cnx = $cnx;
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

    public function getApellido() {
        return $this->Apellido;
    }

    public function setApellido($x) {
        $this->Apellido = $x;
    }

    public function getUsuario() {
        return $this->Usuario;
    }

    public function setUsuario($x) {
        $this->Usuario = $x;
    }
    
	public function getPassword() {
        return $this->Password;
    }

    public function setPassword($x) {
        $this->Password = $x;
    }
    
	public function getDni() {
        return $this->Dni;
    }

    public function setDni($x) {
        $this->Dni = $x;
    }
    
	public function getOnline() {
        return $this->Online;
    }

    public function setOnline($x) {
        $this->Online = $x;
    }
	
	public function getId_Perfil() {
        return $this->Id_Perfil;
    }

    public function setId_Perfil($x) {
        $this->Id_Perfil = $x;
    }
	
    public function getId_Area() {
        return $this->Id_Area;
    }

    public function setId_Area($x) {
        $this->Id_Area = $x;
    }


    public function ActualizarDatosUsuario() {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        $id = $this->id;
        
        $cad = "UPDATE from `tb_usuario` WHERE id=$id";
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

    
    public function IngresarDatosUsuario($nombre,$apellido,$usuario,$password,
                                         $dni,$id_perfil,$id_area) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
		$claveSHA = sha1($password);
		
        $cad = "INSERT INTO `tb_usuario` (nombre, apellido,usuario,password,dni,online,
			id_perfil,id_area,status)
			VALUES ('$nombre','$apellido','$usuario',
			'$claveSHA',$dni,0,$id_perfil,$id_area,1)";
        
		$res = mysql_query($cad, $cnx) or die(mysql_error());
        if($res)
			$x = 1;
	    else
			$x = 0;

        $cnx = NULL;
        $db = NULL;
		
        return $x;
	
    }
	
    public function ListadoUsuarios() {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        //FILTROS
        $filtros = $this->Filtros;
        $where = "";
        if($filtros["filter"]== "ape"){
            $where  =  "  and CONCAT_WS(' ',u.apellido,u.nombre) like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "dni"){
            $where  =  "  and u.dni like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "perfil"){
            $where  =  "  and u.id_perfil =".$filtros["value"]."  ";
        }elseif($filtros["filter"]== "area"){
            $where  =  "  and u.id_area =".$filtros["value"]."  ";
        }elseif($filtros["filter"]== "empresa_principal"){
            $where  =  "  and u.ideecc =".$filtros["value"]."  ";
        }

        //paginacion
        $this->inicio = ($this->pagina - 1) * $this->tamano;

        $cad = "SELECT  u.id, u.nombre, u.apellido, u.usuario, u.dni, p.perfil as perfil, a.area as area, 
				u.status
                FROM `tb_usuario` u, tb_perfil p, tb_area a
				WHERE u.id_perfil=p.id AND u.id_area=a.id
				$where
                ORDER BY u.`apellido` ASC
                 Limit ".$this->inicio.",".$this->tamano;
		$deb = 1;
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

    public function paginacion($filtro = array()){

        $db = new Conexion();
        $cnx = $db->conectarBD();



        //FILTROS
        $filtros = $this->Filtros;
        $where = "";
        if($filtros["filter"]== "ape"){
            $where  =  "  and CONCAT_WS(' ',u.apellido,u.nombre) like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "dni"){
            $where  =  "  and u.dni like '%".$filtros["value"]."%'  ";
        }elseif($filtros["filter"]== "perfil"){
            $where  =  "  and u.id_perfil =".$filtros["value"]."  ";
        }elseif($filtros["filter"]== "area"){
            $where  =  "  and u.id_area =".$filtros["value"]."  ";
        }elseif($filtros["filter"]== "empresa_principal"){
            $where  =  "  and u.ideecc =".$filtros["value"]."  ";
        }

        $cad = "SELECT  1
                FROM `tb_usuario` u, tb_perfil p, tb_area a
				WHERE u.id_perfil=p.id AND u.id_area=a.id
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

	public function Deshabilitar($idUsuario) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "UPDATE `tb_usuario` SET status=0 WHERE id=$idUsuario ";
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        if ($res)
			$x = 1;
		else
			$x = 0;
        $cnx = NULL;
        $db = NULL;
        return $x;
    }

	public function Habilitar($idUsuario) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "UPDATE `tb_usuario` SET status=1 WHERE id=$idUsuario ";
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        if ($res)
			$x = 1;
		else
			$x = 0;
        $cnx = NULL;
        $db = NULL;
        return $x;
    }

	public function ClonarPerfil($idusuario_modelo, $nombre, $apellidos, $login, $password, $dni) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad = "SELECT id_perfil, id_area FROM `tb_usuario` WHERE id=$idusuario_modelo ";
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        if ($res) {
			$row = mysql_fetch_array($res);
			$id_perfil_modelo = $row["id_perfil"];
			$id_area_modelo = $row["id_area"];
			
			$this->IngresarDatosUsuario($nombre,$apellidos,$login,$password,$dni,$id_perfil_modelo,$id_area_modelo);
			
			$cad1 = "SELECT LAST_INSERT_ID()";
			$res1 = mysql_query($cad1);
			$r = mysql_fetch_row($res1);
			$id_new_usuario = $r[0];
			
			$cad2 = "INSERT INTO `usuarios_submodulos` (idusuario, idsubmodulo)
					SELECT $id_new_usuario, idsubmodulo FROM usuarios_submodulos 
					WHERE idusuario=$idusuario_modelo";
			//echo $cad2;
			$res2 = mysql_query($cad2) or die(mysql_error());
			if ($res2)
				$x = 1;
			else
				$x = 0;
		}
		else
			$x = 0;

		$cnx = NULL;
        $db = NULL;
		
        return $x;
    }


    public function CambiarClave($idUsuario, $passwdActual, $passwdNueva ) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        
        $cad0 = "SELECT id FROM `tb_usuario` WHERE id=$idUsuario AND password=sha1('$passwdActual') ";
        //echo $cad0;
        $res0 = mysql_query($cad0);
        if ($res0) {
            if (mysql_num_rows($res0) > 0 ) {
                $cad = "UPDATE `tb_usuario` SET password=TRIM(SHA1('$passwdNueva')) WHERE id=$idUsuario LIMIT 1 ; ";
                //echo "<b>".$cad."</b>";
                $res = mysql_query($cad, $cnx) or die(mysql_error());
                if ($res)
                    $x = 1;   // cambio de clave ok
                else
                    $x = 0;   // ocurrio un error al cambiar la clave
            }
            else
                $x = 2;   // clave no coincide
        }
        else
            $x = 0 ; // ocurrio un error;

        $cnx = NULL;
        $db = NULL;
        return $x;
    }
	
    public function listarEECCxUsuario($idusuario) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        global $BD;

        $cad = "SELECT a.ideecc, b.eecc
                FROM `usuarios_eecc` a, `tb_eecc` b
                WHERE a.idusuario = $idusuario
                AND b.id=a.ideecc
                ORDER BY b.eecc ASC;";
		//echo $cad;
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
	

    public function listarZonalesxUsuario($idusuario) {
        $db = new Conexion();
        $cnx = $db->conectarBD();
        global $BD;

        $cad = "SELECT a.zonal, b.zonal_desc
                FROM webpsi.`usuario_zonales` a, webpsi.zonales b
                WHERE a.idusuario = $idusuario
                AND a.zonal = b.zonal
                AND a.status=1
                ORDER BY b.zonal_desc ASC;";
		//echo $cad;
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function getPerfiles(){

        $cnx = $this->cnx;

        $cad = "select id, perfil nombre from tb_perfil where estado = 1";
        //echo $cad;
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    public function getAreas(){

        $cnx = $this->cnx;

        $cad = "select id, area nombre from tb_area where estado = 1";
        //echo $cad;
        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }
        //var_dump($row);
        $cnx = NULL;
        $db = NULL;
        return $arr;
    }

    function getOtionsHTML($rows = array() , $idseleted = ""){



        $options = "";
        if(count($rows))
        {
            foreach($rows as $row)
            {
                $selected = "";
                if($row["id"] == $idseleted)
                {
                    $selected = "selected";
                }
                $options .= "<option value='".$row[0]."' $selected>". $row[1]. "</option>";
            }
        }


        return $options;

    }

    public function editarUsurio($data = array())
    {
        //CREAMOS EL USUARI

        $nombre         = $data["nombre"];
        $apellido       = $data["apellido"];
        $usuario        = $data["usuario"];
        $pass           = $data["password"];
        $dni            = $data["dni"];
        $online         = $data["online"];
        $idperfil       = $data["id_perfil"];
        $idarea         = $data["id_area"];
        $estado         = $data["estado"];
        $ideecc         = $data["ideecc"];
        $quiebres       = $data["quiebres"];
        $empresas       = $data["empresas"];
        $empcri         = $data["emp_cri"];
        $ususub         = $data["usu_sub"];
        $idusuario      = $data["id"];
        $proyectos      = $data["proy"];

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try {

            $cnx->beginTransaction();

            //VALIDAR SI EXISTE LOGIN EN OTRO USUARIO
            $sql = "select id from webpsi.tb_usuario where usuario='$usuario' and id <> $idusuario";
            $res = $cnx->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);

            if (!empty($row["id"])) {
                return json_encode(array("error" => 1, "msj" => "El login \"$usuario\" ya existe en otro usuario",));
            }

            $sql = "select id from webpsi.tb_usuario where dni='$dni' and id <> $idusuario";
            $res = $cnx->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);

            if (!empty($row["id"])) {
                return json_encode(array("error" => 1, "msj" => "El Dni \"$dni\" ya existe en otro usuario",));
            }

            //ACTUALIZAMOS EL USUARIO
            //revisamos si cambio de clave
            if ($pass == "******") {
                $insert_update_pass = "";
            } else {
                $claveSHA = sha1($pass);
                $insert_update_pass = "  password = '$claveSHA' , ";
            }

            $sql = "
                    update webpsi.tb_usuario  SET
                        nombre = '$nombre' , apellido  ='$apellido' ,
                        usuario = '$usuario', $insert_update_pass
                        dni = '$dni', online = $online , id_perfil = $idperfil ,
                        id_area = $idarea , status = $estado , ideecc = $ideecc
                        where id = $idusuario
                    ";
            $cnx->exec($sql);

            $id_new_usuario = $idusuario;

            $DELSQL = "delete from webpsi.usuarios_eecc where idusuario = $idusuario";
            $cnx->exec($DELSQL);
            //insertantado otras empresas
            $otras_empresas = explode(",", $empresas);
            $deb = 1;
            foreach ($otras_empresas as $emp) {
                $sql = "insert into  usuarios_eecc set
                  idusuario = $id_new_usuario , ideecc = $emp , activo   = 1";
                $cnx->exec($sql);
            }


            $DELSQL = "DELETE from webpsi.tb_usuario_quiebre where id_usuario =  $idusuario";
            $cnx->exec($DELSQL);

            //insertantado quiebres
            $idquiebres = explode(",", $quiebres);
            foreach ($idquiebres as $quiebre) {
                $sql = "insert into webpsi.tb_usuario_quiebre set
                    id_usuario = $id_new_usuario , id_quiebre = $quiebre , cestado = 1";
                $cnx->exec($sql);
            }

            //USUARIO CONTRATAS CRITICOS
            $DELSQL = "DELETE from webpsi_criticos.usuarios_contratas_criticos where id_usuario = $idusuario";
            $cnx->exec($DELSQL);

            $contratas = explode("|", $empcri);
            foreach ($contratas as $con) {
                list($emp, $vis) = explode("-", $con);
                $sql = "insert into webpsi_criticos.usuarios_contratas_criticos set
                    id_usuario = $id_new_usuario , id_empresa = $emp , visible = $vis";
               $cnx->exec($sql);
            }


            //PROYECTOS ASIGNADOS
            $DELSQL = "DELETE from  webpsi.geo_proyecto_usuarios where usuario_id = $idusuario";
            $cnx->exec($DELSQL);
            $proyectos = explode("|",$proyectos);
            foreach($proyectos as $proyecto){
                if(!empty($proyecto)){
                    list($proy , $vis) = explode("-",$proyecto);
                    $sql  ="insert into webpsi.geo_proyecto_usuarios set
                    usuario_id = $id_new_usuario , geo_proyecto_id = $proy , editar = $vis , fec_registro = NOW()";
                    $cnx->exec($sql);
                }
            }

            //SUBMODULOS
            $DELSQL = "DELETE From webpsi.usuarios_submodulos where idusuario =  $idusuario";
            $cnx->exec($DELSQL);

            if (!empty($ususub)) {
                $pormodulos = explode("|", $ususub);
                foreach ($pormodulos as $modulo) {
                    list($modulo, $submodulos) = explode("-", $modulo);
                    $submodulos_array = explode(",", $submodulos);
                    foreach ($submodulos_array as $sub) {
                        $sql = "insert into webpsi.usuarios_submodulos set
                        idusuario = $id_new_usuario , idsubmodulo =$sub";
                        $cnx->exec($sql);
                    }
                }
            }


            $cnx->commit();
            return json_encode(array("error" => 0, "msj" => "Usuario actualizado Correctamente",));

        }catch (PDOException $e){

            $cnx->rollBack();
            return json_encode(array("error" => 1, "msj" => $e->getMessage()));

        }
    }


    public function crearUsuario($data = array())
    {
        //CREAMOS EL USUARIO

        $nombre         = $data["nombre"];
        $apellido       = $data["apellido"];
        $usuario        = $data["usuario"];
        $pass           = $data["password"];
        $dni            = $data["dni"];
        $online         = $data["online"];
        $idperfil       = $data["id_perfil"];
        $idarea         = $data["id_area"];
        $estado         = $data["estado"];
        $ideecc         = $data["ideecc"];
        $quiebres       = $data["quiebres"];
        $empresas       = $data["empresas"];
        $empcri         = $data["emp_cri"];
        $ususub         = $data["usu_sub"];
        $proyectos         = $data["proy"];

        $claveSHA = sha1($pass);
        $cnx = $this->cnx;

        $db = new Conexion();
        $cnx = $db->conectarPDO();

        try {

            $cnx->beginTransaction();

        //validar si existe login
        $sql = "select id from webpsi.tb_usuario where usuario='$usuario'";
            $res = $cnx->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);

        if(!empty($row["id"])){
            return json_encode(array("error"=>1,"msj"=>"El login \"$usuario\" ya existe",));
        }

        $sql = "select id from webpsi.tb_usuario where dni='$dni'";
            $res = $cnx->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);

        if(!empty($row["id"])){
            return json_encode(array("error"=>1,"msj"=>"El Dni \"$dni\" ya existe",));
        }

        //REGISTRAMOS USUARIO
        $sql= "
        insert into webpsi.tb_usuario  SET
            nombre = '$nombre' , apellido  ='$apellido' ,
            usuario = '$usuario' , password = '$claveSHA',
            dni = '$dni', online = $online , id_perfil = $idperfil ,
            id_area = $idarea , status = $estado , ideecc = $ideecc;
        ";
        $res = $cnx->exec($sql);

        $id_new_usuario = $cnx->lastInsertId();


        //insertantado otras empresas
        $otras_empresas = explode(",",$empresas);
        foreach($otras_empresas as  $emp){
            $sql = "insert into  usuarios_eecc set
                  idusuario = $id_new_usuario , ideecc = $emp , activo   = 1";
            $cnx->exec($sql);
        }

        //insertantado quiebres
        $idquiebres = explode(",",$quiebres);
        foreach($idquiebres as  $quiebre){
            $sql = "insert into webpsi.tb_usuario_quiebre set
                    id_usuario = $id_new_usuario , id_quiebre = $quiebre , cestado = 1";
           $cnx->exec($sql);
        }

        //USUARIO CONTRATAS CRITICOS
        $contratas = explode("|",$empcri);
        foreach($contratas as $con){
            if(!empty($con)){
                list($emp , $vis) = explode("-",$con);
                $sql  ="insert into webpsi_criticos.usuarios_contratas_criticos set
                    id_usuario = $id_new_usuario , id_empresa = $emp , visible = $vis";
                $cnx->exec($sql);
            }
        }

            //PROYECTOS ASIGNADOS
            $proyectos = explode("|",$proyectos);
            foreach($proyectos as $proyecto){
                if(!empty($proyecto)){
                    list($proy , $vis) = explode("-",$proyecto);
                    $sql  ="insert into webpsi.geo_proyecto_usuarios set
                    usuario_id = $id_new_usuario , geo_proyecto_id = $proy , editar = $vis , fec_registro = NOW()";
                    $cnx->exec($sql);
                }
            }


        //SUBMODULOS
        if(!empty($ususub)){
            $pormodulos = explode("|",$ususub);
            foreach($pormodulos as $modulo){
                list($modulo,$submodulos) = explode("-",$modulo);
                $submodulos_array = explode(",",$submodulos);
                foreach($submodulos_array as $sub){
                    $sql = "insert into webpsi.usuarios_submodulos set
                        idusuario = $id_new_usuario , idsubmodulo =$sub";
                    $cnx->exec($sql);
                }
            }
        }
            $cnx->commit();
            return json_encode(array("error" => 0, "msj" => "Usuario Registrado Correctamente",));

        }catch (PDOException $e){

            $cnx->rollBack();
            return json_encode(array("error" => 1, "msj" => $e->getMessage()));

        }
    }
	
	public function getSubmodulosJSON()
    {

        $cnx = $this->cnx;
        $cad = "select *
                from modulos m
                inner join submodulos sm on sm.idmodulo = m.idmodulo
                where m.`status` = 1 and sm.`status` = 1";

        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_assoc($res))
        {
            $arr[] = $row;
        }

        return json_encode($arr);


    }

    public function getModulos()
    {

        $cnx = $this->cnx;
        $cad = "select  idmodulo id , modulo nombre from modulos  where status = 1";

        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }

        return $arr;


    }

    public function getModulosOptionsHTML()
    {
        return $this->getOtionsHTML($this->getModulos());
    }

    public function getEECC()
    {

        $cnx = $this->cnx;
        $cad = "select   id , eecc nombre from tb_eecc  where estado = 1 ";

        $res = mysql_query($cad, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_row($res))
        {
            $arr[] = $row;
        }

        return $arr;


    }

    public function getEECCOptionsHTML()
    {
        return $this->getOtionsHTML($this->getEECC());
    }


    public function empresas_criticas_trs()
    {
        $html = "";
        $sql = "select id, nombre from webpsi_criticos.empresa where activo = 1";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_assoc($res))
        {   $deb = 1;
            $html .= "<tr class=\"cri-empresas\">
                        <td>".$row["nombre"]."</td>
                        <td style=\"text-align:center\"><input type='checkbox' class=\"cri emp id \" id=\"cri_emp_".$row["id"]."\" emp='".$row["id"]."' value='1' ></td>
                        <td style=\"text-align:center\"><input type='checkbox'  class=\"cri emp vis \" id=\"cri_vis_".$row["id"]."\" value='1'></td>
                    </tr>";
        }


        return $html;

    }

    public function proyectos_trs(){
        $html = "";
        $sql = "select id, nombre from webpsi.geo_proyectos where estado = 1";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx) or die(mysql_error());
        while ($row = mysql_fetch_assoc($res))
        {   $deb = 1;
            $html .= "<tr class=\"row-proyecto\">
                        <td>".$row["nombre"]."</td>
                        <td style=\"text-align:center\"><input type='checkbox' style='width:85px' class=\"proy id \" id=\"proy_id_".$row["id"]."\" proy='".$row["id"]."' value='1' ></td>
                        <td style=\"text-align:center\"><input type='checkbox' style='width:85px' class=\"proy vis \" id=\"proy_vis_".$row["id"]."\" value='1'></td>
                    </tr>";
        }


        return $html;
    }


    public function getDataUsuarioAll($idusuario){

        if(empty($idusuario)){
            return json_encode(array("error"=> 1 , "msj"=> "El id de usuario esta vacio"));
        }

        //DATOS DE USUARIO
        $sql = "select * from webpsi.tb_usuario where id = $idusuario";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["info"] = $data;

        //OTRAS EMPRESAS
        $sql = "select GROUP_CONCAT(ideecc)  empresas
                from webpsi.usuarios_eecc where idusuario = $idusuario and activo = 1";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["empresas"] = $data;

        //QUIEBRES
        $sql = "select GROUP_CONCAT(id_quiebre)  quiebres
                from webpsi.tb_usuario_quiebre where id_usuario = $idusuario and cestado = 1";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["quiebres"] = $data;


        //USUARIO CONTRATA CRITICOS
        $sql = "select GROUP_CONCAT(CONCAT(id_empresa,'-',visible) SEPARATOR '|')  data
                from webpsi_criticos.usuarios_contratas_criticos where id_usuario = $idusuario";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["empcri"] = $data;


        //PROYECTOS ASIGNADOS
        $sql = "select GROUP_CONCAT(CONCAT(geo_proyecto_id,'-',editar) SEPARATOR '|')  data
                from webpsi.geo_proyecto_usuarios pu where pu.usuario_id = $idusuario";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["proy"] = $data;


        //SUBMODULOS
        $sql = "SELECT GROUP_CONCAT( info SEPARATOR '|'  )  data
                from (
                select
                concat(
                mo.idmodulo , '-' ,
                GROUP_CONCAT(sub.idsubmodulo)) info
                 from webpsi.usuarios_submodulos uss
                inner join webpsi.submodulos sub on sub.idsubmodulo = uss.idsubmodulo
                inner join webpsi.modulos mo on mo.idmodulo = sub.idmodulo
                where uss.idusuario = $idusuario
                group by mo.idmodulo
                ) q1";
        $cnx = $this->cnx;
        $res = mysql_query($sql, $cnx);
        $data = mysql_fetch_assoc($res);
        $data_usuario["ususub"] = $data;


        return json_encode(array("error"=>0 , "data"=>$data_usuario ));
    }

}