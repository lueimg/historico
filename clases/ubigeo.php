<?php

class Ubigeo
{

    protected $_db = 'webpsi';
    protected $_table = 'ubigeo';

    public function listar($conexion, $coddep, 
            $codpro, $coddis, $ubigeo, $nombre){
        try {
            $sql = "SELECT coddep, codpro, coddis, ubigeo, nombre  
                    FROM " . $this->_db . "." . $this->_table . 
                    " WHERE id<>''";
            if ( $coddep !== "" ) {
                $sql .= " AND coddep='$coddep'";
            }
            if ( $codpro !== "" ) {
                $sql .= " AND codpro='$codpro'";
            }
            if ( $coddis !== "" ) {
                $sql .= " AND coddis='$coddis'";
            }
            if ( $ubigeo !== "" ) {
                $sql .= " AND ubigeo='$ubigeo'";
            }
            if ( $nombre !== "" ) {
                $sql .= " AND nombre LIKE '%$ubigeo%'";
            }
            $bind = $conexion->prepare($sql);
            $bind->execute();

            $reporte = array();
            while ($data = $bind->fetch(PDO::FETCH_ASSOC)) {
                $reporte[] = $data;
            }
            return $reporte;
        } catch (PDOException $e) {
            exit();
        }
    }
    
    public function listarDistritos($conexion, $coddep, $codpro){
        try {
            $sql = "SELECT coddep, codpro, coddis, ubigeo, nombre  
                    FROM " . $this->_db . "." . $this->_table . 
                    " WHERE id<>''";
            if ( $coddep !== "" ) {
                $sql .= " AND coddep='$coddep'";
            }
            if ( $codpro !== "" ) {
                $sql .= " AND codpro='$codpro'";
            }
            
            $sql .= " AND coddis<>'00' AND estado=1";
            
            $bind = $conexion->prepare($sql);
            $bind->execute();

            $reporte = array();
            while ($data = $bind->fetch(PDO::FETCH_ASSOC)) {
                $reporte[] = $data;
            }
            return $reporte;
        } catch (PDOException $e) {
            exit();
        }
    }

}