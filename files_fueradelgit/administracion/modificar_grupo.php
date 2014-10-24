<?php
require_once("../../cabecera.php");
require_once("../../clases/class.Grupo.php");

$grupo = new Grupo();

//GRUPOS
$array_grupos = $grupo->ListadoGrupos();



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>PSI - Web SMS - Mensajes Grupales</title>
    <meta http-equiv="content-type" content="text/html">
    <meta charset="utf8">
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
    <script type="text/javascript" src="js/modificar_grupo.js"></script>

</head>

<body>
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
<div id="page-wrap">
    <?php echo pintar_cabecera(); ?>

    <div id="div_res_grupal" class="div_res_grupal">
        <div class="cabecera">
            <div class="opciones">
                <div>
                    <a href="#" id="popupADD">[Crear Grupo]</a>
                </div>
            </div>
        </div>

        <table class="tabla_res_grupal" style="width: 800px;" >
            <thead>
            <tr>
                <th class="th_res_grupal">Grupo</td>
                <th class="th_res_grupal">Status</td>

            </tr>
            </thead>
            <?php
            $i = 1;

            foreach ($array_grupos as $fila) {        ?>
                <tr>

                    <td class="td_res_grupal" style="width:120px" ><?php echo $fila[1] ?></td>

                    <td class="td_res_grupal" style="width:150px">
                        <span class="popupEDIT" idExistente="<?=$fila[0]; ?>">
                            <a href="#">
                                <img src="../../img/pencil_16.png" alt="Editar Usuario" title="Editar Usuario">
                            </a>
                        </span>
                        <?php  if ($fila[2]=="0") {
                            $img = "estado_deshabilitado"; $alt = "Deshabilitado";$valor_enviar= 1;
                        }else {
                            $img = "estado_habilitado";$alt = "Habilitado";$valor_enviar= 0;
                        }  ?>
                        <a href="#" onclick="cambiarEstado(<?=$fila[0]?>, <?=$valor_enviar;?>)" >
                            <img src="../../img/<?=$img;?>.png" alt="<?=$alt;?>" title="Persona <?=alt;?>">
                        </a>
                    </td>
                </tr>
                <?php $i++; } ?>
        </table>
        <div id="paginacion">  <?= $html_paginacion; ?> </div>
    </div>


    <div id="parentModal" style="display: none;">
        <div id="childModal" style="padding: 10px; background: #fff;"></div>
    </div>

    <?php include "tpl/modificar_grupo.tpl.php"; ?>
    <script>
        window.filtrosactivos = {};
        filtrosactivos = <?php print  json_encode($_SESSION["MantGruFilter"]); ?>
    </script>
</body>
</html>
