<?php
session_start();
include("conexion.php");

// Asegurarse de que el usuario está logueado y de que existe un idcliente en la sesión
if (!isset($_SESSION['idcliente'])) {
    echo "Error: No se encontró el ID del cliente en la sesión.";
    exit();
}

$idcliente = $_SESSION['idcliente'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST["nombre"]));
    $telefono = mysqli_real_escape_string($conexion, trim($_POST["telefono"]));
    $ubicacion = mysqli_real_escape_string($conexion, trim($_POST["ubicacion"]));
    $colonia = mysqli_real_escape_string($conexion, trim($_POST["colonia"]));
    $descripcion = mysqli_real_escape_string($conexion, trim($_POST["descripcion"]));
    $estado = mysqli_real_escape_string($conexion, trim($_POST["estado"]));
    $municipio = mysqli_real_escape_string($conexion, trim($_POST["municipio"]));

    if (empty($nombre) || empty($telefono) || empty($ubicacion) || empty($colonia) || empty($descripcion) || empty($estado) || empty($municipio)) {
        echo "<script>alert('Por favor, completa todos los campos requeridos.');</script>";
        exit();
    }

    // Consulta SQL para insertar los datos en la tabla `peticion`
    $query = "INSERT INTO peticion (nombre, telefono, ubicacion, colonia, descripcion, estado, municipio, idcliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("sssssssi", $nombre, $telefono, $ubicacion, $colonia, $descripcion, $estado, $municipio, $idcliente);

    if ($stmt->execute()) {
        $_SESSION['registro_exitoso'] = true;
        header("Location: ../interfaz.php"); // Redirigir a la interfaz principal
        exit();
    } else {
        echo "<script>alert('Error al registrar la petición. Intenta de nuevo.');</script>";
    }

    $stmt->close();
    mysqli_close($conexion);
} else {
    echo "Método no permitido.";
}