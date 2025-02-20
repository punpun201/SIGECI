<?php
require('fpdf186/fpdf.php');
include("controlador/conexion.php");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 10, utf8_decode('Reporte de Peticiones'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function AddPetition($petition) {
        $this->SetFillColor(230, 230, 250); // Color de fondo para cada petición
        $this->SetDrawColor(100, 100, 150); // Color de los bordes
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 100);
        
        // Encabezado de la petición
        $this->Cell(0, 10, utf8_decode('Detalles de la petición'), 0, 1, 'L', true);
        $this->Ln(2);

        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0, 0, 0);
        $this->SetX(10);

        // Bordes y formato de cada campo
        $fields = [
            'Nombre' => $petition['nombre'],
            'Descripción' => $petition['descripcion'],
            'Teléfono' => $petition['telefono'],
            'Ubicación Completa' => $petition['ubicacion_completa'],
            'Estado' => $petition['estado'],
            'Municipio' => $petition['municipio']
        ];

        foreach ($fields as $label => $value) {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(50, 8, utf8_decode($label . ':'), 0, 0, 'L');
            $this->SetFont('Arial', '', 11);
            $this->MultiCell(0, 8, utf8_decode($value), 0, 'L');
            $this->Ln(1); // Espacio entre cada campo
        }

        // Mostrar el estado subrayado con color según su valor en estado_peticion
        $estadoColor = [0, 0, 0]; // Negro por defecto
        if ($petition['estado_peticion'] === 'Pendiente') {
            $estadoColor = [0, 0, 255]; // Azul
        } elseif ($petition['estado_peticion'] === 'En revisión') {
            $estadoColor = [255, 165, 0]; // Naranja suave
        } elseif ($petition['estado_peticion'] === 'Revisado') {
            $estadoColor = [0, 128, 0]; // Verde
        }

        // Imprimir el estado subrayado con color
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor($estadoColor[0], $estadoColor[1], $estadoColor[2]);
        $this->Cell(50, 8, utf8_decode('Estado de la Petición:'), 0, 0, 'L');
        $this->SetFont('Arial', 'U', 11); // Subrayado
        $this->Cell(0, 8, utf8_decode($petition['estado_peticion']), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0); // Restablecer el color a negro

        $this->Ln(5); // Espacio entre peticiones
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Consulta para unir ubicación y colonia en una sola columna y agregar estado_peticion y municipio
$query = "SELECT nombre, descripcion, telefono, CONCAT(ubicacion, ', ', colonia) AS ubicacion_completa, estado, municipio, estado_peticion FROM peticion";
$resultado = $conexion->query($query);

while ($fila = $resultado->fetch_assoc()) {
    $pdf->AddPetition($fila);
}

$pdf->Output('D', 'reporte_peticiones.pdf');