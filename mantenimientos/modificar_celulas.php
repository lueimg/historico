<?php
require_once("../../../cabecera.php");
require_once("../clases/class.CelulasCriticos.php");
require_once('../clases/empresa.php');
require_once('../clases/quiebre.php');
include_once "../../../clases/class.Conexion.php";

$db = new Conexion();
$cnx = $db->conectarPDO();

$celula = new CelulasCriticos();

//Agregando paginacion
$celula->setTamano(10);
$celula->setPagina($_GET["pagina"]);
$celula->setInicio(0);

$filtros = array();
if (!empty($_SESSION["filtro"]["idempresa"])) {
    $filtros["idempresa"] = $_SESSION["filtro"]["idempresa"];
}
if (!empty($_SESSION["filtro"]["nombre"])) {
    $filtros["nombre"] = $_SESSION["filtro"]["nombre"];
}

$arr = $celula->ListarCelulasTodos($filtros);
$html_paginacion = $celula->paginacion($filtros);
//print_r($arr);

header('Content-Type: text/html; charset=utf-8');


$empresa = new Empresa();
$empresa->setCnx($cnx);
$empresa_options = $empresa->getEmpresaAllSelectOptions($idempresa);

$quiebres = new Quiebre();
$quiebres_options = $quiebres->comboQuiebres();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>WEBPSI - Mantenimiento de Celulas</title>
<meta name="author" content="Sergio MC"/>
<meta charset="UTF-8">

<?php include("../../../includes.php") ?>

<link type="text/css" href='../css/jquery.multiselect.css' rel="Stylesheet" />
<script type="text/javascript" src="../js/jquery.filter_input.js"></script>
<script type="text/javascript" src="../js/prettify.js"></script>
<script type="text/javascript" src="../js/jquery.multiselect.min.js"></script>

<script type="text/javascript" src="../js/js.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/estiloAdmin.css">
<link rel="stylesheet" type="text/css" href="../../../css/buttons.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/json2/20121008/json2.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.0/backbone-min.js"></script>
<!-- Latest compiled and minified CSS -->
<!--        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">-->
<script type="text/javascript">

    function deshabilitar_celula(idtecnico) {
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

    function habilitar_celula(idtecnico) {
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

    function EditarEstado(e) {
        var element = $(e)
            .parent()
            .parent()
            .find(".td-data").attr("data");
        var celula = JSON.parse(element);
//    console.log(celula);
        window.celula = celula;
        var nuevo_estado = 0;
        if (celula.estado == 0) {
            nuevo_estado = 1;
        }

        $.ajax({
            type: "POST",
            url: "modificar_celulas_ajax.php",
            data: {
                action: "EditarCelula",
                idcelula: celula.idcedula,
                idempresa: celula.idempresa,
                estado: nuevo_estado,
                nombre: celula.nombre
            },
            success: function (response) {
                location.reload();
            }
        });


    }

    function editar_celula(e) {

        $("#childModal").html("");
        $("#childModal").html(templates.formCedulaTemplate());


        //CARGAR MULTISELEC
        $("#quiebres").multiselect({});

        //CARGAR DATOS
        var element = $(e)
            .parent()
            .parent()
            .find(".td-data").attr("data");

        var celula = JSON.parse(element);
        $("#slct_empresa").val(celula.idempresa)
        $("#celula_nombre").val(celula.nombre)
        $("#slct_estado").val(celula.estado)
        $("#idcelula").val(celula.idcedula)

        if( celula.quiebres != null ){
            var quiebres  = celula.quiebres.split(",");
            quiebres.forEach(function(i){
                $("#quiebres option[value="+i+"]").attr("selected",true);
            });
            $("#quiebres").multiselect("refresh");
        }

        $("#childModal #guardarCelula span").text("Actualizar Celula");

        $("#childModal #guardarCelula").click(function () {

            var validacion = validarFormulario("#childModal_nuevo");
            if(validacion){
                $.ajax({
                    type: "POST",
                    url: "modificar_celulas_ajax.php",
                    data: {
                        action: "EditarCelula",
                        idcelula: $("#idcelula").val(),
                        idempresa: $("#slct_empresa").val(),
                        estado: $("#slct_estado").val(),
                        nombre: $("#celula_nombre").val(),
                        quiebres:$("#quiebres").val().join()
                    },
                    success: function (response) {
                        $("#childModal_nuevo").dialog("close");
                        alert(response);
                        location.reload();
                    }
                });
            }
        });

        $("#childModal").dialog({
            modal: true,
            width: '45%',
            hide: 'slide',
            title: 'Editar Celula',
            position: 'top'
        });

    }

    function nueva_celula(idtecnico) {

        $("#childModal_nuevo").html("");
        $("#childModal_nuevo").html(templates.formCedulaTemplate());

       //CARGAR MULTISELEC
        $("#quiebres").multiselect({});

        //AGREGAR ACCION
        $("#childModal_nuevo #guardarCelula").click(function () {

            var validacion = validarFormulario("#childModal_nuevo");
            if(validacion){
                //obtenemos datos
                var idempresa = $("#slct_empresa").val()
                var nombre = $("#celula_nombre").val()
                var estado = $("#slct_estado").val()

                var quiebres = $("#quiebres").val().join();

                //Enviamos datos aguardar
                $.post("modificar_celulas_ajax.php", {
                    idempresa: idempresa,
                    nombre: nombre,
                    estado: estado,
                    quiebres:quiebres,
                    action: "CrearCelula"
                }, function (data) {
                    $("#childModal_nuevo").dialog("close");
                    alert(data);
                    location.reload();
                });
            }


        });

        $("#childModal_nuevo").dialog({
            modal: true,
            width: '45%',
            hide: 'slide',
            title: 'Nueva Celula',
            position: 'top'
        });

    }

validarFormulario  = function(idparent){
    var error = 0;

    $(idparent + " input").each(function(i,el){


        //si no es un campo de empresa critica, si no es un checkbox
        if( $(el).attr("id") != "idcelula"){

            var value  = $(el).val();
            if(value == ""){
                $(el).css("border-color","red");
                alert("Debe llenar todos los datos");
                console.log($(el))
                error++;
                return false;
            }else{
                $(el).css("border-color","");
            }
        }


    });
    if(error > 0 ){
        return false;
    }


    if($("#quiebres").val() == null ){
        alert("Debe seleccionar al menos un quiebre");
        return false
    }

    return true;
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

    #celula_filtros {
        margin: 20px;
    }

    #celula_filtros .control-label {
        background: #1b5790;
        color: #fff;
        padding: 5px;
        width: 75px;
        display: inline-block;
        margin: 5px 0;
        border-radius: 3px;
    }

    #celula_filtros select {
        width: 200px;
    }

    .nueva-celula a {
        margin-top: 15px;
        display: inline-block;
        font-weight: bold;
    }

    #paginacion {
        text-align: center;
        font-weight: bold;
    }

    #paginacion a {
        text-align: center;
        font-weight: normal;
    }

