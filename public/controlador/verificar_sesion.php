<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: /Proyecto/public/interfaz.php');  
    $loggedIn = false;  
} else {
    $loggedIn = true;  
}

