<?php
require_once '../../../clases/class.Conexion.php';
require_once '../clases/capacidadHorarios.php';
require_once '../clases/empresa.php';
require_once '../clases/zonales.php';

require_once '../agenda/agendaController.php';

//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$Zonal = new Zonales();
$Empresa = new Empresa();
$CapHorario = new capacidadHorarios();

$AgendaController = new agendaController();

if ( isset($_POST["action"]) )
{
    $action = $_POST["action"];
    
    if ($action="getAgendaData")
    {
        $zonal_id = $Zonal->getIdZonal($cnx, $_POST["zonal"]);
        $eecc_id = $Empresa->getIdEmpresa($cnx, $_POST["eecc"]);
        
        $inicio = date("Y-m-d");
        $fin = date("Y-m-d", strtotime($inicio) + (7*3600));  
        
        $dia = date("w", strtotime($inicio));
        
        $AgendaController->setCnx($cnx);
        $AgendaController->setVzona($zonal_id);
        $AgendaController->setVempresa($eecc_id);
        $agenda = $AgendaController->AgendarAveriaShow();
        
        echo $agenda;
        
        
        /*
        $agenda = $CapHorario->getAgendaLibre(
                $cnx, $zonal_id, $eecc_id, $inicio, $fin, $dia
                );
        
        
        $agendaHtml = "<table><tr><th colspan=\"\"></th></tr>";
        
        foreach($agenda["data"] as $key=>$val){
            
            //filas y cupos
            $agendaHtml .= "<tr>"
                            . "<td style=\"width: 50px\">{$val["hora"]}</td>"
                            . "</tr>";
        }
        
        $i = 1;
        foreach($agenda["data"] as $key=>$val){
            
            //filas y cupos
            if ($i===1)
            {
                $agendaHtml .= "<tr>"
                            . "<td>{$val["hora"]}</td>";
            }
            
            $agendaHtml .= "<td>";
            $agendaHtml .= "Total: " . $val["capacidad"] . "<br>";
            $agendaHtml .= "Libres: " . $val["libre"] . "<br>";
            $agendaHtml .= "</td>";   
            
            if ($i===7)
            {
                $agendaHtml .= "</tr>";
                $i=0;
            }
            $i++;
        }
        $agendaHtml .= "</table>";
        
        echo $agendaHtml;
         * 
         */
    }
    
}