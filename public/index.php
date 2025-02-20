<?php
session_start();

$usuarioLogueado = isset($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="interfaz/css/estiloinicio.css">
    <title>Sistema</title>
    <script defer src="js/estilosistema.js"></script>
</head>
<body>
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroModalLabel">¡Bienvenido al SIGECI!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="display: none;"></button>
            </div>
            <div class="modal-body">
                <p class="problema-texto">¿Tienes alguna petición ciudadana?</p>
                <p>Entonces, crea una cuenta para tomar en cuenta tu petición.</p>
                <p>¡Es rápido y fácil!</p>
                <a href="registro.html" class="btn-crear-cuenta">Crear cuenta</a>
                <br><br>
                <p>Si ya tienes una cuenta creada, por favor inicia sesión.</p>
                <a href="inicio.html" class="btn-iniciar-sesion">Iniciar sesión</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
