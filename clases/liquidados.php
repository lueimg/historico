<?php

class Liquidados{

	function addLiquidados($cnx,$id_gestion,$contacto,$pruebas,$feedback,$cump_agenda
        	,$solucion_comercial,$critico,$estado,$fecha_liquidacion,$penalizable){
		
			$sql = "INSERT INTO webpsi_criticos.liquidados values ('',$id_gestion,'$contacto','$pruebas',$feedback,
				'$cump_agenda','$solucion_comercial','$critico','$estado','$fecha_liquidacion','$penalizable')";
			$res = $cnx->exec($sql);
        	return $res;
        
	}

	function getLiquidado($cnx, $id_gestion){
		$cad = "SELECT * FROM webpsi_criticos.`liquidados` where id_gestion = $id_gestion";
		
		$cnx->exec("set names utf8");
		$res = $cnx->query($cad);
		$row = $res->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function updateLiquidados($cnx,$id_gestion,$contacto,$pruebas,$feedback,$cump_agenda
        	,$solucion_comercial,$critico,$estado){
		
            $cnx->exec("set names utf8");
			$cad = "update webpsi_criticos.`liquidados` set contacto='$contacto',pruebas='$pruebas',feedback='$feedback'
			,cump_agenda='$cump_agenda',solucion_comercial='$solucion_comercial',critico='$critico',estado='$estado' 
			where id_gestion=$id_gestion";
			$res = $cnx->exec($cad);
            return $res;
	}

}

?>