</style>

</head>

<body>

<input type="hidden" value="<?php echo $IDUSUARIO ?>" name="txt_idusuario" id="txt_idusuario"/>

<div id="page-wrap">
<?php echo pintar_cabecera(); ?>

<br/>

<div id="div_res_grupal" class="div_res_grupal" style="border: 1px solid #304B73; padding-top: 0px; float:left; overflow-y: auto;
			height: 500px; width: 780px;">
    <span style="padding: 20px" class="nueva-celula"><a href="#" onclick="nueva_celula();">[ Agregar Celula ]</a> </span>

    <div id="celula_filtros" class="form-group">
        <label class="control-label">Filtros:</label>
            <span>
                <select name="filtro_principal" id="filtro_principal" class="form-control">
                    <option value="">Seleccione Fitro</option>
                    <option value="filtro_empresa">Filtrar por Empresa</option>
                    <option value="filtro_nombre">Filtrar Por nombre</option>
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

            </div>
            <div id="filtro_nombre" class="filtro-item" style="display: none">
                <label class="control-label">Por Nombre:</label>
                    <span>
                        <input type="text" id="text_busqueda" class="form-control" placeholder=" Buscar ... "/>
                    </span>
                <span><input type="submit" id="btnFiltrar" value=":: Filtrar"
                             class="form-control btn btn-primary"/></span>
                <!--                    <span><input type="submit" id="btnLimpiar" value=":: Limpiar"  class="form-control btn btn-primary" /></span>-->
            </div>
        </div>

    </div>

    <table class="tabla_res_grupal" style="width: 100%;">
        <thead>
        <tr>
            <th class="th_res_grupal2">#
            </td>
            <th class="th_res_grupal2">Nombre Celula
            </td>
            <th class="th_res_grupal2">Responsable
            </td>
            <th class="th_res_grupal2">Empresa
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
                <td style="display: none;" class="td-data" data='<?= json_encode($fila); ?>'></td>
                <td class="td_res_grupal2" style="width:10px"><?php echo $cbox ?></td>
                <td class="td_res_grupal2" style="width:120px"><?php echo $fila["nombre"] ?></td>
                <td class="td_res_grupal2"
                    style="width:120px"><?= ($fila["responsable"]) ? $fila["responsable"] : "Sin responsable" ?></td>
                <td class="td_res_grupal2" style="width:80px"><?php echo $fila["empresa"] ?></td>
                <td class="td_res_grupal2" style="width:150px">
                    <a href="#" onclick="editar_celula(this)">
                        <img src="../../../img/pencil_16.png" alt="Editar Celula" title="Editar Celula">
                    </a>

                    <?php
                    $imagen = "estado_deshabilitado.png";
                    $title = "Celula Deshabilitado";
                    $alt = "Deshabilitado";
                    if ($fila["estado"]) {
                        $imagen = "estado_habilitado.png";
                        $title = "Celula Habilitado";
                        $alt = "Habilitado";

                    }
                    ?>
                    <a href="#" onclick="EditarEstado(this)">
                        <img src="../../../img/<?= $imagen; ?>" alt="<?= $alt; ?>" title="<?= $title; ?>">
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
    <!--	<div id="childModal" style="padding: 10px; background: #fff;"></div>-->

    <div id="childModal" style="background: #fff;"></div>
    <div id="childModal_nuevo" style="background: #fff;"></div>
    <script>

        $().ready(function () {

            //Uso de templates de underscore
            _.templateSettings = {interpolate: /\{\{(.+?)\}\}/g, evaluate: /\{!(.+?)!\}/g};
            window.templates = {};
            templates.formCedulaTemplate = _.template($("#formCedulaTemplate").html());


            //FIltro principal
            $("#filtro_principal").change(function () {
                var id = $(this).val();
//               $(".filtro-item").hide();
//               $("#"+id).show("slow");

                $.ajax({
                    type: "POST",
                    url: "modificar_celulas_ajax.php",
                    data: {
                        action: "filtro_principal",
                        filtro: $(this).val()
                    },
                    success: function (response) {
                        window.location.href = "modificar_celulas.php"
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
                    url: "modificar_celulas_ajax.php",
                    data: {
                        action: "filtro_empresa",
                        idempresa: $(this).val()
                    },
                    success: function (response) {
                        window.location.href = "modificar_celulas.php"
                    },
                    error: function () {
                        alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                    }
                });

            });


            //FILTRO INPUT
            $("#btnFiltrar").click(function () {
                $.ajax({
                    type: "POST",
                    url: "modificar_celulas_ajax.php",
                    data: {
                        action: "filtro_nombre",
                        nombre: $(this).parent().parent().find("input").val()
                    },
                    success: function (response) {
                        window.location.href = "modificar_celulas.php"
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
                    url: "modificar_celulas_ajax.php",
                    data: {
                        action: "reiniciar_filtros"

                    },
                    success: function (response) {
                        window.location.href = "modificar_celulas.php"
                    },
                    error: function () {
                        alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                    }
                });

            });

            //LIMPIAR
            $("#btnLimpiar").click(function () {
                $("#text_busqueda").val("").focus()

            });


            <?php
            $js  ="";
            if(!empty($_SESSION["filtro"])){
                $js .= "$('#filtro_principal').val('".$_SESSION["filtro"]["tipo"]."');";
                $js .= "$('#".$_SESSION["filtro"]["tipo"]."').show();";


                if(!empty($_SESSION["filtro"]["idempresa"])){
                    $js .= "$('#fil_empresa').val('".$_SESSION["filtro"]["idempresa"]."');";
                }

                if(!empty($_SESSION["filtro"]["nombre"])){
                    $js .= "$('#text_busqueda').val('".$_SESSION["filtro"]["nombre"]."');";
                }

                print $js;
            }

            ?>




        });
    </script>


    <script id="formCedulaTemplate" type="text/template">
        <div id="div_Clonar" class="divClonar">
            <table class="tablaClonar">
                <tr>
                    <td class="celda_titulo">Nombre:</td>
                    <td class="celda_res" colspan="2">
                        <input type="text" name="celula_nombre" id="celula_nombre" class="celula_nombre" value=""/>
                    </td>
                </tr>
                <tr>
                    <input type="hidden" name="idcelula" id="idcelula" class="idcelula" value=""/>
                    <td class="celda_titulo">Empresa:</td>
                    <td class="celda_res" colspan="2">
                        <select class="slct_empresa" id="slct_empresa" name="slct_empresa">
                            <?= $empresa_options ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="celda_titulo">Quiebres:</td>
                    <td class="celda_res" colspan="2">
                        <select class="quiebres" id="quiebres" name="quiebres" multiple>
                            <?= $quiebres_options ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="celda_titulo">Responsable:</td>
                    <td class="celda_res" colspan="2">
                        <div id="divCedulas"> Sin responsable</div>
                    </td>
                </tr>
                <tr>
                    <td class="celda_titulo">Estado:</td>
                    <td class="celda_res" colspan="2">
                        <select class="slct_estado" id="slct_estado" name="slct_estado">
                            <option value='1'>Activo</option>
                            <option value='0'>Inactivo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="celda_res" colspan="3" align="center">

                        <button id="guardarCelula" class="action blue" title="Generar Password">
                            <span class="label">Guardar Celula</span>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </script>

</body>
</html>
