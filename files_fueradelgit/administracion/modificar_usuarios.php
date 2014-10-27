<?php
require_once("../../cabecera.php");
require_once("../../clases/class.Usuario.php");
require_once("../historico/clases/empresa.php");
require_once("../historico/clases/quiebre.php");

$usuario = new Usuario();

$usuario->setPagina($_GET["pagina"]);
$usuario->setFiltros($_SESSION["MantUsuFilter"]);
$arr = $usuario->ListadoUsuarios();
$html_paginacion = $usuario->paginacion();

$array_areas = $usuario->getAreas();
$areas_options_html = $usuario->getOtionsHTML($array_areas);

$array_perfiles = $usuario->getPerfiles();
$perfiles_options_html = $usuario->getOtionsHTML($array_perfiles);

$modulos_options_html = $usuario->getModulosOptionsHTML();

$empresas_options_html= $usuario->getEECCOptionsHTML();
$quiebres = new Quiebre();
$quiebres_options_html = $quiebres->comboQuiebres();



//empresas criticas
$empresas_criticas_trs = $usuario->empresas_criticas_trs();
$proyectos_trs = $usuario->proyectos_trs();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>PSI - Web SMS - Mensajes Grupales</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <meta name="author" content="Sergio MC" />
    <?php include ("../../includes.php") ?>
    <script type="text/javascript" src="../js/js.js"></script>


    <link type="text/css" href='../historico/css/jquery.multiselect.css' rel="Stylesheet" />
    <script type="text/javascript" src="../historico/js/jquery.filter_input.js"></script>
    <script type="text/javascript" src="../historico/js/prettify.js"></script>
    <script type="text/javascript" src="../historico/js/jquery.multiselect.min.js"></script>

    <script src="js/json2.js"></script>
    <script src="js/underscore-min.js"></script>
    <script src="js/backbone-min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/adminUsuario.css">
    <script type="text/javascript" src="js/adminUsuario.js"></script>

</head>

<body>
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
<div id="page-wrap">
    <?php echo pintar_cabecera(); ?>

    <div id="div_res_grupal" class="div_res_grupal">
       <div class="cabecera">
        <div class="opciones">
            <div>
                <a href="#" id="crearUsuario">[Crear usuario]</a>
            </div>
        </div>
        <fieldset>
            <legend>Filtros de Usuario:</legend>
            <table>
                <tr>
                    <td colspan="2"><div>seleccione filtro</div></td>
                </tr>
                <tr>
                    <td>

                        <select name="filtros" id="filtros">
                            <option value="">Seleccione un Filtro</option>
                            <option value="ape">Apellidos y nombre</option>
                            <option value="dni">Por DNI</option>
                            <option value="perfil">Por Perfil</option>
                            <option value="area">Por area</option>
                            <option value="empresa_principal">Por Empresa</option>
                        </select>
                    </td>
                    <td class="filter-seleccionado"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <button class="actionFiltrar"> Filtrar </button>
                        <button class="actionReiniciar">Reiniciar</button>
                    </td>
                </tr>
            </table>
        </fieldset>
       </div>

        <table class="tabla_res_grupal" style="width: 800px;" >
            <thead>
            <tr>
                <th class="th_res_grupal">Item #</td>
                <th class="th_res_grupal">Apellidos</td>
                <th class="th_res_grupal">Nombres</td>
                <th class="th_res_grupal">Login</td>
                <th class="th_res_grupal">DNI</td>
                <th class="th_res_grupal">Perfil</td>
                <th class="th_res_grupal">Area</td>
                <th class="th_res_grupal">Status</td>

            </tr>
            </thead>
            <?php
            $i = 1;

            foreach ($arr as $fila) {
                $cbox = "<input type='checkbox' name='pg_checkboxs' value='".$fila["id"]."' />";
                ?>
                <tr>
                    <td class="td_res_grupal" style="width:10px"><?php echo $cbox?></td>
                    <td class="td_res_grupal" style="width:120px" ><?php echo $fila["apellido"] ?></td>
                    <td class="td_res_grupal" style="width:120px"><?php echo $fila["nombre"]?></td>
                    <td class="td_res_grupal" style="width:80px"><?php echo $fila["usuario"]?></td>
                    <td class="td_res_grupal" style="width:20px"><?php echo $fila["dni"]?></td>
                    <td class="td_res_grupal" style="width:20px"><?php echo $fila["perfil"]?></td>
                    <td class="td_res_grupal" style="width:20px"><?php echo $fila["area"]?></td>
                    <td class="td_res_grupal" style="width:150px">
<!--                        <img src="../../img/search_16.png" alt="Ver Detalle" title="Ver Detalle">-->
                        <span class="editUser" idusuario="<?=$fila["id"]; ?>">
                            <a href="#">
                                <img src="../../img/pencil_16.png" alt="Editar Usuario" title="Editar Usuario">
                            </a>
                        </span>

                        <?php
                        if ($fila["status"]=="0") {
                            ?>
                            <a href="#" onclick="habilitar_usuario(<?=$fila["id"]?>)" >
                                <img src="../../img/estado_deshabilitado.png" alt="Deshabilitado" title="Usuario Deshabilitado">
                            </a>
                        <?php
                        }
                        else {
                            ?>
                            <a href="#" onclick="deshabilitar_usuario(<?=$fila["id"]?>)" >
                                <img src="../../img/estado_habilitado.png" alt="Habilitado" title="Usuario Habilitado">
                            </a>
                        <?php
                        }

                        ?>
                        <a href="#" onclick="clonar_usuario(<?=$fila["id"]?>)" >
                            <img src="../../img/btn_multi_user.png" alt="Clonar" title="Clonar Usuario">
                        </a>

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
        <div id="childModal" style="padding: 10px; background: #fff;"></div>
    </div>

    <?php
    include "tpl/adminUsuario.tpl.php";
    ?>
    <script>

    window.filtrosactivos = {}
    filtrosactivos = <?php print  json_encode($_SESSION["MantUsuFilter"]); ?>

    </script>


</body>
</html>
