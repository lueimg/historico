<?php
set_time_limit(0);
function limpia_cadena($value)
{
   $nopermitidos = array("'",'\\','<','>',"\"",";");
   $nueva_cadena = str_replace($nopermitidos, "", $value);
   return $nueva_cadena;
}
 
 
/**
* Conexion MySQL
*/
$dbHost = "10.226.44.223";
$dbUser = "initium_procesos";
$dbPass = "oRovvk";
$dbName = "sergio";
$mysql = mysql_connect($dbHost, $dbUser, $dbPass);
mysql_select_db($dbName);
 
/**
* Carga de acrchivo csv
*/
if (isset($_FILES["userfile"])) {
    $uploaddir = 'files/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
 
    echo '<pre>';
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "El archivo es válido y fue cargado exitosamente.\n";
 
        /**
         * Limpiar tabla
         */
        mysql_query("DELETE FROM iquitos_web;");
 
        $fila = 1;
        if (($gestor = fopen($uploadfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                $numero = count($datos);
 
                $fila++;
                $dataLoad = "";
                for ($c = 0; $c < $numero; $c++) {
                    $dataLoad .= ", '" . limpia_cadena($datos[$c]). "'";
                }
                $dataLoad .= ", ''";
                                /* $sql = "INSERT INTO iquitos_web (nropeti,telefono,producto,sub_produc,cod_ps,ps,caterogira,sub_cat_ps,cod_paq,paquete,cat_paq,sub_cat_pq,geografia,region,zonal,depart,provin,distrito,segmen,canal,entidad,cod_pto_v,pto_ven,nro_peti,est_peti,est_agru,est_subpet,mot_subpet,fech_reg,id_cli,nom_cli,ape_pat,ape_mat,tip_doc,num_doc,ruc_doc,mdf,geo_inst,area_inst,prov_inst,muni_inst,local_inst,direccion,bolsa,des_esta,com_corto,linea_orig,adsl_orig,catv_orig,serv,estado,fecha,codmot,motivo,userultimo,llave,dependen,sit,marca,des_marca,reg_total,pdte_regis,reg_atis,pen_asig1,pen_cmr1,can_pre_os,ooss,pen_cgest,pen_inst,pen_sist1,pen_sist2,pen_eecc,pen_pai,pen_otro1,pen_otro2,pen_asig2,pen_cmr2,can_pos_os,convertx,fecreg,mesreg,fuente,flag,contacto,edo_ges,det_gest,subdet_ges,detobs_pai,usu_atis,canal_v,a_adsl_l,a_adsl_p,a_tba_l,a_tba_p,t_adsl_l,t_adsl_p,t_tba_l,t_tba_p,liq,f_liq,cancel,d304,nombre_cliente) "
                        . "VALUES (" . substr($dataLoad, 1) . ")"; */
                                               
                                $sql = "INSERT INTO iquitos_web (nropeti, telefono, cod_ps, ps, sub_cat_pq, distrito,
                                                        segmen, canal, id_cli, fech_reg, nom_cli,ape_pat,ape_mat,num_doc,ruc_doc,
                                                        direccion,serv,estado,mdf, a_adsl_l,a_adsl_p,a_tba_l,a_tba_p,t_adsl_l,t_adsl_p,t_tba_l,t_tba_p,
                                                        liq,f_liq,cancel,d304,peti2, obs, campo1, reingreso ) "
                                                . "VALUES (" . substr($dataLoad, 1) . ")";                                             
                                               
                //die($sql);
                $query = mysql_query($sql) or die(mysql_error()." -- ".$sql);
            }
            fclose($gestor);
        }
 
        /**
         * UPDATE nombre de cliente
         */
                //mysql_query("ALTER TABLE iquitos_web ADD COLUMN nombre_cliente VARCHAR(255) NULL ;") or die(mysql_error());
        mysql_query("UPDATE iquitos_web "
                . "SET nombre_cliente = "
                . "CONCAT(nom_cli, ' ', ape_pat, ' ', ape_mat)");
 
    } else {
        echo "¡Posible ataque de carga de archivos!\n";
    }
 
    echo 'Aquí hay más información de depurado:';
    print_r($_FILES);
 
    print "</pre>";
 
    /**
     * Cerrar conexion
     */
    mysql_close($mysql);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
 
        <form enctype="multipart/form-data" action="" method="POST">
            <!-- El nombre del elemento de entrada determina el nombre en el array $_FILES -->
            Enviar este archivo: <input name="userfile" type="file" />
            <input type="submit" value="Send File" />
        </form>
 
    </body>
</html>