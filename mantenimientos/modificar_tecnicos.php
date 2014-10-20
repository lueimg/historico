<?php
require_once("../../../cabecera.php");
require_once("../clases/class.TecnicosCriticos.php");
require_once('../clases/empresa.php');
include_once "../../../clases/class.Conexion.php";
require_once('../clases/cedula.php');


$db = new Conexion();
$cnx = $db->conectarPDO();

if(!empty($_SESSION["filtro_tec"]["idempresa"]))
{
    $idempresa = $_SESSION["filtro_tec"]["idempresa"];
}

$empresa = new Empresa();
$empresa->setCnx($cnx);
$empresa_options = $empresa->getEmpresaAllSelectOptions($idempresa);


$cedula = new Cedula();
$cedula->setCnx($cnx);
$cedula->setIdempresa($idempresa);
$celulas_options = $cedula->getCedulaAllByEmpresaSelectOptions();

//$celulas_options

$tecnico = new TecnicosCriticos();

//Agregando paginacion
$tecnico->setTamano(8);
$tecnico->setPagina($_GET["pagina"]);
$tecnico->setInicio(0);
$tecnico->setFiltros($_SESSION["filtro_tec"]);

$arr = $tecnico->ListarTecnicosTodos();
$html_paginacion = $tecnico->paginacion();
//print_r($arr);




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


    <script type="text/javascript">
        function deshabilitar_usuario(idtecnico) {
            var pagina = "modificar_tecnicos_ajax.php";
            var envio = "deshabilitar_tecnico=1&idtecnico=" + idtecnico;

            //alert(envio);

            $.ajax({
                type: "POST",
                url: pagina,
                data: envio,
                success: function (html) {
                    var resp = jQuery.trim(html);
                    location.reload();
                    //window.location.reload();
                }
            });
        }

        function habilitar_usuario(idtecnico) {
            var pagina = "modificar_tecnicos_ajax.php";
            var envio = "habilitar_tecnico=1&idtecnico=" + idtecnico;

            //alert(envio);

            $.ajax({
                type: "POST",
                url: pagina,
                data: envio,
                success: function (html) {
                    var resp = jQuery.trim(html);
                    location.reload();
                }
            });
        }

        function editar_tecnico(idtecnico) {

            $("#childModal").html("");

            $.post("editar_tecnico.php", {
                idtecnico: idtecnico,
                action: "editar.tecnico"
            }, function (data) {
                $("#childModal").html(data);
            });

            $("#childModal").dialog({
                modal: true,
                width: '40%',
                hide: 'slide',
                title: 'Editar tecnico',
                position: 'top'
            });

        }

        //CREA UN NUEVO TECNICO
        function nuevo_tecnico(idtecnico) {

            $("#childModal_nuevo").html("");

            $.post("nuevo_tecnico.php", {
                idtecnico: idtecnico,
                action: "nuevo_tecnico"
            }, function (data) {
                $("#childModal_nuevo").html(data);
            });

            $("#childModal_nuevo").dialog({
                modal: true,
                width: '40%',
                hide: 'slide',
                title: 'Nuevo tecnico',
                position: 'top'
            });

        }


    </script>

    <link rel="stylesheet" type="text/css" href="../../../estilos.css">

    <style>
        #childModal2 {
            position: fixed;
            height: 80px;
            width: 200px;
            left: 35%;
            top: 50%;
            margin-left: -40px;
            margin-right: -100px;
        }

        .th_res_grupal2 {
            font: 10px "Century Gothic", "Trebuchet MS", Arial, Helvetica, sans-serif;
            color: #FFFFFF;
            background-color: #6E9CC8;
            text-align: center;
            border-bottom: 1px solid #e5eff8;
            border-left: 1px solid #e5eff8;
            padding: .3em 1em;
        }

        .td_res_grupal2 {
            font: 11px "Century Gothic", "Trebuchet MS", Arial, Helvetica, sans-serif;
            color: #000000;
            background: #E5F1F4;
            border-bottom: 1px solid #e5eff8;
            border-left: 1px solid #e5eff8;
            padding: .3em 1em;
            text-align: center;
            border-bottom: 1px solid #5A7399;

        }

        table {
            border-collapse: collapse;
        }

        #paginacion {
            text-align: center;
            font-weight: bold;
        }

        #paginacion a {
            text-align: center;
            font-weight: normal;
        }

        #filtros {
            margin: 20px;
        }

        #filtros .control-label {
            background: #1b5790;
            color: #fff;
            padding: 5px;
            width: 75px;
            display: inline-block;
            margin: 5px 0;
            border-radius: 3px;
        }

        #filtros select {
            width: 200px;
        }

        .nueva a {
            margin-top: 10px;
            display: inline-block;
            font-weight: bold;
        }

    </style>

