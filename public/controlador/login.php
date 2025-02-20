<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $contraseña = $_POST['contraseña']; // No escapamos, usamos directamente en password_verify()

    // Buscar en la tabla cliente
    $consulta_cliente = "SELECT * FROM cliente WHERE usuario = '$usuario'";
    $resultado_cliente = mysqli_query($conexion, $consulta_cliente);

    if (mysqli_num_rows($resultado_cliente) === 1) {
        $fila_cliente = mysqli_fetch_assoc($resultado_cliente);

        if (password_verify($contraseña, $fila_cliente['contraseña'])) {
            // Guardar datos del cliente en la sesión, incluyendo idcliente
            $_SESSION['idcliente'] = $fila_cliente['idcliente'];
            $_SESSION['usuario'] = $fila_cliente['usuario'];
            $_SESSION['correo'] = $fila_cliente['correo'];
            $_SESSION['telefono'] = $fila_cliente['telefono'];
            $_SESSION['tipo_usuario'] = 'cliente';

            header('Location: /Proyecto/public/interfaz.php');
            exit();
        } else {
            header('Location: /Proyecto/public/inicio.html?error=Contraseña incorrecta. Inténtalo de nuevo.');
            exit();
        }
    }

    // Buscar en la tabla usuarios si no se encuentra en cliente
    $consulta_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $resultado_usuario = mysqli_query($conexion, $consulta_usuario);

    if (mysqli_num_rows($resultado_usuario) === 1) {
        $fila_usuario = mysqli_fetch_assoc($resultado_usuario);

        if (password_verify($contraseña, $fila_usuario['contraseña'])) {
            $_SESSION['usuario'] = $fila_usuario['usuario'];
            $_SESSION['correo'] = $fila_usuario['correo'];
            $_SESSION['telefono'] = $fila_usuario['telefono'];
            $_SESSION['tipo_usuario'] = $fila_usuario['rol'];

            header('Location: /Proyecto/public/interfaz.php');
            exit();
        } else {
            header('Location: /Proyecto/public/inicio.html?error=Contraseña incorrecta. Inténtalo de nuevo.');
            exit();
        }
    }

    // Si el usuario no se encuentra en ninguna tabla
    header('Location: /Proyecto/public/inicio.html?error=El usuario no existe.');
    exit();
}