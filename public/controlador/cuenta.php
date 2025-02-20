<?php
session_start();
include("conexion.php");

// Verificar si el tipo de usuario está presente en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    echo "<script>alert('Error: No se encontró el tipo de usuario en la sesión.'); 
          window.location.href = '/Proyecto/public/index.php';</script>";
    exit();
}

// Obtener el tipo de usuario de la sesión, normalizado a minúsculas y sin espacios
$tipo_usuario = strtolower(trim($_SESSION['tipo_usuario']));
$usuario_actual = $_SESSION['usuario'];

// Verificar si se desea eliminar la cuenta
if (isset($_POST['eliminar'])) {
    if ($tipo_usuario === 'cliente') {
        // Obtener idcliente basado en el usuario actual
        $queryIdCliente = "SELECT idcliente FROM cliente WHERE usuario = '$usuario_actual'";
        $resultadoId = mysqli_query($conexion, $queryIdCliente);
        $filaId = mysqli_fetch_assoc($resultadoId);
        $idcliente = $filaId['idcliente'];

        // Eliminar comentarios asociados a las peticiones del cliente
        $queryEliminarComentarios = "DELETE FROM comentarios WHERE idpeticion IN (SELECT idpeticion FROM peticion WHERE idcliente = '$idcliente')";
        mysqli_query($conexion, $queryEliminarComentarios);

        // Eliminar peticiones asociadas al cliente
        $queryEliminarPeticiones = "DELETE FROM peticion WHERE idcliente = '$idcliente'";
        mysqli_query($conexion, $queryEliminarPeticiones);

        // Eliminar la cuenta del cliente
        $consulta_eliminar = "DELETE FROM cliente WHERE usuario = '$usuario_actual'";
    } else {
        // Eliminar la cuenta del usuario o administrador
        $consulta_eliminar = "DELETE FROM usuarios WHERE usuario = '$usuario_actual'";
    }

    if (mysqli_query($conexion, $consulta_eliminar)) {
        session_unset();
        session_destroy();
        header('Location: /Proyecto/public/index.php?cuenta_eliminada=1');
        exit();
    } else {
        echo "Error al eliminar la cuenta: " . mysqli_error($conexion);
    }
}

// Obtener los datos del formulario
$usuario = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
$correo = mysqli_real_escape_string($conexion, trim($_POST['correo']));
$telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
$contraseña = trim($_POST['contraseña']); // Elimina espacios extra

$consulta_actualizar = "";

// Verificar si los datos existen en otras cuentas
$errores = [];
$queryExistenciaCliente = "SELECT * FROM cliente WHERE (usuario = ? OR correo = ? OR telefono = ?) AND usuario != ?";
$queryExistenciaUsuarios = "SELECT * FROM usuarios WHERE (usuario = ? OR correo = ? OR telefono = ?) AND usuario != ?";

$stmtCliente = $conexion->prepare($queryExistenciaCliente);
$stmtCliente->bind_param("ssss", $usuario, $correo, $telefono, $usuario_actual);
$stmtCliente->execute();
$resultadoCliente = $stmtCliente->get_result();

$stmtUsuarios = $conexion->prepare($queryExistenciaUsuarios);
$stmtUsuarios->bind_param("ssss", $usuario, $correo, $telefono, $usuario_actual);
$stmtUsuarios->execute();
$resultadoUsuarios = $stmtUsuarios->get_result();

if ($resultadoCliente->num_rows > 0 || $resultadoUsuarios->num_rows > 0) {
    $errores[] = "Los datos que intenta ingresar ya existen en otra cuenta. Verifique el nombre de usuario, correo y teléfono.";
}

$stmtCliente->close();
$stmtUsuarios->close();

// Validación y hash de la nueva contraseña
if (!empty($contraseña)) {
    if (strlen($contraseña) < 6 || preg_match('/[^a-zA-Z0-9]/', $contraseña) || preg_match('/\s/', $contraseña)) {
        $errores[] = "La nueva contraseña debe tener al menos 6 caracteres, sin caracteres especiales ni espacios en blanco.";
    } else {
        $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);
    }
}

// Si hay errores, mostrar modal de error
if (!empty($errores)) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header('Location: /Proyecto/public/Cuenta.php');
    exit();
}

// Generar la consulta de actualización si no hay errores
switch ($tipo_usuario) {
    case 'cliente':
        $consulta_actualizar = !empty($contraseña) 
            ? "UPDATE cliente SET usuario = '$usuario', correo = '$correo', telefono = '$telefono', contraseña = '$hashed_password' WHERE usuario = '$usuario_actual'"
            : "UPDATE cliente SET usuario = '$usuario', correo = '$correo', telefono = '$telefono' WHERE usuario = '$usuario_actual'";
        break;

    case 'usuario':
    case 'administrador':
        $consulta_actualizar = !empty($contraseña) 
            ? "UPDATE usuarios SET usuario = '$usuario', correo = '$correo', telefono = '$telefono', contraseña = '$hashed_password' WHERE usuario = '$usuario_actual'"
            : "UPDATE usuarios SET usuario = '$usuario', correo = '$correo', telefono = '$telefono' WHERE usuario = '$usuario_actual'";
        break;

    default:
        echo "<script>alert('Tipo de usuario no válido: $tipo_usuario.'); 
              window.location.href = '/Proyecto/public/index.php';</script>";
        exit();
}

// Ejecutar la consulta de actualización
if (!empty($consulta_actualizar) && mysqli_query($conexion, $consulta_actualizar)) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['correo'] = $correo;
    $_SESSION['telefono'] = $telefono;

    header('Location: /Proyecto/public/Cuenta.php?actualizado=1');
    exit();
} else {
    echo "Error al actualizar: " . mysqli_error($conexion);
}

mysqli_close($conexion);