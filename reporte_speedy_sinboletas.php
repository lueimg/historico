<?php
header("Content-Type:  application/x-msexcel");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Disposition: attachment; filename=reporte_speedy_sinboletas.xls");
header("Expires: 0");


include ("../../clases/class.Conexion.php");

print "\xEF\xBB\xBF"; // UTF-8 BOM



function limpia_cadena_rf($value) {
        $input = trim( preg_replace( '/\s+/', ' ', $value ) );
        return $input;
}

function limpia_campo_mysql_text($text)
{
    $text = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $text);
    return str_replace("<br />","\n",$text);
}

function writeRow($val) {
    echo '<td>'.limpia_campo_mysql_text($val).'</td>';              
}
function limpia_cadena($value)
{
   $nopermitidos = array("'",'\\','<','>',"\"",";"); 
   $nueva_cadena = str_replace($nopermitidos, "", $value); 
   return $nueva_cadena;
}

$objCnx = new Conexion();

$cnx = $objCnx->conectarPDO();




//$cad = "SELECT * FROM webpsi_coc.averias_criticos_empresas ORDER BY 1 ASC ";

/*


*/
$cad = "SELECT b.`fecha_liquidacion`, b.`telefono`,  b.`orden`, b.`solicitud`, b.`contrata`, codigo_servicio, movimiento, prefijo
FROM  `facturas_2013`.`speedy_indice_2014` a
RIGHT JOIN `schedulle_sistemas`.`prov_liq_bas_pais_2014` b
ON a.telefono=b.telefono AND a.`ooss`=b.`orden`
WHERE b.`ciudad`='00990' AND b.cod_negocio='03'
AND prefijo!='BF'
AND a.`telefono` IS NULL";





$res = $cnx->query($cad);
$array = $res->fetchAll(PDO::FETCH_ASSOC);

foreach($array as $row){
	foreach($row as $key=>$val){
		if(!in_array($key, $h)){
			$h[] = $key;   
		}
	}
}
echo "<table border='1' cellpadding='0' cellspacing='0'><tr>";

foreach($h as $key) {
	$key = ucwords($key);
	echo '<th>'.$key.'</th>';
}
echo '</tr>';


foreach($array as $row){
	echo '<tr>';
	foreach($row as $val)
		writeRow($val);   
}
echo '</tr>';

echo '</table>';

?>
