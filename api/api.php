<?php
// Permet les requêtes depuis n'importe quelle origine
header("Access-Control-Allow-Origin: *");
// Permet les méthodes HTTP spécifiées
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Permet les en-têtes HTTP spécifiés
header("Access-Control-Allow-Headers: Content-Type");
// Définit le type de contenu comme JSON
header("Content-Type: application/json");

// Inclut la configuration nécessaire (connexion à la base de données)
require_once('config.php');

// Obtient la méthode HTTP utilisée pour la requête
$method = $_SERVER['REQUEST_METHOD'];
// Obtient l'endpoint spécifié dans la requête GET
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// Gère les requêtes OPTIONS pour les pré-requêtes CORS
if ($method === 'OPTIONS') {
    response([], 200);
    exit;
}

// Traite les différentes méthodes HTTP
switch ($method) {
    case 'POST':
        // Gère les requêtes POST en fonction de l'endpoint
        if ($endpoint === 'login') {
            handleLogin();
        } elseif ($endpoint === 'logout') {
            handleLogout();
        } else {
            /* Commenté : authentification requise pour créer des menus
            if (isLoggedIn()) {
                handleCreateFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            }
            */
            handleCreateFood();
        }
        break;

    case 'GET':
        // Gère les requêtes GET en fonction de l'endpoint
        if ($endpoint === 'foods') {
            handleGetFoods();
        }
        break;

    case 'PUT':
        // Gère les requêtes PUT en fonction de l'endpoint
        if ($endpoint === 'foods') {
            /* Commenté : authentification requise pour mettre à jour des menus
            if (isLoggedIn()) {
                handleUpdateFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            }
            */
            handleUpdateFood();
        }
        break;

    case 'DELETE':
        // Gère les requêtes DELETE en fonction de l'endpoint
        if ($endpoint === 'foods') {
            /* Commenté : authentification requise pour supprimer des menus
            if (isLoggedIn()) {
                handleDeleteFood();
            } else {
                response(["message" => "Non autorisé."], 403);
            }
            */
            handleDeleteFood();
        }
        break;

    default:
        // Gère les méthodes HTTP non autorisées
        response(["message" => "Méthode non autorisée."], 405);
}

// Vérifie si l'utilisateur est connecté
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Gère la connexion des utilisateurs
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

// Gère la déconnexion des utilisateurs
function handleLogout()
{
    response(["status" => 200, "message" => "Déconnexion réussie."], 200);
}

// Gère la création d'un nouveau menu
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
            response(["status" => 201, "message" => "Menu ajouté avec succès."], 201);
        } else {
            response(["message" => "Échec de l'ajout du menu."], 500);
        }
    } else {
        response(["message" => "Les données sont incomplètes."], 400);
    }
}

// Gère la récupération des menus
function handleGetFoods()
{
    global $connexion;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $nameFilter = isset($_GET['name']) ? $_GET['name'] : '';
    $priceFilter = isset($_GET['price']) ? (float) $_GET['price'] : null;

    if ($id !== null) {
        // Rechercher un menu par ID
        $stmt = $connexion->prepare("SELECT * FROM foods WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $food = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($food) {
            response($food, 200);
        } else {
            response(["message" => "Menu non trouvé."], 404);
        }
    } else {
        // Rechercher tous les menus avec filtres et pagination
        if ($limit <= 0 || $offset < 0) {
            response(["message" => "Paramètres invalides."], 400);
            return;
        }

        try {
            $query = "SELECT COUNT(*) FROM foods WHERE name LIKE :name";
            $params = [':name' => "%$nameFilter%"];

            if ($priceFilter !== null) {
                $query .= " AND price <= :price";
                $params[':price'] = $priceFilter;
            }

            $stmt = $connexion->prepare($query);
            $stmt->execute($params);
            $total = $stmt->fetchColumn();

            $query = "SELECT * FROM foods WHERE name LIKE :name";
            if ($priceFilter !== null) {
                $query .= " AND price <= :price";
            }
            $query .= " LIMIT :limit OFFSET :offset";

            $stmt = $connexion->prepare($query);
            $stmt->bindParam(':name', $params[':name'], PDO::PARAM_STR);
            if ($priceFilter !== null) {
                $stmt->bindParam(':price', $params[':price'], PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            response([
                'total' => $total,
                'data' => $foods
            ], 200);
        } catch (Exception $e) {
            response(["message" => "Une erreur s'est produite : " . $e->getMessage()], 500);
        }
    }
}

// Gère la mise à jour d'un menu
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
            response(["status" => 200, "message" => "Menu mis à jour avec succès."], 200);
        } else {
            response(["message" => "Échec de la mise à jour du menu."], 500);
        }
    } else {
        response(["message" => "L'ID ou les données sont incomplètes."], 400);
    }
}

// Gère la suppression d'un menu
function handleDeleteFood()
{
    global $connexion;
    $id = isset($_GET['id']) ? $_GET['id'] : die(response(["message" => "L'ID du menu est requis."], 400));
    $stmt = $connexion->prepare("DELETE FROM foods WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        response(["status" => 200, "message" => "Menu supprimé avec succès."], 200);
    } else {
        response(["message" => "Échec de la suppression du menu."], 500);
    }
}

// Envoie une réponse JSON au client avec le code de statut HTTP spécifié
function response($data, $status)
{
    http_response_code($status);
    echo json_encode($data);
}