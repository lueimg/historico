<?php

require_once("../../cabecera.php");

$mensaje = $_GET["mensaje"];

//var_dump($_SESSION);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <title>Mensajes Libres e Individuales</title>

		

            <?php include ("../../includes.php") ?>         

            <link href="css/sms_libre.css" rel="stylesheet" type="text/css" media="all"/>
            <script type="text/javascript">

                $(document).ready(function() {
                    iniciar();
					$("#mensaje").val("<?php echo $mensaje?>");
	
                });

                function iniciar()
                {
                    $("#countdown").val(140);
                    $("#celular").val("");
                    $("#mensaje").val("");
                }


                function enviar()
                {
                    var celular = $("#celular").val();
                    var mensaje = $("#mensaje").val();
                    var iduser = $("#txt_idusuario").val();
	
                    if (celular.length<9)
                    {
					   
                        alert("Numero celular no valido. Debe tener 9 digitos.");
                        return;
                    }
	
					
                    if (mensaje.length<4)
                    {
                        alert("Mensaje debe tener minimo 4 letras.");
                        return;
                    }
	
                    $.ajax({
                        type: "POST",
                        url: "../../sms_enviar_individual_ajax.php",
                        data: { 
                            enviar_sms: 1,
                            celular: celular,
                            iduser: iduser,
							
                            mensaje: mensaje
                        }
                    }).done(function( msg ) {
                        var res = msg.split("|");
                        r = $.trim(res[1])
                        //alert(r);
                        if (r == "1") {
                            $("#div_res").html("Se envio el mensaje correctamente.");
                            iniciar();
                        }
                        else
                            $("#div_res").html("Ocurrio un error, no se pudo enviar el mensaje.");
                    });
                }

                function limitText() {
                    var limitField = document.getElementById("mensaje");
                    var limitCount = document.getElementById("countdown");
                    var limitNum = 140;
	
                    if (limitField.value.length > limitNum) {
                        limitField.value = limitField.value.substring(0, limitNum);
                    } else {
                        limitCount.value = limitNum - limitField.value.length;
                    }
                }

            </script>


    </head>

    <body >
        <input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
        <div id="page-wrap">
            <div id="main-content">
                <div id="id0" class="div0">
                <table>
                <thead>
                    <tr class="odd">
                        <th scope="col" abbr="Home" colspan="4" class="col_titulo">&nbsp;Envio de SMS Individuales</th>
                    </tr>	
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="column1">Numero Celular:</th>
                        <td class="column2"><input name="celular" id="celular" type="text" maxlength="10" /></td>
                    </tr>
                    <tr>
                        <th scope="row" class="column1">Mensaje SMS:</th>
                        <td class="column2"><textarea id="mensaje" name="mensaje" class="caja_texto2"  onKeyDown="limitText();" 
                                        onKeyUp="limitText();"></textarea></td>

                    </tr>
                    <tr>
                        <td colspan="2" class="td_center">
                            <font size="1">(Max caracteres: 140)<br>
                                    Tienes <input readonly type="text" name="countdown" id="countdown" size="3" value="140" style="width:40px; font-size: 10px;"> caracteres por escribir.</font>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_center"><input name="enviar" 
                                type="button" 
                                value="Enviar SMS" onclick="enviar()"/></td>
                    </tr>
                    </tbody>
                    </table>
                </div>

                <div id="div_res" class="div0"></div>                
        <?php

        ?>
        </div>

        </div>

    </body>
</html>