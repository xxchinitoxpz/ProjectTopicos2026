<?php
// Obtener la ruta base del proyecto desde la URL
$ruta_base = dirname($_SERVER['SCRIPT_NAME']);
$ruta = rtrim($ruta_base, '/') . '/public/index.php';

header('Location: ' . $ruta);
exit();
?>