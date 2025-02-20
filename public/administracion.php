<?php
session_start();
include("controlador/verpeticion.php");

// Los de abajo son parametros de seguridad para sacar a alguien en caso de navegación por URL, sino esta logeado o si no es rol administrador o usuario
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['tipo_usuario'], ['Administrador', 'Usuario'])) {
    header("Location: index.php");
    exit();
}

$usuarioLogueado = isset($_SESSION['usuario']);
$tipo_usuario = $_SESSION['tipo_usuario'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="interfaz/css/estiloadministracion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Administración</title>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="interfaz.php">Sistema de petición ciudadana</a>
        </div>
        <!-- Lo de abajo son condiciones, que al logearse, las pantallas que cada tipo de usuario puede ver e ingresar -->
        <div class="navbar-right">
            <?php if ($usuarioLogueado): ?>
                <?php if ($tipo_usuario === 'cliente'): ?>
                    <a href="Cuenta.php">Cuenta</a>
                <?php elseif ($tipo_usuario === 'Usuario'): ?>
                    <a href="administracion.php">Lista de peticiones</a>
                    <a href="Cuenta.php">Cuenta</a>
                <?php elseif ($tipo_usuario === 'Administrador'): ?>
                    <a href="controlador/reportes.php">Reportes</a>
                    <a href="crearusuario.php">Crear usuario</a>
                    <a href="administracion.php">Lista de peticiones</a>
                    <a href="Cuenta.php">Cuenta</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Barra de busqueda, mediante texto y los parametros en que estado se encuentra la petición -->
    <h2 class="text-center mt-3">Lista de peticiones ciudadanas</h2>
    <form id="searchForm" method="GET">
        <div class="search-container">
            <input type="text" name="search" class="search-bar" placeholder="Buscar...">
            <button type="submit" class="search-button">Buscar</button>
        </div>
        <div class="checkbox-group">
            <label>
                Pendiente
                <input type="checkbox" name="estado[]" value="pendiente" checked>
            </label>
            <label>
                En revisión 
                <input type="checkbox" name="estado[]" value="en revision" checked> 
            </label>
            <label>
                Revisado
                <input type="checkbox" name="estado[]" value="revisado" checked>
            </label>
        </div>
    </form>

    <!-- Listado de resultados -->
    <div class="container mt-4">
        <ul class="list-group">
            <?php if (empty($peticiones)) : ?>
                <li class="list-group-item">No se encontraron resultados para tu búsqueda.</li>
            <?php else: ?>
                <?php foreach ($peticiones as $peticion): ?>
                    <li class="list-group-item">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalPeticion<?php echo $peticion['idpeticion']; ?>">
                            <?php echo htmlspecialchars($peticion['nombre']); ?>
                        </a>
                    </li>

                    <!-- Modal para mostrar los detalles de la petición -->
                    <div class="modal fade" id="modalPeticion<?php echo $peticion['idpeticion']; ?>" tabindex="-1" aria-labelledby="modalPeticionLabel<?php echo $peticion['idpeticion']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalPeticionLabel<?php echo $peticion['idpeticion']; ?>"><?php echo htmlspecialchars($peticion['nombre']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($peticion['descripcion']); ?></p>
                                    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($peticion['ubicacion']); ?></p>
                                    <p><strong>Colonia:</strong> <?php echo htmlspecialchars($peticion['colonia']); ?></p>
                                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($peticion['estado']); ?></p>
                                    <p><strong>Municipio:</strong> <?php echo htmlspecialchars($peticion['municipio']); ?></p>
                                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($peticion['telefono']); ?></p>
                                    <p><strong>Estado de la petición:</strong> <?php echo htmlspecialchars($peticion['estado_peticion']); ?></p>
                                    <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($peticion['hora_peticion']); ?></p>

                                    <form action="controlador/guardar_comentario.php" method="post">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="comentario" name="comentario" rows="3" required placeholder="Escribe un comentario"></textarea>
                                        </div>
                                        <input type="hidden" name="idpeticion" value="<?php echo $peticion['idpeticion']; ?>">
                                        <button type="submit" class="btn btn-primary">Comentar</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <a href="editarpeticion2.php?idpeticion=<?php echo $peticion['idpeticion']; ?>" class="btn btn-warning">Editar</a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?php echo $peticion['idpeticion']; ?>">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmación de eliminación -->
                    <div class="modal fade" id="modalConfirmarEliminar<?php echo $peticion['idpeticion']; ?>" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel<?php echo $peticion['idpeticion']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalConfirmarEliminarLabel<?php echo $peticion['idpeticion']; ?>">Confirmar Eliminación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Estás seguro de que deseas eliminar esta petición?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="controlador/eliminarpeticion.php" method="POST">
                                        <input type="hidden" name="idpeticion" value="<?php echo $peticion['idpeticion']; ?>">
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Modal de éxito de comentario -->
    <?php if ($comentarioExito): ?>
        <div class="modal fade" id="modalComentarioExito" tabindex="-1" aria-labelledby="modalComentarioExitoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalComentarioExitoLabel">Comentario guardado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>El comentario se ha enviado exitosamente.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar el modal de éxito automáticamente si el comentario fue guardado
        <?php if ($comentarioExito): ?>
            var modalExito = new bootstrap.Modal(document.getElementById('modalComentarioExito'));
            modalExito.show();
        <?php endif; ?>
    </script>
</body>
</html>