<?php
header("Content-Type:  application/x-msexcel");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Disposition: attachment; filename=averias_criticos.xls");
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


//$cad = "SELECT * FROM webpsi_coc.averias_criticos_final ORDER BY 1 ASC ";
$cad = "
SELECT f.*,
(SELECT IF(horas_registro<24, '00-24', IF(horas_registro>24 AND horas_registro<48, '24-48', 
	IF(horas_registro>48, '>48', '')))) AS rango_registro,
(SELECT IF(horas_subida<24, '00-24', IF(horas_subida>24 AND horas_subida<48, '24-48', 
	IF(horas_subida>48, '>48', '')))) AS rango_subida,
(SELECT IF(horas_ult_mov<24, '00-24', IF(horas_ult_mov>24 AND horas_ult_mov<48, '24-48', 
	IF(horas_ult_mov>48, '>48', '')))) AS rango_ult_mov
FROM (SELECT a.*, 
ROUND(TIMESTAMPDIFF(SECOND, a.fecha_registro, NOW()) / 3600,2) AS horas_registro,
ROUND(TIMESTAMPDIFF(SECOND, a.fecha_subida, NOW()) / 3600,2) AS horas_subida,
ROUND(TIMESTAMPDIFF(SECOND, a.fecha_ult_mov, NOW()) / 3600,2) AS horas_ult_mov
FROM webpsi_criticos.`viewInformePorEstados` a WHERE a.eecc_zona!='' 
) AS f
";
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
