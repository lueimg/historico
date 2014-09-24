<?php
print_r($_POST);
$datos=$_POST;
print_r($_POST['cadena']);
echo json_encode($datos);
?>