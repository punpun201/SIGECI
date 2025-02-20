<?php
include("conexion.php");

$response = [
    "success" => false,
    "message" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = mysqli_real_escape_string($conexion, $_POST["Usuario"]);
    $correo = mysqli_real_escape_string($conexion, $_POST["Correo"]);
    $contrasena = mysqli_real_escape_string($conexion, $_POST["Contrasena"]);
    $conficontrasena = mysqli_real_escape_string($conexion, $_POST["Conficontrasena"]); 
    $telefono = mysqli_real_escape_string($conexion, $_POST["Telefono"]);

    $errores = [];

    // Validaciones de entrada
    if (!preg_match("/^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñ])[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/", $usuario)) {
        $errores[] = "El nombre de usuario no es válido. Solo debe contener letras, espacios y acentos.";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }
    if (!preg_match("/^[a-zA-Z0-9]{4,15}$/", $contrasena)) {
        $errores[] = "La contraseña solo debe contener letras y números, sin espacios ni caracteres especiales, y debe tener entre 4 y 15 caracteres.";
    }
    if ($contrasena !== $conficontrasena) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (!preg_match("/^\d{10}$/", $telefono)) {
        $errores[] = "El número de teléfono debe tener 10 dígitos.";
    }

    if (empty($errores)) {
        // Verificación de existencia en la tabla cliente
        $queryCliente = "SELECT * FROM cliente WHERE usuario = ? OR correo = ? OR telefono = ?";
        $stmtCliente = $conexion->prepare($queryCliente);
        $stmtCliente->bind_param("sss", $usuario, $correo, $telefono);
        $stmtCliente->execute();
        $resultadoCliente = $stmtCliente->get_result();

        // Verificación de existencia en la tabla usuarios
        $queryUsuarios = "SELECT * FROM usuarios WHERE usuario = ? OR correo = ? OR telefono = ?";
        $stmtUsuarios = $conexion->prepare($queryUsuarios);
        $stmtUsuarios->bind_param("sss", $usuario, $correo, $telefono);
        $stmtUsuarios->execute();
        $resultadoUsuarios = $stmtUsuarios->get_result();

        if ($resultadoCliente->num_rows > 0 || $resultadoUsuarios->num_rows > 0) {
            $errores[] = "Los datos que intenta ingresar ya existen en el sistema. Verifique el nombre de usuario, correo y teléfono.";
        }

        $stmtCliente->close();
        $stmtUsuarios->close();
    }

    if (empty($errores)) {
        // Registrar usuario si no hay errores
        $contrasenaHasheada = password_hash($contrasena, PASSWORD_BCRYPT);
        $stmt = $conexion->prepare("INSERT INTO cliente (usuario, correo, contraseña, telefono) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario, $correo, $contrasenaHasheada, $telefono);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Cuenta creada exitosamente. Redirigiendo al inicio...";
        } else {
            $response["message"] = "Error al registrar el usuario. Por favor, inténtelo de nuevo.";
        }
        $stmt->close();
    } else {
        $response["message"] = implode("<br>", $errores);
    }

    mysqli_close($conexion);
}

// Devolver respuesta JSON
header("Content-Type: application/json");
echo json_encode($response);
exit();