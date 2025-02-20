<?php
include("controlador/conexion.php");

// Verificación de sesión y permisos
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['tipo_usuario'], ['Administrador', 'Usuario'])) {
    header("Location: index.php");
    exit();
}

$usuarioLogueado = isset($_SESSION['usuario']);
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : '';

// Obtener ID de la petición
if (isset($_GET['idpeticion'])) {
    $idpeticion = $_GET['idpeticion'];

    // Obtener datos de la petición
    $query = "SELECT nombre, descripcion, hora_peticion, estado, telefono, ubicacion, colonia, estado_peticion FROM peticion WHERE idpeticion = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $idpeticion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $peticion = $resultado->fetch_assoc();

    if (!$peticion) {
        echo "No se encontró la petición o no tienes permisos para editarla.";
        exit();
    }
} else {
    echo "ID de petición no especificado.";
    exit();
}

// Procesar el formulario para actualizar la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $telefono = $_POST['telefono'];
    $ubicacion = $_POST['ubicacion'];
    $colonia = $_POST['colonia'];
    $estado_peticion = $_POST['estado_peticion'];

    $query = "UPDATE peticion SET nombre = ?, descripcion = ?, telefono = ?, ubicacion = ?, colonia = ?, estado_peticion = ? WHERE idpeticion = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ssssssi", $nombre, $descripcion, $telefono, $ubicacion, $colonia, $estado_peticion, $idpeticion);

    if ($stmt->execute()) {
        $_SESSION['edicion_exitosa'] = true;
        header("Location: /Proyecto/public/administracion.php");
        exit();
    } else {
        echo "Error al actualizar la petición.";
    }

    $stmt->close();
}
$conexion->close();
