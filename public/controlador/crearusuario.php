<?php
session_start();
include("conexion.php");

function validarUsuario($usuario) {
    return preg_match("/^[a-zA-Z0-9]+$/", $usuario);
}

function validarCorreo($correo) {
    return filter_var($correo, FILTER_VALIDATE_EMAIL);
}

function validarContrasena($contrasena) {
    return preg_match("/^[a-zA-Z0-9]{4,25}$/", $contrasena);
}

function validarTelefono($telefono) {
    return preg_match("/^\d{10}$/", $telefono);
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['nombre'])));
    $correo = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['correo'])));
    $contrasena = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['contrasena'])));
    $telefono = mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['telefono'])));
    $rol = isset($_POST['rol']) ? mysqli_real_escape_string($conexion, htmlspecialchars(trim($_POST['rol']))) : '';

    // Validaciones de campo
    if (!validarUsuario($usuario)) {
        $errores[] = "El nombre de usuario no es válido.";
    }
    if (!validarCorreo($correo)) {
        $errores[] = "El correo no tiene un formato válido.";
    }
    if (!validarContrasena($contrasena)) {
        $errores[] = "La contraseña debe tener entre 4 y 25 caracteres.";
    }
    if (!validarTelefono($telefono)) {
        $errores[] = "El teléfono debe tener 10 dígitos.";
    }
    if ($rol !== 'Usuario' && $rol !== 'Administrador') {
        $errores[] = "El rol seleccionado no es válido.";
    }

    // Verificación en ambas tablas
    if (empty($errores)) {
        $queryUsuarios = "SELECT * FROM usuarios WHERE usuario = ? OR correo = ? OR telefono = ?";
        $stmtUsuarios = $conexion->prepare($queryUsuarios);
        $stmtUsuarios->bind_param("sss", $usuario, $correo, $telefono);
        $stmtUsuarios->execute();
        $resultadoUsuarios = $stmtUsuarios->get_result();

        if ($resultadoUsuarios->num_rows > 0) {
            $fila = $resultadoUsuarios->fetch_assoc();
            if ($fila['usuario'] === $usuario) {
                $errores[] = "El nombre de usuario ya existe.";
            }
            if ($fila['correo'] === $correo) {
                $errores[] = "El correo ya está registrado.";
            }
            if ($fila['telefono'] === $telefono) {
                $errores[] = "El número de teléfono ya está registrado.";
            }
        }
        $stmtUsuarios->close();

        $queryClientes = "SELECT * FROM cliente WHERE usuario = ? OR correo = ? OR telefono = ?";
        $stmtClientes = $conexion->prepare($queryClientes);
        $stmtClientes->bind_param("sss", $usuario, $correo, $telefono);
        $stmtClientes->execute();
        $resultadoClientes = $stmtClientes->get_result();

        if ($resultadoClientes->num_rows > 0) {
            $fila = $resultadoClientes->fetch_assoc();
            if ($fila['usuario'] === $usuario) {
                $errores[] = "El nombre de usuario ya existe.";
            }
            if ($fila['correo'] === $correo) {
                $errores[] = "El correo ya está registrado.";
            }
            if ($fila['telefono'] === $telefono) {
                $errores[] = "El número de teléfono ya está registrado.";
            }
        }
        $stmtClientes->close();

        // Insertar usuario si no hay errores
        if (empty($errores)) {
            $contrasenaHasheada = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, correo, contraseña, rol, telefono) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $usuario, $correo, $contrasenaHasheada, $rol, $telefono);

            if ($stmt->execute()) {
                $stmt->close();
                $conexion->close();
                header("Location: /Proyecto/public/crearusuario.php?success=1");
                exit();
            } else {
                $stmt->close();
                $conexion->close();
                header("Location: /Proyecto/public/crearusuario.php?error=1&message=" . urlencode("Error al registrar el usuario. Inténtelo de nuevo."));
                exit();
            }
        }
    }

    // Si hay errores, mostrarlos en el modal
    $error_messages = implode("<br>", $errores);
    header("Location: /Proyecto/public/crearusuario.php?error=1&message=" . urlencode($error_messages));
    exit();
}