<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : '';
$telefono = isset($_SESSION['telefono']) ? $_SESSION['telefono'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="interfaz/css/estilocuenta.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar fixed-top">
    <a class="navbar-brand" href="interfaz.php">Sistema de Petición Ciudadana</a>
</nav>
<div class="container mt-5 pt-5">
    <form id="perfil-form" action="controlador/cuenta.php" method="POST" onsubmit="return validarFormulario()">
        <table class="table">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Información</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Usuario</td><td><input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" disabled></td></tr>
                <tr><td>Correo</td><td><input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" disabled></td></tr>
                <tr><td>Teléfono</td><td><input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" disabled></td></tr>
                <tr>
                    <td>Nueva Contraseña</td>
                    <td>
                        <div class="password-field d-flex align-items-center">
                            <input type="password" id="contraseña" name="contraseña" placeholder="Nueva contraseña (opcional)" class="form-control" style="max-width: 300px;" disabled>
                            <input type="checkbox" id="toggle-password" onclick="mostrarOcultarContraseña()" class="ml-2">
                            <label for="toggle-password" class="ml-2">Mostrar</label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="profile-actions">
            <button type="button" id="editar-btn" onclick="habilitarEdicion()">Editar</button>
            <button type="submit" id="guardar-btn" name="guardar" disabled>Guardar Cambios</button>
            <!-- Botón que abre el modal de confirmación -->
            <button type="button" class="delete-btn" onclick="confirmarEliminarCuenta()">Eliminar Cuenta</button>
        </div>
    </form>
    <form action="controlador/logout.php" method="post">
        <button type="submit" class="logout-btn">Cerrar Sesión</button>
    </form>
</div>

<!-- Modal de confirmación para eliminar la cuenta -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmación de Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar su cuenta?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <!-- Botón para confirmar eliminación -->
                <button type="button" class="btn btn-danger" onclick="eliminarCuenta()">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Mensajes de Error -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalErrorMessage">
                    <?php
                    if (isset($_SESSION['errores'])) {
                        echo $_SESSION['errores'];
                        unset($_SESSION['errores']); // Limpiar los errores después de mostrarlos
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    
    $(document).ready(function() {
        // Mostrar el modal si hay un mensaje de error
        if ($('#modalErrorMessage').text().trim() !== '') {
            $('#errorModal').modal('show');
        }
    });

    function habilitarEdicion() {
        document.getElementById('usuario').disabled = false;
        document.getElementById('correo').disabled = false;
        document.getElementById('telefono').disabled = false;
        document.getElementById('contraseña').disabled = false;
        document.getElementById('guardar-btn').disabled = false;
    }

    function mostrarOcultarContraseña() {
        const contraseña = document.getElementById('contraseña');
        const checkbox = document.getElementById('toggle-password');
        contraseña.type = checkbox.checked ? 'text' : 'password';
    }

    // Función para abrir el modal de confirmación
    function confirmarEliminarCuenta() {
        $('#confirmDeleteModal').modal('show');
    }

    // Función para enviar el formulario de eliminación
    function eliminarCuenta() {
        const form = document.getElementById("perfil-form");
        const inputEliminar = document.createElement("input");
        inputEliminar.type = "hidden";
        inputEliminar.name = "eliminar";
        form.appendChild(inputEliminar);
        form.submit(); // Enviar el formulario para eliminar la cuenta
    }

</script>
</body>
</html>