<?php
session_start();

$usuarioLogueado = isset($_SESSION['usuario']);
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="interfaz/css/estilosistema.css">
    <title>Sistema</title>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="interfaz.php">Sistema de petición ciudadana</a>
        </div>
        <div class="navbar-right">
            <?php if ($usuarioLogueado): ?>
                <?php if ($tipo_usuario === 'cliente'): ?>
                    <a href="Cuenta.php">Cuenta</a>
                <?php elseif ($tipo_usuario === 'Usuario'): ?>
                    <a href="administracion.php">Lista de peticiones</a>
                    <a href="Cuenta.php">Cuenta</a>
                <?php elseif ($tipo_usuario === 'Administrador'): ?>
                    <a href="reportes.php">Reportes</a>
                    <a href="crearusuario.php">Crear usuario</a>
                    <a href="administracion.php">Lista de peticiones</a>
                    <a href="Cuenta.php">Cuenta</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>

    <div class="petición">
        <h1>Sistema de petición ciudadana</h1>
        <div class="enlaces">
            <p class="highlight">¿Tiene algún inconveniente en tu localidad?<br><br>
                Si tiene algún inconveniente en su localidad, alguna queja o problema, por favor acceda al apartado "Crear petición" para tomar la captura de su situación.<br><br>
                Si necesita ver el estado de su petición, ingrese en "Ver estado de petición".<br><br>
            </p>
            <?php if ($usuarioLogueado): ?>
                <?php if ($tipo_usuario === 'cliente'): ?>
                    <a href="Ingresardatos.php">Crear petición</a>
                    <a href="peticion.php" class="ver">Ver estado de petición</a>
                <?php else: ?>
                    <!-- Enlaces deshabilitados para Usuario y Administrador -->
                    <a href="#" class="disabled-link">Crear petición</a>
                    <a href="#" class="disabled-link">Ver estado de petición</a>
                <?php endif; ?>
            <?php else: ?>
                <!-- Enlaces deshabilitados cuando no se ha iniciado sesión -->
                <a href="#" class="disabled-link" onclick="alert('Debe iniciar sesión para crear una petición.')">Crear petición</a>
                <a href="#" class="disabled-link" onclick="alert('Debe iniciar sesión para ver el estado de la petición.')">Ver estado de petición</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para éxito en el registro de la petición -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registro Exitoso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    La petición se ha registrado exitosamente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para bloquear acceso si no ha iniciado sesión -->
    <?php if (!$usuarioLogueado): ?>
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">¡Bienvenido al GECI!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="display: none;"></button>
                </div>
                <div class="modal-body">
                    <p>¿Tienes algún inconveniente? <br><br>
                        Primero <a href="registro.html">crea una cuenta</a> para acceder al sistema.</p>
                    <p>¡Es rápido y fácil!</p>
                    <p>Si ya tienes una cuenta, por favor <a href="inicio.html">inicia sesión</a>.<br><br></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']): ?>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'), {});
                successModal.show();
                <?php unset($_SESSION['registro_exitoso']); ?>
            <?php endif; ?>

            <?php if (!$usuarioLogueado): ?>
                var registroModal = new bootstrap.Modal(document.getElementById('registroModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                registroModal.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>