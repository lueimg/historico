<?php
require_once("../../../clases/class.EstructuraLari.php");


function comboCategoriasLari() {
	$objLari = new EstructuraLari();
	$arrCategorias = $objLari->getLariCategorias();
	$x = "<select id='categoria' name='categoria'> ";
	$x .= "<option value=0 selected='true'>Seleccione categoria .. </option>";
	foreach ($arrCategorias as $row) {
		$x .= "<option value=".$row["idpersona_categoria"]." >".$row["categoria"]."</option>";
	}
	$x .= "</select>";
	return $x;
}
function comboEmpresas() {
	$objLari = new EstructuraLari();
	$arrCategorias = $objLari->getEmpresas();
	$x = "<select id='empresa' name='empresa'> ";
	$x .= "<option value=0 selected='true'>Seleccione empresa .. </option>";
	foreach ($arrCategorias as $row) {
		$x .= "<option value=".$row["id"]." >".$row["eecc"]."</option>";
	}
	$x .= "</select>";
	return $x;
}


?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8">


<script type="text/javascript" src="../../../js/jquery-1.7.2.min.js"></script>
<link href="../../../css/form-styles.css" rel="stylesheet" type="text/css">

<script>
function guardar() {
	//if (validar_requerido_jquery($("#txt_observaciones"),"Debe ingresar observaciones.")==false)
    //    {$("#txt_observaciones").focus();return false};	
		
	var url = "newPersona_ajax.php";
	$.ajax({
		type: "POST",
		url: url,
		data: $("#form1").serialize(), // Adjuntar los campos del formulario enviado.
		success: function(data)
		{
			var response = jQuery.trim(data);
			var resp = response.split("|");

			if (resp[0]=="1")  {  // grabo OK
				alert("Se registro correctamente a la persona.");
			}

		}
	});		
}

</script>

</head>

<body>

<form action="" method="post" class="dark-matter" name="form1" id="form1">
    <h1>Formulario de Registro
        <span>Ingrese los datos de la persona.</span>
    </h1>
    <label>
        <span>Nombres :</span>
        <input id="nombres" type="text" name="nombres" placeholder="Su nombre" />
    </label>
    <label>
        <span>Apellido Paterno :</span>
        <input id="apellidoP" type="text" name="apellidoP" placeholder="Su apellido paterno" />
    </label>
    <label>
        <span>Apellido Materno :</span>
        <input id="apellidoM" type="text" name="apellidoM" placeholder="Su apellido materno" />
    </label>
	 <label>
        <span>DNI :</span>
        <input id="dni" type="text" name="dni" placeholder="Su Documento de Identidad" />
    </label>
    <label>
        <span>Empresa :</span>
			<?php echo comboEmpresas() ?>
    </label> 
	<label>
        <span>Categoria :</span>
		<?php echo comboCategoriasLari() ?>
    </label> 
	<label>
        <span>RPM  :</span>
        <input type="text" id="rpm" name="rpm" placeholder="Su numero rpm" />
    </label>	
	<label>
        <span>Celular  :</span>
        <input type="text" id="celular" name="celular" placeholder="Su numero celular" />
    </label>
	<label>
        <span>Email :</span>
        <input id="email" type="email" name="email" placeholder="Su Correo electronico" />
    </label>
	 <label>
        <span>Telefono 1 :</span>
        <input type="text" id="telefono1" name="telefono1" placeholder="Su numero telefonico #1" />
    </label>		
   	<label>
     <span>Telefono 2 :</span>
        <input type="text" id="telefono2" name="telefono2" placeholder="Su numero telefonico #2" />
    </label>	
	<!--
    <label>
        <span>Your Email :</span>
        <input id="email" type="email" name="email" placeholder="Valid Email Address" />
    </label>
   
    <label>
        <span>Message :</span>
        <textarea id="message" name="message" placeholder="Your Message to Us"></textarea>
    </label>
	-->
     <label>
        <span>&nbsp;</span>
        <input type="button" class="button" value="Guardar" onclick='guardar()'/>
    </label>    
</form>

</body>