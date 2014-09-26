<?php
/**
 * Created by PhpStorm.
 * User: lmori
 * Date: 25/09/14
 * Time: 12:16 PM
 */


class agendaController extends capacidadHorarios{

    protected $cnx;
    protected $vempresa;
    protected $vzona;

    /**
     * @return mixed
     */
    public function getCnx()
    {
        return $this->cnx;
    }

    /**
     * @param mixed $cnx
     */
    public function setCnx($cnx)
    {
        $this->cnx = $cnx;
    }

    /**
     * @return mixed
     */
    public function getVzona()
    {
        return $this->vzona;
    }

    /**
     * @param mixed $vzona
     */
    public function setVzona($vzona)
    {
        $this->vzona = $vzona;
    }

    /**
     * @return mixed
     */
    public function getVempresa()
    {
        return $this->vempresa;
    }

    /**
     * @param mixed $vempresa
     */
    public function setVempresa($vempresa)
    {
        $this->vempresa = $vempresa;
    }



    public function AgendarAveriaShow()
    {
        //Obteniendo Fecha Actual
        $dia = date("d");
        $numero_dia = date("w");
        $ini = date("Y/n/" . $dia);
        $fecha_ini = date("Y-m-d");

        //Aumentandole 7 dias
        $fecha = new DateTime($ini);
        $fecha->add(new DateInterval('P7D'));
        $fecha_fin = $fecha->format('Y-m-d');
        $data = $this->getAgendaLibre($this->cnx, $this->vzona, $this->vempresa, $fecha_ini, $fecha_fin , $numero_dia);
        //$static = $this->agendarAveriaContentStatic();
        $horarios = $data["data"];

        //Generando HTML
        //Obteniendo Fecha Actual
        $dia = date("d");
        $fecha_ini = date("Y/n/" . $dia);
        $cnx = $this->getCnx();
        //Aumentandole 7 dias
        $fecha = new DateTime($fecha_ini);
        $fecha->add(new DateInterval('P7D'));
        $fecha_fin = $fecha->format('Y-m-d');
        //creando la cabecera
        $id_mes = date("m");
        $table = '<table id="horario"><thead>';
        $table .= '<tr><th colspan=8>'
            . date("Y")
            . ' - '
            . capacidadHorarios::nombreMes($id_mes - 1)
            . '</th></tr>';

        $table .= '<tr><th>Hora</th>';

        for ($i = 0; $i < 7; $i++) {
            //sumando dias a la fecha
            $fec = new DateTime($fecha_ini);
            $fec->add(new DateInterval('P' . $i . 'D'));
            $fecha_res = $fec->format('Y-m-d');
            $dia = substr($fecha_res, 8, 2);
            $table .= "<th>"
                . capacidadHorarios::diasSemana($fecha_res)
                . "(" . $dia . ")</th>";
        }

        $table .= '</tr></thead><tbody>';
        //Obteniendo la cantidad de horarios
        $sql3 = "select count(*) 'total' from webpsi_criticos.horarios";
        $res3 = $cnx->query($sql3);
        $row3 = $res3->fetch(PDO::FETCH_ASSOC);
        $cantidadHorarios = $row3["total"];

        $num_celda_hora = 0;
        $num_celda_valor = 0;
        for ($i = 1; $i <= $cantidadHorarios; $i++) {

            $table .= '<tr><td title="'
                . $num_celda_hora
                . '" style="background:#c4e0f2">HORA</td>';
            $cont_dias = 0;

            $num_celda_valor++;
            foreach ($horarios as $data1) {

                if ($data1["horario"] == $i) {
                    $fec = new DateTime($fecha_ini);
                    $fec->add(new DateInterval('P' . $cont_dias . 'D'));
                    $fecha_res = $fec->format('Y-m-d');
                    $nombre_dia = capacidadHorarios::diasSemana($fecha_res);
                    $codigo_dia = capacidadHorarios::codigoDia($fecha_res);
                    $codigo_dia = ($codigo_dia == 0) ? 7 : $codigo_dia;
                    $estado = "";
                    $cant_libres = (!empty($data1["libre"]) && $data1["libre"] != "null" && $data1["libre"] != null)?$data1["libre"] : $data1["capacidad"];
                    $libres = "Total:"
                        . $data1["capacidad"]
//                        . "<br>capacidad:"
//                        . $data1["capacidad"]
                        . "<br>Libres"
                        . $cant_libres;
                    $hoy = date("Y-m-d");
                    if ($estado == "" && $hoy == $fecha_res) {
                        $hora = date("H") + 2;
                        if ($hora >= $data1["hora_fin"])
                        {
                            $estado = "background:#ffff00;color:#000";
                        }
                    }elseif($data1["ocupado"]>=$data1["capacidad"])
                    {
                        $estado = "background:#ffff00;color:#000";
                    }

                    //Imprimiendo estado del horario
                    $hora = $data1["hora"];
                    $table = str_replace("HORA", $hora, $table);
                    if ($nombre_dia == "domingoSSS") { // DOMINGO SE TOMARA EN CUENTA
                        $table .= '<td title="'
                            . $num_celda_valor
                            . '" data-fec="'
                            . $fecha_res
                            . '" data-horario="'
                            . $data1["horario"]
                            . '" data-dia="'
                            . $codigo_dia
                            . '" data-hora="'
                            . $data1["hora"]
                            . '" data-total="'
                            . $data1["capacidad"]
                            . '" style="background:#ffff00;color:#000">0</td>';
                    } else {
                        $table .= '<td title="'
                            . $num_celda_valor
                            . '" data-fec="'
                            . $fecha_res
                            . '" data-horario="'
                            . $data1["horario"]
                            . '" data-dia="'
                            . $codigo_dia
                            . '" data-hora="'
                            . $data1["hora"]
                            . '" data-total="'
                            . $data1["capacidad"]
                            . '" style="'
                            . $estado . '">' . $libres . '</td>';
                    }
                    $cont_dias++;
                    $num_celda_valor++;
                }
            }
            $table .= '</tr>';
            $num_celda_hora+=8;
        }
        $scripts = $this->agendarAveriaContentStatic();




        return  $table . $scripts;

    }


    public function agendarAveriaContentStatic()
    {

        ob_start();
        $PATH =  $_SERVER['DOCUMENT_ROOT']."/webpsi/";
        $deb = 1;
        require('/var/www/webpsi/modulos/historico/agenda/agenga.tpl.php');
//        include_once "agenda.tpl.php";
        $tpl_content = ob_get_clean();

        return $tpl_content;

    }




} 