</head>

<body>

<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<div id="page-wrap">
    <?php echo pintar_cabecera(); ?>

    <br/>

    <div id="div_res_grupal" class="div_res_grupal"
         style="border: 1px solid #304B73; padding-top: 0px; float:left; overflow-y: auto;
			 width: 780px;">
        <span style="padding: 20px" class="nueva"><a href="#" onclick="nuevo_tecnico();">[ Agregar Tecnico ]</a> </span>

        <div id="filtros" class="form-group">
            <label class="control-label">Filtros: </label>
            <span>
                <select name="filtro_principal" id="filtro_principal" class="form-control">
                    <option value="">Seleccione Fitro</option>
                    <option value="filtro_empresa">Filtrar por Empresa y celula</option>
                    <option value="filtro_nombre">Filtrar Por Apellidos y nombres</option>
                    <option value="filtro_carnet">Filtrar Por carnet</option>
                    <option value="filtro_carnet_critico">Filtrar Por carnet crítico</option>
                </select>
            </span>
            <span><input type="submit" value="Reiniciar filtros" id="btnReiniciar"/></span>

            <div>
                <div id="filtro_empresa" class="filtro-item" style="display: none">
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
                <div id="filtro_nombre" class="filtro-item" style="display: none">
                    <label class="control-label">Por Apellidos y Nombre:</label>
                    <span>
                        <input type="text" id="nombre_busqueda" class="form-control" placeholder=" Buscar ... "/>
                    </span>
                    <span>
                        <input type="submit" id="btnFiltrar_nombre" value=":: Filtrar" class="btnFiltrar form-control btn btn-primary"/>
                    </span>
                </div>
                <div id="filtro_carnet" class="filtro-item" style="display: none">
                    <label class="control-label">Por Carnet:</label>
                    <span>
                        <input type="text" id="carnet" class="form-control" placeholder=" Buscar ... "/>
                    </span>
                    <span>
                        <input type="submit" id="btnFiltrar_carnet" value=":: Filtrar" class="btnFiltrar form-control btn btn-primary"/>
                    </span>
                </div>
                <div id="filtro_carnet_critico" class="filtro-item" style="display: none">
                    <label class="control-label">Por Carnet critico:</label>
                    <span>
                        <input type="text" id="carnet_critico" class="form-control" placeholder=" Buscar ... "/>
                    </span>
                    <span>
                        <input type="submit" id="btnFiltrar_critico" value=":: Filtrar" class="btnFiltrar form-control btn btn-primary"/>
                    </span>
                </div>

            </div>

        </div>

        <table class="tabla_res_grupal" style="width: 100%;">
            <thead>
            <tr>
                <th class="th_res_grupal2">#
                </td>
                <th class="th_res_grupal2">Apellido Paterno
                </td>
                <th class="th_res_grupal2">Apellido Materno
                </td>
                <th class="th_res_grupal2">Nombres
                </td>
                <th class="th_res_grupal2">Empresa
                </td>
                <th class="th_res_grupal2">Carnet
                </td>
                <th class="th_res_grupal2">Carnet Critico
                </td>
                <th class="th_res_grupal2">Cedula
                </td>
                <th class="th_res_grupal2">Officetrack
                </td>
                <th class="th_res_grupal2">Acciones
                </td>
            </tr>
            </thead>
            <?php
            $i = 1;

            foreach ($arr as $fila) {
                $cbox = "<input type='checkbox' name='pg_checkboxs' value='" . $fila["id"] . "' />";
                ?>
                <tr>
                    <td class="td_res_grupal2" style="width:10px"><?php echo $cbox ?></td>
                    <td class="td_res_grupal2" style="width:120px"><?php echo $fila["ape_paterno"] ?></td>
                    <td class="td_res_grupal2" style="width:120px"><?php echo $fila["ape_materno"] ?></td>
                    <td class="td_res_grupal2" style="width:80px"><?php echo $fila["nombres"] ?></td>
                    <td class="td_res_grupal2" style="width:20px"><?php echo $fila["empresa"] ?></td>
                    <td class="td_res_grupal2" style="width:20px"><?php echo $fila["carnet"] ?></td>
                    <td class="td_res_grupal2" style="width:20px"><?php echo $fila["carnet_critico"] ?></td>
                    <td class="td_res_grupal2" style="width:20px"><?php echo $fila["cedula"] ?></td>
                    <td class="td_res_grupal2" style="width:20px"><?php
                        if ($fila["officetrack"] == 0)
                            echo "NO";
                        else
                            echo "SI";
                        ?>
                    </td>
                    <td class="td_res_grupal2" style="width:150px">
                        <a href="#" onclick="editar_tecnico(<?= $fila["id"] ?>)">
                            <img src="../../../img/pencil_16.png" alt="Editar Tecnico" title="Editar Tecnico">
                        </a>

                        <?php
                        if ($fila["activo"] == "0") {
                            ?>
                            <a href="#" onclick="habilitar_usuario(<?= $fila["id"] ?>)">
                                <img src="../../../img/estado_deshabilitado.png" alt="Deshabilitado"
                                     title="Tecnico Deshabilitado">
                            </a>
                        <?php
                        } else {
                            ?>
                            <a href="#" onclick="deshabilitar_usuario(<?= $fila["id"] ?>)">
                                <img src="../../../img/estado_habilitado.png" alt="Habilitado"
                                     title="Tecnico Habilitado">
                            </a>
                        <?php
                        }

                        ?>
                    </td>
                </tr>
                <?php

                $i++;
            }

            ?>
        </table>
        <div id="paginacion">
            <?= $html_paginacion; ?>
        </div>

    </div>

    <div id="parentModal" style="display: none;">
        <!--	<div id="childModal" style="padding: 10px; background: #fff;"></div>-->

        <div id="childModal" style="background: #fff;"></div>
        <div id="childModal_nuevo" style="background: #fff;"></div>

        <script>

            $().ready(function(){
                var url_ajax = "modificar_tecnicos_ajax.php";
                var url = "modificar_tecnicos.php";

                //FIltro principal
                $("#filtro_principal").change(function () {
                    var id = $(this).val();


                    $.ajax({
                        type: "POST",
                        url: url_ajax,
                        data: {
                            action: "filtro_principal",
                            filtro: $(this).val()
                        },
                        success: function (response) {
                            window.location.href = url
                        },
                        error: function () {
                            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                        }
                    });

                });


                //FILTRO INPUT
                $(".btnFiltrar").click(function () {
                    $.ajax({
                        type: "POST",
                        url: url_ajax,
                        data: {
                            action:"filtro_busqueda" ,
                            tipo : $("#filtro_principal").val(),
                            busqueda: $(this).parent().parent().find("input").val()
                        },
                        success: function (response) {
                            window.location.href = url
                        },
                        error: function () {
                            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                        }
                    });

                });

                //REINICIAR FILTROS
                $("#btnReiniciar").click(function () {
                    $.ajax({
                        type: "POST",
                        url: url_ajax,
                        data: {
                            action: "reiniciar_filtros"

                        },
                        success: function (response) {
                            window.location.href = url
                        },
                        error: function () {
                            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                        }
                    });

                });

                //FILTRO EMPRESA
                $("#fil_empresa").change(function () {

                    $.ajax({
                        type: "POST",
                        url: url_ajax,
                        data: {
                            action: "filtro_empresa",
                            idempresa: $(this).val()
                        },
                        success: function (response) {
                            window.location.href = url
                        },
                        error: function () {
                            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                        }
                    });

                });


                $("#fil_celula").change(function () {

                    $.ajax({
                        type: "POST",
                        url: url_ajax,
                        data: {
                            action: "filtro_celula",
                            idempresa: $("#fil_empresa").val(),
                            idcelula: $("#fil_celula").val()
                        },
                        success: function (response) {
                            window.location.href = url
                        },
                        error: function () {
                            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                        }
                    });

                });


                <?php
            $js  ="";
            if(!empty($_SESSION["filtro_tec"])){
                $js .= "$('#filtro_principal').val('".$_SESSION["filtro_tec"]["tipo"]."');";
                $js .= "$('#".$_SESSION["filtro_tec"]["tipo"]."').show();";


                if(!empty($_SESSION["filtro_tec"]["busqueda"])){
                    $js .= "$('#".$_SESSION["filtro_tec"]["tipo"]." input[type=text]').val('".$_SESSION["filtro_tec"]["busqueda"]."');";
                }

                if(!empty($_SESSION["filtro_tec"]["idcelula"])){
                    $js .= "$('#fil_celula').val('".$_SESSION["filtro_tec"]["idcelula"]."');";
                }



                print $js;
            }

            ?>

                //EVENT ENTER
                $("#nombre_busqueda").keypress(function(e){
                    if(e.which === 13){ $("#btnFiltrar_nombre").trigger("click") }
                });

                $("#carnet").keypress(function(e){
                                    if(e.which === 13){ $("#btnFiltrar_carnet").trigger("click") }
                });
                $("#carnet_critico").keypress(function(e){
                                    if(e.which === 13){ $("#btnFiltrar_critico").trigger("click") }
                });


            });


        </script>

</body>
</html>
