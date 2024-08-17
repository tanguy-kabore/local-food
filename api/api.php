<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('config.php');

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

if ($method === 'OPTIONS') {
    response([], 200);
    exit;
}

switch ($method) {
    case 'POST':
        if ($endpoint === 'login') {
            handleLogin();
        } elseif ($endpoint === 'logout') {
            handleLogout();
        } else {
            /* if (isLoggedIn()) {
                handleCreateFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            } */
            handleCreateFood();
        }
        break;

    case 'GET':
        if ($endpoint === 'foods') {
            handleGetFoods();
        }
        break;

    case 'PUT':
        if ($endpoint === 'foods') {
            /* if (isLoggedIn()) {
                handleUpdateFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            } */
            handleUpdateFood();
        }
        break;

    case 'DELETE':
        if ($endpoint === 'foods') {
            /* if (isLoggedIn()) {
                handleDeleteFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            } */
            handleDeleteFood();
        }
        break;

    default:
        response(["message" => "Méthode non autorisée."], 405);
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function handleLogin()
{
    global $connexion;
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->username) && isset($data->password)) {
        $username = $data->username;
        $password = $data->password;
        $stmt = $connexion->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            response(["status" => 200, "message" => "Connexion réussie.", "user_id" => $user['id']], 200);
        } else {
            response(["status" => 401, "message" => "Nom d'utilisateur ou mot de passe invalide."], 401);
        }
    } else {
        response(["status" => 400, "message" => "Nom d'utilisateur et mot de passe requis."], 400);
    }
}

function handleLogout()
{
    response(["message" => "Déconnexion réussie."], 200);
}

function handleCreateFood()
{
    global $connexion;
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->name, $data->description, $data->price, $data->image_url)) {
        $stmt = $connexion->prepare("INSERT INTO foods (name, description, price, image_url) VALUES (:name, :description, :price, :image_url)");
        $stmt->bindParam(':name', $data->name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $data->description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $data->price, PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data->image_url, PDO::PARAM_STR);
        if ($stmt->execute()) {
            response(["message" => "Menu ajouté avec succès."], 201);
        } else {
            response(["message" => "Échec de l'ajout du menu."], 500);
        }
    } else {
        response(["message" => "Les données sont incomplètes."], 400);
    }
}

function handleGetFoods()
{
    global $connexion;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    if ($limit <= 0 || $offset < 0) {
        response(["message" => "Paramètres invalides."], 400);
        return;
    }

    try {
        if ($id !== null) {
            $stmt = $connexion->prepare("SELECT * FROM foods WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $food = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($food) {
                response($food, 200);
            } else {
                response(["message" => "Aucun menu trouvé avec l'ID spécifié."], 404);
            }
        } else {
            $stmt = $connexion->query("SELECT COUNT(*) FROM foods");
            $total = $stmt->fetchColumn();

            $stmt = $connexion->prepare("SELECT * FROM foods LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            response([
                'total' => $total,
                'data' => $foods
            ], 200);
        }
    } catch (Exception $e) {
        response(["message" => "Une erreur s'est produite : " . $e->getMessage()], 500);
    }
}

function handleUpdateFood()
{
    global $connexion;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = json_decode(file_get_contents("php://input"));
    if ($id !== null && isset($data->name, $data->description, $data->price, $data->image_url)) {
        $stmt = $connexion->prepare("UPDATE foods SET name = :name, description = :description, price = :price, image_url = :image_url WHERE id = :id");
        $stmt->bindParam(':name', $data->name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $data->description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $data->price, PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data->image_url, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            response(["message" => "Menu mis à jour avec succès."], 200);
        } else {
            response(["message" => "Échec de la mise à jour du menu."], 500);
        }
    } else {
        response(["message" => "L'ID ou les données sont incomplètes."], 400);
    }
}

function handleDeleteFood()
{
    global $connexion;
    $id = isset($_GET['id']) ? $_GET['id'] : die(response(["message" => "L'ID du menu est requis."], 400));
    $stmt = $connexion->prepare("DELETE FROM foods WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        response(["message" => "Menu supprimé avec succès."], 200);
    } else {
        response(["message" => "Échec de la suppression du menu."], 500);
    }
}

function response($data, $status)
{
    http_response_code($status);
    echo json_encode($data);
}