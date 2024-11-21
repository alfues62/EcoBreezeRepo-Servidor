<?php
session_start();
ob_start(); // Inicia el buffer de salida
// Incluye la vista de la página de usuario
include '../../frontend/php/edicion_usuario.vista.php';
