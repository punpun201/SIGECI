<?php
include("controlador/conexion.php");

$idcliente = $_SESSION['idcliente'];
$peticion = [];

// Verificar que se envíe el ID de la petición
if (isset($_GET['idpeticion'])) {
    $idpeticion = $_GET['idpeticion'];

    // Obtener los datos de la petición actual
    $query = "SELECT nombre, descripcion, hora_peticion, estado, telefono, ubicacion, colonia, estado_peticion FROM peticion WHERE idpeticion = ? AND idcliente = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $idpeticion, $idcliente);
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

// Procesar la actualización de la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $telefono = $_POST['telefono'];
    $ubicacion = $_POST['ubicacion'];
    $colonia = $_POST['colonia'];

    $query = "UPDATE peticion SET nombre = ?, descripcion = ?, telefono = ?, ubicacion = ?, colonia = ? WHERE idpeticion = ? AND idcliente = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ssssssi", $nombre, $descripcion, $telefono, $ubicacion, $colonia, $idpeticion, $idcliente);

    if ($stmt->execute()) {
        $_SESSION['edicion_exitosa'] = true;
        header("Location: /Proyecto/public/peticion.php");
        exit();
    } else {
        echo "Error al actualizar la petición.";
    }

    $stmt->close();
}
$conexion->close();