<?php
$tipo = trim($_REQUEST["tipo"]);
//echo $tipo; die();

include ("../../clases/class.Conexion.php");

//print "\xEF\xBB\xBF"; // UTF-8 BOM

function limpia_cadena_rf($value) {
        $input = trim( preg_replace( '/\s+/', ' ', $value ) );
        return $input;
}

function limpia_campo_mysql_text($text)
{
    $text = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $text);
    return str_replace("<br />","\n",$text);
}

function limpia_cadena($value)
{
   $nopermitidos = array("'",'\\','<','>',"\"",";"); 
   $nueva_cadena = str_replace($nopermitidos, "", $value); 
   return $nueva_cadena;
}

$objCnx = new Conexion();

$cnx2 = $objCnx->conectarBD();

if ($tipo=="stb") {
	$arr[0][0] = "/tmp/stb.csv";	
	$arr[0][1] = "SELECT * FROM peter.basica ";			
}
else if ($tipo=="adsl") {
	$arr[0][0] = "/tmp/adsl.csv";	
	$arr[0][1] = "SELECT * FROM peter.speedy ";			
}
else if ($tipo=="catv") {
	$arr[0][0] = "/tmp/catv.csv";	
	$arr[0][1] = "SELECT * FROM peter.catv ";			
}
			
foreach ($arr as $fila) {
	//echo "\n\n ### GENERANDO REPORTES CSV -- ".$fila[0];

	$f = fopen($fila[0], "w");
	$cad = $fila[1];
	$res = mysql_query($cad, $cnx2 );
	
	while ($row0 = mysql_fetch_assoc($res ) ) {
		$array[] = $row0 ; 
	}
	$h = array();
	
	foreach($array as $row){
		foreach($row as $key=>$val){
			if(!in_array($key, $h)){
				$keyUp = strtoupper($key);
				$h[] = $key; 
				//echo "|".$keyUp;
			}
		}
	}
	
	// Poner en mayusculas la cabecera
	foreach ($h as $h2) 
		$h3[] = strtoupper($h2);
		
	fputcsv($f, $h3, ",");
	
    foreach ($array as $line) {
        fputcsv($f, $line, ",");
    }
	
	fclose($f);
	
	unset($h3);
	unset($h2);
	unset($h);
}

mysql_close($cnx2);


header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$tipo.'.csv');
header('Pragma: no-cache');
readfile("/tmp/".$tipo.".csv");



?>
