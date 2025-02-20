<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['tipo_usuario'], ['Administrador', 'Usuario'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario']);
    $idpeticion = $_POST['idpeticion'];

    if (!empty($comentario) && !empty($idpeticion)) {
        $query = "INSERT INTO comentarios (idpeticion, comentario, fecha_comentario) VALUES (?, ?, NOW())";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("is", $idpeticion, $comentario);

        if ($stmt->execute()) {
            // Redirigir a la página anterior o mostrar mensaje de éxito
            header("Location: ../administracion.php?comentario=exito");
            exit();
        } else {
            echo "Error al guardar el comentario.";
        }

        $stmt->close();
    } else {
        echo "Comentario o ID de petición vacío.";
    }
}
$conexion->close();