<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirige al index
    header("Location: index.php");
    exit();
}

$usuarioLogueado = isset($_SESSION['usuario']);
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="interfaz/css/estilosistema.css">
    <title>Registro de datos</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const estadoSelect = document.getElementById("estado");
            const municipioSelect = document.getElementById("municipio");

            const municipiosCampeche = [
                { value: "Calkini", text: "Calkiní" },
                { value: "Campeche", text: "San Francisco de Campeche" },
                { value: "Carmen", text: "Carmen" },
                { value: "Champoton", text: "Champotón" },
                { value: "Hecelchakan", text: "Hecelchakán" },
                { value: "Hopelchen", text: "Hopelchén" },
                { value: "Palizada", text: "Palizada" },
                { value: "Tenabo", text: "Tenabo" },
                { value: "Escarcega", text: "Escárcega" },
                { value: "Calakmul", text: "Calakmul" },
                { value: "Candelaria", text: "Candelaria" },
                { value: "Seybaplaya", text: "Seybaplaya" },
                { value: "Dzitbalche", text: "Dzitbalché" }
            ];

            estadoSelect.addEventListener("change", function() {
                municipioSelect.innerHTML = "<option value=''>Selecciona un municipio</option>";

                if (estadoSelect.value === "Campeche") {
                    municipiosCampeche.forEach(municipio => {
                        const option = document.createElement("option");
                        option.value = municipio.value;
                        option.textContent = municipio.text;
                        municipioSelect.appendChild(option);
                    });
                }
            });
        });

        function validarFormulario(event) {
            event.preventDefault();
            let errores = [];

            let nombre = document.querySelector("input[name='nombre']");
            let telefono = document.querySelector("input[name='telefono']");
            let ubicacion = document.querySelector("input[name='ubicacion']");
            let colonia = document.querySelector("input[name='colonia']");
            let descripcion = document.querySelector("textarea[name='descripcion']");
            let estado = document.querySelector("select[name='estado']");
            let municipio = document.querySelector("select[name='municipio']");

            //Validacion de campos
            if (!/^(?!\s*$)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre.value.trim())) {
                errores.push("El campo Nombre solo debe contener letras, espacios y acentos, y no puede estar vacío.");
            }
            if (!/^\d{10}$/.test(telefono.value.trim())) {
                errores.push("El campo Teléfono debe contener solo 10 dígitos y no puede estar vacío.");
            }
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s,.:;]+$/.test(ubicacion.value.trim())) {
                errores.push("El campo Ubicación puede contener letras, números y signos de puntuación, pero no puede estar vacío o conformado de espacios.");
            }
            if (!/^(?!\s*$)[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(colonia.value.trim())) {
            errores.push("El campo Colonia solo debe contener letras, espacios y acentos, y no puede estar vacío o conformado solo por espacios.");
            }
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s,.:;]+$/.test(descripcion.value.trim())) {
                errores.push("El campo Descripción solo debe contener letras, números y signos de puntuación, pero no puede estar vacío o conformado de espacios.");
            }
            if (estado.value === "") {
                errores.push("Por favor, selecciona un Estado.");
            }
            if (municipio.value === "") {
                errores.push("Por favor, selecciona un Municipio.");
            }

            // Mostrar errores en la interfaz si existen
            let errorDiv = document.getElementById("errores");
            errorDiv.innerHTML = ""; // Limpia mensajes previos

            if (errores.length > 0) {
                errores.forEach(error => {
                    let errorItem = document.createElement("p");
                    errorItem.textContent = error;
                    errorItem.style.color = "red";
                    errorDiv.appendChild(errorItem);
                });
            } else {
                // Envia el formulario si no hay errores
                document.getElementById("formulario-peticion").submit();
            }
        }
    </script>
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
                    <a href="Cuenta.html">Cuenta</a>
                <?php elseif ($tipo_usuario === 'Administrador'): ?>
                    <a href="crearusuario.php">Crear usuario</a>
                    <a href="administracion.php">Lista de peticiones</a>
                    <a href="Cuenta.php">Cuenta</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>
    <div class="overlay"></div>
    <div class="popup">
        <h3>Ingrese los datos requeridos</h3>
        <div id="errores"></div> <!-- Aquí el ontenedor para los mensajes de error -->
        <form id="formulario-peticion" action="controlador/crearpeticion.php" method="POST" onsubmit="validarFormulario(event)">
            <div class="inputs">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="tel" name="telefono" placeholder="Teléfono WhatsApp" pattern="\d{10}" required>
                <input type="text" name="ubicacion" placeholder="Ubicación" required>
                <input type="text" name="colonia" placeholder="Colonia" required>
                <textarea id="descripcion" name="descripcion" rows="4" placeholder="Escribe una descripción detallada de tu problema"></textarea>
                <select id="estado" name="estado" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Aguascalientes">Aguascalientes</option>
                    <option value="Baja California">Baja California</option>
                    <option value="Baja California Sur">Baja California Sur</option>
                    <option value="Campeche">Campeche</option>
                    <option value="chiapas">Chiapas</option>
                    <option value="chihuahua">Chihuahua</option>
                    <option value="cdmx">Ciudad de México</option>
                    <option value="coahuila">Coahuila</option>
                    <option value="colima">Colima</option>
                    <option value="durango">Durango</option>
                    <option value="estado_de_mexico">Estado de México</option>
                    <option value="guanajuato">Guanajuato</option>
                    <option value="guerrero">Guerrero</option>
                    <option value="hidalgo">Hidalgo</option>
                    <option value="jalisco">Jalisco</option>
                    <option value="michoacan">Michoacán</option>
                    <option value="morelos">Morelos</option>
                    <option value="nayarit">Nayarit</option>
                    <option value="nuevo_leon">Nuevo León</option>
                    <option value="oaxaca">Oaxaca</option>
                    <option value="puebla">Puebla</option>
                    <option value="queretaro">Querétaro</option>
                    <option value="quintana_roo">Quintana Roo</option>
                    <option value="san_luis_potosi">San Luis Potosí</option>
                    <option value="sinaloa">Sinaloa</option>
                    <option value="sonora">Sonora</option>
                    <option value="tabasco">Tabasco</option>
                    <option value="tamaulipas">Tamaulipas</option>
                    <option value="tlaxcala">Tlaxcala</option>
                    <option value="veracruz">Veracruz</option>
                    <option value="yucatan">Yucatán</option>
                    <option value="zacatecas">Zacatecas</option>
                </select>
                <select id="municipio" name="municipio" required>
                    <option value="">Selecciona un municipio</option>
                </select>
            </div>
            <input type="submit" class="btn-submit" value="Cargar">
        </form>
    </div>
</body>
</html>