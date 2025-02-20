<?php
session_start();
session_destroy(); 
header('Location: /Proyecto/public/index.php');  
exit();

