<?php

class Averias{
	
	function getAverias($cnx, $averia){

		$cad = "SELECT * FROM webpsi_coc.`averias_criticos_final` where averia = '$averia'";
		$cnx->exec("set names utf8");
		$res = $cnx->query($cad);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
	
	function getAveriaxFono($cnx, $fono){
		$cad = "SELECT averia FROM webpsi_coc.`averias_criticos_final` where telefono_codclientecms = '$fono'";
		
		$cnx->exec("set names utf8");
		$res = $cnx->query($cad);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row["averia"];
	}

}

?>