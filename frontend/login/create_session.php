<?php
session_start(); // Démarre la session pour pouvoir enregistrer des données utilisateur

// Vérifie si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les données JSON envoyées dans le corps de la requête
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérifie si un ID utilisateur a été fourni dans les données reçues
    $userId = isset($data['user_id']) ? $data['user_id'] : null;

    // Si l'ID utilisateur est présent, on le stocke dans la session
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        // Vous pouvez également stocker d'autres informations utilisateur dans la session ici, si nécessaire
        response(["status" => 200, "message" => "Session créée avec succès."], 200); // Réponse de succès avec un code 200
    } else {
        // Si l'ID utilisateur n'est pas fourni, retourne une erreur avec un code 400
        response(["status" => 400, "message" => "ID utilisateur non fourni."], 400);
    }
} else {
    // Si la méthode n'est pas POST, retourne une erreur avec un code 405 pour méthode non autorisée
    response(["status" => 405, "message" => "Méthode non autorisée."], 405);
}

// Fonction utilitaire pour envoyer des réponses JSON avec un statut HTTP
function response($data, $status)
{
    http_response_code($status); // Définit le code de statut HTTP
    echo json_encode($data); // Encode les données en JSON et les envoie comme réponse
}
?>