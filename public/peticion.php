<?php
session_start();
include("controlador/reflejarpeticion.php");

$usuarioLogueado = isset($_SESSION['usuario']);
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="interfaz/css/estilopetición.css">
    <title>Estado de Petición</title>
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
                <a href="crearusuario.php">Crear usuario</a>
                <a href="administracion.php">Lista de peticiones</a>
                <a href="Cuenta.php">Cuenta</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>

<h1 class="text-center mt-4">Mis Peticiones</h1>
<div class="container mt-5">
    <div class="row">
        <?php foreach ($peticiones as $peticion): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($peticion['nombre']); ?></h5>
                        <p class="card-text">Estado: <?php echo htmlspecialchars($peticion['estado']); ?></p>
                        <p class="card-text"><strong>Estado de la petición:</strong> <?php echo htmlspecialchars($peticion['estado_peticion']); ?></p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPeticion<?php echo $peticion['idpeticion']; ?>">
                            Ver detalles
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal para ver detalles de la petición -->
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
                            <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($peticion['hora_peticion']); ?></p>
                            
                            <!-- Mostrar comentarios -->
                            <h6 class="mt-3">Comentarios:</h6>
                            <?php if (!empty($comentarios[$peticion['idpeticion']])): ?>
                                <ul class="list-group">
                                    <?php foreach ($comentarios[$peticion['idpeticion']] as $comentario): ?>
                                        <li class="list-group-item">
                                            <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                                            <small class="text-muted">Fecha: <?php echo htmlspecialchars($comentario['fecha_comentario']); ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No hay comentarios para esta petición.</p>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <a href="editarpeticion.php?idpeticion=<?php echo $peticion['idpeticion']; ?>" class="btn btn-warning">Editar</a>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>