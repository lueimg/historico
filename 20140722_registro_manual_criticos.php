<?php
include_once "../../clases/class.Conexion.php";
require_once("../../cabecera.php");
require_once('clases/averias.php');
require_once('clases/gestionCriticos.php');
require_once('clases/capacidadHorarios.php');
require_once('clases/tecnicos.php');
require_once('clases/motivos.php');
require_once('clases/zonales.php');
require_once './clases/ubigeo.php';


//Definiendo la zona horaria
date_default_timezone_set("America/Lima");

//Abriendo la conexion
$db = new Conexion();
$cnx = $db->conectarPDO();

$Zonal = new Zonales();
$arrZonal = $Zonal->getZonalAll($cnx);

$ob_ubigeo = new Ubigeo();
//Solo LIMA
$distritos = $ob_ubigeo->listarDistritos($cnx, '15', '01');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>PSI - Web SMS - Mensajes Grupales</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
            <meta name="author" content="Sergio MC" />

            <?php include ("../../includes.php") ?>	
            <script type="text/javascript" src="js/jquery.filter_input.js"></script>
            <script type="text/javascript" src="js/criticos.js"></script>

            <link type="text/css" href='css/estilo.css' rel="Stylesheet" />
            <link type="text/css" href='css/horarios.css' rel="Stylesheet" />

            <link type="text/css" href='css/demo_page.css' rel="Stylesheet" />
            <link type="text/css" href='css/demo_table.css' rel="Stylesheet" />
            
            <script>
            var segmentoNoCatv = {
                8:'8',
                9:'9',
                A:'A',
                B:'B',
                C:'C',
                D:'D'
            };
            
            var segmentoCatv = {
                vip:'VIP',
                'NO VIP':'NO VIP'
            };
                
            $(document).ready(function (){
                //MouseOver td
                $("td").mouseover(function (){
                    $(this).css("color", "#000000");
                    $(this).css(
                            "background-color", 
                            $(this).css("background-color"));
                });
                $("td").css("padding", "2px");
                //Errores de formulario
                $(".error_form").css("color", "#FF0000");
                $(".error_form").css("font-size", "11px");
                $(".error_form").hide();
                //Resultado de registro
                $(".registro_manual").hide();
                //Tipos de datos
                $('#telefono').filter_input({regex:'[0-9]'});
                $('#inscripcion').filter_input({regex:'[0-9]'});
                $('#direccion').filter_input({regex:'[0-9-#a-zA-Z\ áéíóúñÑ.,]'});
                //Envio y registro de datos
                $("#frm_criticos").submit(function (event){
                    event.preventDefault();
                    var datos = $(this).serialize();
                    
                    //Validacion de campos
                    var formOk = true;
                    $.each( $(".error_form"), function (){
                        var title = $(this).attr("title");
                        if ( $.trim( $("#" + title).val() ) === "" ) {
                            $(this).show().delay(4000).fadeOut(3000);
                            formOk = false;
                        }
                    });
                    
                    if ( !formOk ) {
                        return false;
                    }
                    
                    //Validacion OK, enviar datos.
                    $.ajax({
                        url: "casos_nuevos.php",
                        type: 'POST',
                        data: "action=registraRutina&" + datos,
                        dataType: "json",
                        success: function(datos) {
                            if ( datos.estado === true ) {
                                $(".registro_manual").hide();
                            
                                window.parent
                                        .jQuery('#dialog-registro-manual')
                                        .dialog('close');
                                window.parent
                                        .$("#filtro_personalizado")
                                        .click();
                            }
                            //Respuesta
                            alert(datos.msg + "\n" + datos.data);
                        }
                    });
                    
                });
                
                //Segmento por tipo de averia
                $("#tipo_averia").change(function (){
                    $("#segmento option").remove();
                    $("#segmento").append("<option value=\"\">"
                            + "-Seleccione-"
                            + "</option>");
                    
                    //Zonal, primera opcion selected
                    $("#zonal option").first().attr("selected", "selected");
                    //Remover options from mdf
                    $("#mdf option").remove();
                    //Valor de eecc vacio
                    $("#eecc").val("");
                    //Valor de lejano vacio
                    $("#lejano").val("");
                    //Valor de microzona vacio
                    $("#microzona").val("");
                    
                    if ( $(this).val()==='rutina-catv-pais' ) {
                        //Texto de la etiqueta
                        $(".inscod").html("Cod. Cliente CMS");
                        for ( index in segmentoCatv ) {
                            if ( segmentoCatv.hasOwnProperty(index) ) {
                                $("#segmento").append("<option value=\"" 
                                        + index 
                                        + "\">" 
                                        + segmentoCatv[index] 
                                        + "</option>");
                            }
                        }
                    } else {
                        //Texto de la etiqueta
                        $(".inscod").html("Inscripcion");
                        for ( index in segmentoNoCatv ) {
                            if ( segmentoNoCatv.hasOwnProperty(index) ) {
                                $("#segmento").append("<option value=\"" 
                                        + index 
                                        + "\">" 
                                        + segmentoNoCatv[index] 
                                        + "</option>");
                            }
                        }
                    }
                });
                
                //Obtener mdf por zonal
                $("#zonal").change(function (){
                    
                    var data = $(this).val();
                    if ( data !== "" ) {
                        $("#eecc").val( "" );
                        $("#lejano").val( "" );
                        $("#microzona").val( "" );
                        $.ajax({
                            url: "casos_nuevos.php",
                            type: 'POST',
                            data: "action=getMdfByZonal&zonal=" 
                                    + data 
                                    + "&tipo=" 
                                    + $("#tipo_averia").val(),
                            dataType: "json",
                            success: function(datos) {
                                $("#mdf option").remove();
                                
                                $("#mdf").append("<option value=\"\">"
                                        + "Seleccione"
                                        + "</option>");
                                $.each(datos, function (){
                                    var eecc = $.trim( this.EECC_CRITICO );
                                    if ( eecc === 'LARI PLAYAS' ) {
                                        eecc = 'LARI';
                                    }
                                    $("#mdf").append("<option value=\"" 
                                            + this.MDF 
                                            + "___"
                                            + eecc
                                            + "___"
                                            + this.LEJANO 
                                            + "___"
                                            + this.ZONA_CRITICO
                                            + "\">" 
                                            + this.MDF 
                                            + "</option>");
                                });
                            }
                        });
                    } else {
                        $("#mdf option").remove();
                        $("#eecc").val( "" );
                        $("#lejano").val( "" );
                        $("#microzona").val( "" );
                    }
                });
                
                //Obtener EECC por mdf o nodo
                $("#mdf").change(function (){
                    
                    var data = $(this).val();
                    if ( data !== "" ) {
                        var arrData = data.split("___");
                        if (arrData[1]!=='null' 
                                && typeof arrData[1]!=='undefined') {
                            $("#eecc").val( arrData[1] );
                        } else {
                            $("#eecc").val( '' );
                        }
                        
                        if (arrData[2]!=='null' 
                                && typeof arrData[2]!=='undefined') {
                            $("#lejano").val( arrData[2] );
                        } else {
                            $("#lejano").val( '' );
                        }
                        
                        if (arrData[3]!=='null' 
                                && typeof arrData[3]!=='undefined') {
                            $("#microzona").val( arrData[3] );
                        } else {
                            $("#microzona").val( '' );
                        }
                        
                    } else {
                        $("#eecc").val( "" );
                        $("#lejano").val( "" );
                        $("#microzona").val( "" );
                    }
                });
            });
            </script>
            
    </head>

    <body>
        <div class="modalPop"></div>

        <h3 class="registro_manual" title="ok" style="color: #0000FF">
            Pedido registrado correctamente
        </h3>
        <h3 class="registro_manual" title="ko" style="color: #FF0000">
            Error al registrar pedido
        </h3>
        
        <div class="registro_clientes">
            <form name="frm_criticos" id="frm_criticos" action="" method="POST">
                
                <table style="width: 100%">
                    <tr>
                        <td style="text-align: left; width: 105px">Tipo averia</td>
                        <td style="text-align: left; width: 105px" colspan="3">
                            <select class="motivo_registro" id="tipo_averia" name="tipo_averia" >
                                <option value="">-Seleccione-</option>
                                <option value="rutina-bas-lima">STB</option>
                                <option value="rutina-adsl-pais">ADSL</option>
                                <option value="rutina-catv-pais">CATV</option>
                            </select>
                            <span class="error_form" title="tipo_averia">Seleccione Tipo de Aver&iacute;a</span>
                            <span class="fin_registro"></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left" class="inscod">Inscripci&oacute;n o Cod. Cliente CMS</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" maxlength="255" value="" id="inscripcion" name="inscripcion" />
                            <span class="error_form" title="inscripcion">Ingrese inscripci&oacute;n</span>
                        </td>
                        <td style="text-align: left">Telefono</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="telefono" id="telefono" />
                            <span class="error_form" title="telefono">Ingrese tel&eacute;fono</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Direccion</td>
                        <td style="text-align: left" colspan="3">
                            <input class="border" type="text" size="50" value="" maxlength="255" name="direccion" id="direccion" />
                            <span class="error_form" title="direccion">Ingrese direcci&oacute;n</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Nombre de contacto</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="255" name="cr_nombre" id="cr_nombre" />
                            <span class="error_form" title="cr_nombre">Ingrese Nombre de contacto</span>
                        </td>
                        <td style="text-align: left">Telefono de contacto</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="cr_telefono" id="cr_telefono" />
                            <span class="error_form" title="cr_telefono">Ingrese Tel&eacute;fono de contacto</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Celular de contacto</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="cr_celular" id="cr_celular" />
                            <span class="error_form" title="cr_celular">Ingrese Celular de contacto</span>
                        </td>
                        <td style="text-align: left">Observaci&oacute;n</td>
                        <td style="text-align: left">
                            <textarea class="border" maxlength="255" value="" id="cr_observacion" name="cr_observacion"></textarea>
                            <span class="error_form" title="cr_observacion">Ingrese observaci&oacute;n</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Segmento</td>
                        <td style="text-align: left">
                            <select class="motivo_registro" id="segmento" name="segmento">          
                            </select>
                            <span class="error_form" title="segmento">Seleccione segmento</span>
                        </td>
                        <td style="text-align: left">Zonal</td>
                        <td style="text-align: left">
                            <select class="motivo_registro" id="zonal" name="zonal">
                                <option value="">-Seleccione-</option>
                                <?php
                                foreach ( $arrZonal as $key=>$val ) {
                                    $id = $val["id"];
                                    $abv = $val["abreviatura"];
                                    $zonal = $val["zonal"];
                                    echo "<option value=\"$abv\">$zonal</option>";
                                }
                                ?>
                            </select>
                            <span class="error_form" title="zonal">Seleccione zonal</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">MDF/NODO</td>
                        <td style="text-align: left">
                            <select class="motivo_registro" id="mdf" name="mdf">
                                
                            </select>
                            <span class="error_form" title="mdf">Seleccione MDF/NODO</span>
                        </td>
                        <td style="text-align: left">Distrito</td>
                        <td style="text-align: left">
                            <select class="motivo_registro" id="distrito" name="distrito">
                                <?php
                                foreach ($distritos as $key=>$val) {
                                    $nombre = $val["nombre"];
                                    echo "<option value=\"$nombre\">$nombre</option>";
                                }
                                ?>
                            </select>
                            <span class="error_form" title="distrito">Seleccione distrito</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Movistar Uno</td>
                        <td style="text-align: left">
                            <select class="motivo_registro" id="movistar_uno" name="movistar_uno">
                                <option value="">NO</option>
                                <option value="MOVISTAR UNO">SI</option>
                            </select>
                        </td>
                        <td style="text-align: left">EECC</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="eecc" id="eecc" readonly="true" />
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Lejano</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="lejano" id="lejano" readonly="true" />
                        </td>
                        <td style="text-align: left">Microzona</td>
                        <td style="text-align: left">
                            <input class="border" type="text" size="12" value="" maxlength="11" name="microzona" id="microzona" readonly="true" />
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <input type="submit" name="guardar" id="guardar" value="Guardar cambios" />
                        </th>
                    </tr>
                </table>
                
            </form>
        </div>
    </body>
</html>