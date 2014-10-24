<?php
require_once("../../cabecera.php");
require_once("../../clases/class.Usuario.php");

$usuario = new Usuario();

$idusuario_modelo = $_REQUEST["idusuario"];

//echo $idusuario;

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>PSI - Web SMS - Mensajes Grupales</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <meta name="author" content="Sergio MC" />
    <?php include ("../../includes.php") ?>
    <script type="text/javascript">

        function randompass(length){
            var letras = new Array();
            letras[1]="a";
            letras[2]="b";
            letras[3]="c";
            letras[4]="d";
            letras[5]="e";
            letras[6]="f";
            letras[7]="g";
            letras[8]="h";
            letras[9]="i";
            letras[10]="j";
            letras[11]="k";
            letras[12]="l";
            letras[13]="m";
            letras[14]="n";
            letras[15]="o";
            letras[16]="p";
            letras[17]="q";
            letras[18]="r";
            letras[19]="s";
            letras[20]="t";
            letras[21]="u";
            letras[22]="w";
            var pass = "";
            var numero = 0;
            for(var i=1;i<length;i++){
                numero = Math.floor(Math.random()*(22-1))+1;
                if(i==1){
                    pass+=numero;
                }else{
                    pass+=letras[numero];
                }
            }
            numero = Math.floor(Math.random()*(22-1))+1;
            pass+=numero;
            return pass;
        }

        function validar_requerido(field,alerttxt)
        {
            var value = $(field).val();
            if (value==null||value==""||value==0)
            {alert(alerttxt);return false}
            else {return true}
        }



        $(document).ready(function(){

            $("#txtNombre").focus();

            $('#btnGenerarPass').click(function(){
                var length = 6;
                var pass = "";
                pass = randompass(length);
                $('#txtPassword').attr('value',pass);
            });

            $('#btnsalir').click(function(){
                $("#childModal").dialog('close');
            });

            $('#btnClonarUsuario').click(function(){
                var nombre = $("#txtNombre");
                var apellidos = $("#txtApellidos");
                var login = $("#txtLogin");
                var password = $("#txtPassword");
                var dni = $("#txtDni");


                if (validar_requerido(nombre,"Falta llenar el campo Nombre.")==false)
                { nombre.focus();return false}
                if (validar_requerido(apellidos,"Falta llenar el campo Apellidos.")==false)
                {apellidos.focus();return false}
                if (validar_requerido(login,"Falta llenar el campo Login.")==false)
                {login.focus();return false}
                if (validar_requerido(password,"Falta llenar el campo Password.")==false)
                {password.focus();return false}
                if (validar_requerido(dni,"Falta llenar el campo DNI.")==false)
                {dni.focus();return false}

                // todo conforme, se procede a clonar usuario
                var pagina="receptor_clonar_usuarios.php";
                var idusuario_modelo = $("#txt_idusuario_modelo").val();
                $.ajax({
                    type: "POST",
                    url: pagina,
                    data: {
                        action: 'clonar_usuario',
                        idusuario_modelo: idusuario_modelo,
                        nombre: nombre.val(),
                        apellidos: apellidos.val(),
                        login: login.val(),
                        password: password.val(),
                        dni: dni.val()
                    },
                    success: function(html){
                        alert(html);
                        window.location.reload();
                    }
                });





            });


        });



    </script>

    <link rel="stylesheet" type="text/css" href="../../estilos.css">
    <link rel="stylesheet" type="text/css" href="estiloAdmin.css">
    <link rel="stylesheet" type="text/css" href="buttons.css">

    <link type="text/css" href='../historico/css/jquery.multiselect.css' rel="Stylesheet" />
    <script type="text/javascript" src="../historico/js/jquery.filter_input.js"></script>
    <script type="text/javascript" src="../historico/js/prettify.js"></script>
    <script type="text/javascript" src="../historico/js/jquery.multiselect.min.js"></script>

    <script src="js/json2.js"></script>
    <script src="js/underscore-min.js"></script>
    <script src="js/backbone-min.js"></script>


</head>

<body>
<input type="hidden" value="<?php echo $IDUSUARIO?>" name="txt_idusuario" id="txt_idusuario"/>
<input type="hidden" value="<?php echo $idusuario_modelo?>" name="txt_idusuario_modelo" id="txt_idusuario_modelo"/>

<div id="div_Clonar" class="divClonar">
    <table class="tablaClonar"  >
        <tr>
            <td class="celda_titulo">Nombre:</td>
            <td class="celda_res"  colspan="2"><input type="text" name="txtNombre" id="txtNombre" class="caja_texto3"/></td>
        </tr>
        <tr>
            <td class="celda_titulo">Apellidos:</td>
            <td class="celda_res"  colspan="2"><input type="text" name="txtApellidos" id="txtApellidos" class="caja_texto3"/></td>
        </tr>
        <tr>
            <td class="celda_titulo">Login Usuario:</td>
            <td class="celda_res"  colspan="2"><input type="text" name="txtLogin" id="txtLogin" class="caja_texto3"/></td>
        </tr>
        <tr>
            <td class="celda_titulo">Password:</td>
            <td class="celda_res"><input type="text" name="txtPassword" id="txtPassword" class="caja_texto3" readonly="readonly"/>
            </td>
            <td class="celda_res">
                <button id="btnGenerarPass" class="action blue" title="Generar Password" style="width: 100px" >
                    <span class="label">Generar Password</span>
                </button>
            </td>

            </td>
        </tr>
        <tr>
            <td class="celda_titulo">DNI:</td>
            <td class="celda_res"  colspan="2"><input type="text" name="txtDni" id="txtDni" class="caja_texto3"/></td>
        </tr>
        <tr>
            <td class="celda_res" colspan="3" align="center">

                <button id="btnClonarUsuario" class="action blue" title="Generar Password" >
                    <span class="label">Guardar Usuario</span>
                </button>
                <button id="btnsalir" class="action red" title="Cancelar">
                    <span class="label">Salir</span>
                </button>

            </td>
        </tr>

    </table>

</div>

</body>
</html>
