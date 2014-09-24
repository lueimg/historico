<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . '/webpsi/clases/');

require_once ("../../../clases/class.Conexion.php");

class CelulasCriticos{

    protected $tamano = 10;
    protected $pagina = 1;
    protected $inicio = 0;

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



    public function ListarCelulasTodos($filtro = array()) {
        $db = new Conexion();
        $cnx = $db->conectarBD();


        $where  = " where 1 = 1 ";

        if(!empty($filtro)){
            if(!empty($filtro["idempresa"])){
                $where .= " and e.id = ".$filtro["idempresa"];
            }
            if(!empty($filtro["nombre"])){
                $where .= " and a.nombre like  '%".$filtro["nombre"]."%' ";
            }
        }


        //consiguiendo pagina

            $this->inicio = ($this->pagina - 1) * $this->tamano;


        $cad = "SELECT a.idcedula, a.nombre, a.estado, a.responsable, e.nombre as empresa, a.idempresa
                FROM webpsi_criticos.cedula a
                inner join webpsi_criticos.empresa e on  a.idempresa=e.id
                $where
				ORDER BY a.idcedula ASC
                Limit ".$this->inicio.",".$this->tamano;
		//echo $cad;
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

        $where  = " where 1 = 1 ";

        if(!empty($filtro)){
            if(!empty($filtro["idempresa"])){
                $where .= " and e.id = ".$filtro["idempresa"];
            }
            if(!empty($filtro["nombre"])){
                $where .= " and a.nombre like  '%".$filtro["nombre"]."%' ";
            }
        }

        $cad = "SELECT a.idcedula, a.nombre, a.estado, a.responsable, e.nombre as empresa, a.idempresa
                FROM webpsi_criticos.cedula a
                inner join webpsi_criticos.empresa e on  a.idempresa=e.id
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

    public function BuscarCelula($id) {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT a.idcedula, a.nombre, a.estado, a.responsable, e.nombre as empresa
                FROM webpsi_criticos.cedula a, webpsi_criticos.empresa e
				WHERE a.id_empresa=e.id and a.idcedula=$id; ";
		//echo $cad;
		$res = mysql_query($cad, $cnx) ;
        while ($row = mysql_fetch_array($res))
        {
            $arr[] = $row;
        }
        var_dump($arr);
        $cnx = NULL;
        $db = NULL;
        return $arr;
		
    }		
	
	public function EditarCelula( $idcelula, $idempresa , $nombre , $estado ) {
	
        $db = new Conexion();
        $cnx = $db->conectarBD();

        // LA TABLES ES CEDULA PERO DEBIERIA SER CELULA xD
        $cad = "UPDATE webpsi_criticos.cedula  SET
                    nombre = '$nombre', estado = $estado, idempresa = $idempresa
                     WHERE idcedula = $idcelula";
        $res = mysql_query($cad, $cnx) ;

        return $res;

	}

	public function CrearCelula( $idempresa, $nombre , $estado= 0 , $responsable = "Sin responsable" )
    {

            $db = new Conexion();
            $cnx = $db->conectarBD();

            // LA TABLES ES CEDULA PERO DEBIERIA SER CELULA xD
			$cad = "INSERT INTO webpsi_criticos.cedula SET
                    nombre = '$nombre', estado = $estado, idempresa = $idempresa , responsable='$responsable' ";
            $res = mysql_query($cad, $cnx) ;

			return "1";

	}
	
	
    public function ListarCelulas($idEmpresa) {
        $db = new Conexion();
        $cnx = $db->conectarBD();

        $cad = "SELECT c.idcedula, c.nombre as cedula, c.estado, c.idempresa
                FROM webpsi_criticos.cedula c
				WHERE c.idempresa=$idEmpresa
				ORDER BY c.nombre ASC; ";
		//echo $cad;
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
	
	
}

?>