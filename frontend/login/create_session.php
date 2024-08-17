<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = isset($data['user_id']) ? $data['user_id'] : null;

    if ($userId) {
        $_SESSION['user_id'] = $userId;
        // Vous pouvez également stocker d'autres informations dans la session si nécessaire
        response(["status" => 200, "message" => "Session créée avec succès."], 200);
    } else {
        response(["status" => 400, "message" => "ID utilisateur non fourni."], 400);
    }
} else {
    response(["status" => 405, "message" => "Méthode non autorisée."], 405);
}

function response($data, $status)
{
    http_response_code($status);
    echo json_encode($data);
}