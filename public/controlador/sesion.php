<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: /Proyecto/public/inicio.html');
    exit();
}