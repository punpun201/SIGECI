<?php
session_start();
include("controlador/conexion.php");
include("controlador/editarpeticion1.php");

if (!isset($_SESSION['idcliente']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Petición</title>
    <link rel="stylesheet" href="interfaz/css/estiloeditarpeticion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Petición</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="descripcion" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required value="<?php echo htmlspecialchars($peticion['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción" required><?php echo htmlspecialchars($peticion['descripcion']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" value="<?php echo htmlspecialchars($peticion['telefono']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ubicación" value="<?php echo htmlspecialchars($peticion['ubicacion']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="colonia" class="form-label">Colonia</label>
                <input type="text" class="form-control" id="colonia" name="colonia" placeholder="Colonia" value="<?php echo htmlspecialchars($peticion['colonia']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="peticion.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>