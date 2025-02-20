<?php

$mostrarModalExito = isset($_SESSION['eliminacion_exitosa']);
unset($_SESSION['eliminacion_exitosa']); // Limpia la variable de sesión

if (!isset($_SESSION['idcliente']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: index.php");
    exit();
}

$idcliente = $_SESSION['idcliente'];
$rutaConexion = $_SERVER['DOCUMENT_ROOT'] . "/Proyecto/public/controlador/conexion.php";

if (file_exists($rutaConexion)) {
    include($rutaConexion);
}

// Obtener las peticiones del cliente
$query = "SELECT idpeticion, nombre, descripcion, hora_peticion, estado, telefono, ubicacion, colonia, municipio, estado_peticion FROM peticion WHERE idcliente = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $idcliente);
$stmt->execute();
$resultado = $stmt->get_result();
$peticiones = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener los comentarios asociados a cada petición
$comentarios = [];
foreach ($peticiones as $peticion) {
    $queryComentarios = "SELECT comentario, fecha_comentario FROM comentarios WHERE idpeticion = ?";
    $stmtComentarios = $conexion->prepare($queryComentarios);
    $stmtComentarios->bind_param("i", $peticion['idpeticion']);
    $stmtComentarios->execute();
    $resultadoComentarios = $stmtComentarios->get_result();
    $comentarios[$peticion['idpeticion']] = $resultadoComentarios->fetch_all(MYSQLI_ASSOC);
    $stmtComentarios->close();
}

$conexion->close();
