<?php
require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");
require_once('../clases/empresa.php');
include_once "../../../clases/class.Conexion.php";
require_once('../clases/cedula.php');

require"../../officetrack/clases/class.Location.php";

$db = new Conexion();
$cnx = $db->conectarPDO();


$empresa = new Empresa();
$empresa->setCnx($cnx);
$empresa_options = $empresa->getEmpresaAllSelectOptions($idempresa);

$cedula = new Cedula();
$cedula->setCnx($cnx);
$cedula->setIdempresa($idempresa);
$celulas_options = $cedula->getCedulaAllByEmpresaSelectOptions();

$tecnico = new TecnicosCriticos();
$json_tecnicos_ot= $tecnico->TecnicosOfficetrackAll();

//location
$location = new Location();

$data = $location->getLocations($cnx, array("CY0002","LA0000"), $numArray = array(), $date = date("Y-m-d"));


header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>PSI - Web SMS - Mensajes Grupales</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <meta name="author" content="Sergio MC"/>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php include("../../../includes.php") ?>
    <script type="text/javascript" src="../js/js.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../estilos.css">
    <link rel="stylesheet" type="text/css" href="asistenciaTecnicos.css">

    <script type="text/javascript" src="../js/jquery.filter_input.js"></script>
    <script type="text/javascript" src="../js/prettify.js"></script>
    <script type="text/javascript" src="../js/jquery.multiselect.min.js"></script>
    <script type="text/javascript" src="../js/jquery.multiselect.filter.js"></script>
    <link type="text/css" href='../css/jquery.multiselect.css' rel="Stylesheet" />
    <link type="text/css" href='../css/jquery.multiselect.filter.css' rel="Stylesheet" />



    <script src="../js/json2.js"></script>
    <script src="../js/underscore-min.js"></script>
    <script src="../js/backbone-min.js"></script>

    <script src="asistenciaTecnicos.js"></script>
    <script>
        //cargo todos los tecnicos officetrack en json
        window.tecnicosOT = {}
        tecnicosOT = <?=$json_tecnicos_ot;?>;
        window.fecha = {}
        fecha.diaHoy = "<?=date("d/m/Y")?>"
    </script>
</head>
<body>

<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<div id="page-wrap">
    <?php echo pintar_cabecera(); ?>    <br/>
    <div id="div_res_grupal" class="div_res_grupal"
         style="border: 1px solid #304B73; padding-top: 0px; float:left; overflow-y: auto;
			 width: 100%;">


        <div id="filtros" class="form-group">


            <div>
                <div id="filtro_empresa" class="filtro-item" >
                    <label class="control-label">Empresa:</label>
                    <span>
                        <select class="fil_empresa" id="fil_empresa" name="fil_empresa" class="form-control">
                            <option value=''>-- Todos --</option>
                            <?= $empresa_options ?>

                        </select>
                    </span>

                    <label class="control-label">Celula:</label>
                    <span>
                        <select class="fil_celula" id="fil_celula" name="fil_celula" class="form-control">
                            <option value=''>-- Todos --</option>
                            <?= $celulas_options ?>

                        </select>
                    </span>
                </div>
                <div>
                    <label class="control-label">Tecnicos Officetrack:</label>
                    <span>
                        <select class="tecnicos_ot" id="tecnicos_ot" name="tecnicos_ot" class="form-control" multiple></select>
                    </span>
                </div>
                <div id="tipo-reporte" class="opciones">
                    <label class="control-label">Tipo de reportes:</label>
                    <span>
                        <input type="radio" name="reporteTipo" class="reporteTipo" id="repo_dia" value="repo_dia" checked/>
                        <label for="repo_dia"> Por dia</label>
                    </span>
                    <span>
                        <input type="radio" name="reporteTipo"  class="reporteTipo"  id="repo_rango" value="repo_rango"/>
                        <label for="repo_rango"> Por rango de fechas</label>
                    </span>
                </div>
                <div>
                    <label class="control-label">Fecha Asistencia:</label>
                    <span><input type="text" class="asistenciaFecha" id="asistenciaFecha" readonly/></span>

                    <span style="display: none" class="fechaFin">
                        <label class="control-label">Fecha Final:</label>
                        <span><input type="text" class="asistenciaFecha" id="asisFechaFin" readonly/></span>
                    </span>
                </div>
                <div>

                    <button id="mostrarAsistencia">Mostrar Asistencias</button>
                    <button id="reiniciarFiltros">Reiniciar Filtros</button>
                    <span><a href="#" id="AsisExportExcel">
                            <img src="../img/excel2007.png" alt="" width="20px" style="vertical-align: bottom;"/>
                            Exportar a excel</a></span>
                </div>
            </div>

        </div>

        <div id="asistenciaTecnicos">


        </div>


    </div>

    <div id="parentModal" style="display: none;">
        <div id="childModal" style="background: #fff;"></div>
        <div id="childModal_nuevo" style="background: #fff;"></div>





</body>
</html>
