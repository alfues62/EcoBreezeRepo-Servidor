<?php
session_start();
ob_start(); // Inicia el buffer de salida
// Incluye la vista de la página de usuario
include '../../frontend/php/inicio_usuario.vista.php';
