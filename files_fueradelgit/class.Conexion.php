<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sergio
 * Date: 02/09/12
 * Time: 08:40 PM
 * To change this template use File | Settings | File Templates.
 */

class Conexion
{
    private $Conexion_ID;

    //public static $HOST = "10.226.44.223";
    //public static $USER = "webpsi";
//    public static $PASSWD = "webpsi59u";
    public static $HOST = "192.168.1.3";
    public static $USER = "lmori";
    public static $PASSWD = "123456";
//    public static $HOST = "localhost";
//    public static $USER = "root";
//    public static $PASSWD = "123";
    public static $BD = "webpsi";

    function conectarBD()
    {
        $this->Conexion_ID = mysql_connect(self::$HOST, self::$USER, self::$PASSWD);
        mysql_select_db(self::$BD, $this->Conexion_ID);
        return $this->Conexion_ID;
    }

	function conectarPDO()
	{
	    try {
			//$db = new PDO("mysql:host='".self::$HOST."';dbname='".self::$BD."', '".self::$USER."', '",self::$PASSWD."'"  );
			// vale $db = new PDO('mysql:host=10.226.44.223;dbname=webpsi', 'webpsi', 'webpsi59u');
            $db = new PDO('mysql:host='.self::$HOST.';dbname='.self::$BD.';charset=utf8', self::$USER, self::$PASSWD);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	        return($db);
	    } catch (PDOException $e) {
	        print "Error: No puede conectarse con la BASE de datos.";
	        exit();
	    }

	}

    function conectarBD_mysqli() {
        $link = mysqli_connect(self::$HOST, self::$USER, self::$PASSWD, self::$BD )
            or die("Error " . mysqli_error($link));
        $this->Conexion_ID = $link;
        return $link;

    }

    function query_mysqli($cadena) {
        $cnx = $this->Conexion_ID;
        $res = $cnx->query($cadena);
        return $res;
    }

    function numrows($res) {
        return $res->num_rows;
    }

}



