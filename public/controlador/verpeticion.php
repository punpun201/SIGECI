<?php

// Conexion
$rutaConexion = $_SERVER['DOCUMENT_ROOT'] . "/Proyecto/public/controlador/conexion.php";
if (file_exists($rutaConexion)) 
    include($rutaConexion);

// Verifica si el comentario se guardó con éxito
$comentarioExito = isset($_GET['comentario']) && $_GET['comentario'] === 'exito';

// Variables para almacenar los filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$estados = isset($_GET['estado']) ? $_GET['estado'] : [];

// Construir la consulta a la base de datos
$query = "SELECT idpeticion, nombre, descripcion, ubicacion, colonia, estado_peticion, hora_peticion, estado, municipio, telefono FROM peticion WHERE 1=1";

$params = [];
$types = '';

// Añadir filtros de búsqueda, por nombre, descripcion, telefono, ubicacion, colonia o municipio
if (!empty($search)) {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ? OR telefono LIKE ? OR ubicacion LIKE ? OR colonia LIKE ? OR municipio LIKE ?)";
    $types .= 'ssssss';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Añadir filtros de estado seleccionados (Pendiente, En revisión y Revisado)
if (!empty($estados)) {
    $placeholders = implode(',', array_fill(0, count($estados), '?'));
    $query .= " AND estado_peticion IN ($placeholders)";
    $types .= str_repeat('s', count($estados));
    foreach ($estados as $estado) {
        $params[] = $estado;
    }
}

$stmt = $conexion->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Asignar tipos y parámetros si existen
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
$peticiones = $resultado->fetch_all(MYSQLI_ASSOC);

// Cerrar la conexión
$stmt->close();
$conexion->close();
