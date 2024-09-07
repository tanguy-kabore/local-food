<?php
session_start(); // Démarre la session pour accéder aux variables de session
header('Content-Type: application/json'); // Définit le type de contenu de la réponse comme JSON

// Vérifie si l'utilisateur est connecté en s'assurant que l'ID de l'utilisateur existe et n'est pas vide dans la session
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Retourne la réponse en format JSON avec l'état de connexion de l'utilisateur
echo json_encode(['isLoggedIn' => $isLoggedIn]);
?>
