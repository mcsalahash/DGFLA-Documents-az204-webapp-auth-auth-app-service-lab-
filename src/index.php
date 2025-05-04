<?php
require_once 'config.php';
session_start();

// Vérification si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user']);

include 'views/home.php';