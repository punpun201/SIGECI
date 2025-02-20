<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idpeticion'])) {
    $idpeticion = $_POST['idpeticion'];

    // Eliminar comentarios asociados a la petición
    $queryComentarios = "DELETE FROM comentarios WHERE idpeticion = ?";
    $stmtComentarios = $conexion->prepare($queryComentarios);
    $stmtComentarios->bind_param("i", $idpeticion);
    $stmtComentarios->execute();
    $stmtComentarios->close();

    // Luego, eliminar la petición
    $queryPeticion = "DELETE FROM peticion WHERE idpeticion = ?";
    $stmtPeticion = $conexion->prepare($queryPeticion);
    $stmtPeticion->bind_param("i", $idpeticion);

    if ($stmtPeticion->execute()) {
        $_SESSION['eliminacion_exitosa'] = true;

        // Redirección dinámica basada en el tipo de usuario
        if ($_SESSION['tipo_usuario'] === 'cliente') {
            header("Location: ../peticion.php"); // Redirige a la vista del cliente
        } else {
            header("Location: ../administracion.php"); // Redirige a la vista del administrador o usuario
        }
        exit();
    } else {
        echo "<script>alert('Error al eliminar la petición. Intenta de nuevo.'); window.history.back();</script>";
    }

    $stmtPeticion->close();
    $conexion->close();
} else {
    header("Location: ../index.php");
    exit();
}