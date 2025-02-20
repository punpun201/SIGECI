<?php
session_start();

// Los de abajo son parametros de seguridad para sacar a alguien en caso de navegación por URL, sino esta logeado
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['tipo_usuario'], ['Administrador', 'Usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="interfaz/css/style3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Crear Usuario</title>
</head>
<body>
<div class="user">
        <h1>Crear usuario</h1>
        <form method="post" action="controlador/crearusuario.php" onsubmit="return validarFormulario()">
            <div class="input-group">
                <input type="text" name="nombre" required placeholder="Nombre">
                <label></label>
            </div>
            <div class="input-group">
                <input type="text" name="correo" required placeholder="Correo">
                <label></label>
            </div>
            <div class="input-group">
                <input type="password" name="contrasena" required placeholder="Contraseña">
                <label></label>
            </div>
            <div class="input-group">
                <input type="tel" name="telefono" required placeholder="Teléfono">
            </div>
            <div class="input-group">
            <select id="rol" name="rol" required>
                <option value="">Selecciona el rol del usuario</option>
                <option value="Usuario">Usuario</option>
                <option value="Administrador">Administrador</option>
            </select>
            </div>
            <input type="submit" value="Registrar" class="btn-submit">
        </form>
        <p><a href="interfaz.php">Regresar al sistema</a></p>
    </div>
    <!-- Modal de Mensajes -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function validarFormulario() {
            const nombre = document.getElementById('nombre').value.trim();
            const correo = document.getElementById('correo').value.trim();
            const contrasena = document.getElementById('contrasena').value.trim();
            const telefono = document.getElementById('telefono').value.trim();
            let mensajeError = '';

            if (!/^[a-zA-Z0-9]+$/.test(nombre)) {
                mensajeError += 'El nombre solo debe contener letras o números sin espacios.<br>';
            }
            if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(correo)) {
                mensajeError += 'El correo no tiene un formato válido.<br>';
            }
            if (!/^[a-zA-Z0-9]{4,25}$/.test(contrasena)) {
                mensajeError += 'La contraseña debe tener entre 4 y 25 caracteres sin espacios ni caracteres especiales.<br>';
            }
            if (!/^\d{10}$/.test(telefono)) {
                mensajeError += 'El teléfono debe tener exactamente 10 dígitos.<br>';
            }

            if (mensajeError) {
                mostrarMensaje(mensajeError);
                return false;
            }
            return true;
        }

        function mostrarMensaje(mensaje, exito = false) {
            document.getElementById('modalMessage').innerHTML = mensaje;
            $('#messageModal').modal('show');
            if (exito) {
                setTimeout(function() {
                    window.location.href = 'interfaz.php';
                }, 3000);
            }
        }

        // Mostrar el modal basado en el parámetro de URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') && urlParams.get('success') === '1') {
                mostrarMensaje('Usuario registrado exitosamente.', true);
            } else if (urlParams.has('error') && urlParams.get('error') === '1') {
                const message = urlParams.get('message') || 'Error al registrar el usuario.';
                mostrarMensaje(decodeURIComponent(message));
            }
        });
    </script>
</body>
</html>