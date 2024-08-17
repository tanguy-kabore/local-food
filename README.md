# Local Food - API Documentation

## Introduction

Ce projet est une API RESTful pour la gestion des menus d'une application de livraison de mets locaux burkinabè. Ce tutoriel vous guidera à travers le processus de mise en place et d'utilisation de l'API, en détaillant chaque fichier et sa fonction.

## Structure du Projet

Le projet est structuré de la manière suivante :
```
local-food/
├── api/
    ├── config.php
    ├── migrate.php
    ├── sql/
    │   └── database.sql
    └── api.php

```

## API - Installation et Configuration

### 1. Préparation de la Base de Données

**Fichier :** `api/sql/database.sql`

Ce fichier contient les instructions SQL pour créer la base de données et les tables nécessaires :

- **Création de la base de données** : Assure que la base de données spécifiée est créée si elle n'existe pas.
- **Création des tables** :
  - `foods` : Stocke les informations sur les menus (ID, nom, description, prix, URL de l'image).
  - `users` : Gère les utilisateurs avec un nom d'utilisateur et un mot de passe crypté.

### Code SQL

```sql
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS {{databaseName}};

-- Utilisation de la base de données
USE {{databaseName}};

-- Création de la table "foods"
CREATE TABLE IF NOT EXISTS foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);

-- Création de la table "users"
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```

### Explication du Code

1. **Création de la Base de Données**

   ```sql
   CREATE DATABASE IF NOT EXISTS {{databaseName}};
   ```
   - Cette instruction crée une base de données si elle n'existe pas déjà.
   - Remplacez `{{databaseName}}` par le nom de votre base de données souhaité.

2. **Utilisation de la Base de Données**

   ```sql
   USE {{databaseName}};
   ```
   - Cette commande sélectionne la base de données que vous allez utiliser pour les opérations suivantes.

3. **Création de la Table `foods`**

   ```sql
   CREATE TABLE IF NOT EXISTS foods (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       description TEXT,
       price DECIMAL(10, 2) NOT NULL,
       image_url VARCHAR(255)
   );
   ```
   - `id` : Identifiant unique pour chaque menu, auto-incrémenté.
   - `name` : Nom du menu (doit être renseigné).
   - `description` : Description du menu (optionnelle).
   - `price` : Prix du menu (doit être renseigné).
   - `image_url` : URL de l'image du menu (optionnelle).

4. **Création de la Table `users`**

   ```sql
   CREATE TABLE IF NOT EXISTS users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL
   );
   ```
   - `id` : Identifiant unique pour chaque utilisateur, auto-incrémenté.
   - `username` : Nom d'utilisateur (doit être renseigné et unique).
   - `password` : Mot de passe de l'utilisateur (doit être renseigné).

### 2. Configuration de la Base de Données

**Fichier :** `api/config.php`

Ce fichier configure la connexion à la base de données MySQL :

- Définit les paramètres de connexion (hôte, utilisateur, mot de passe, nom de la base de données).
- Vérifie l'existence de la base de données et la crée si nécessaire.
- Établit la connexion à la base de données.



### Code PHP

```php
<?php

// Paramètres de connexion à la base de données
$host = 'localhost';
$utilisateur = 'root';
$motDePasse = 'root';
$nomBaseDeDonnees = 'localFoods';

// Chaîne de connexion PDO pour la connexion initiale (sans sélection de base de données)
$dsn = "mysql:host=$host;charset=utf8mb4";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Tentative de connexion à MySQL sans sélection de base de données
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);

    // Vérifier si la base de données existe
    $query = $connexion->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$nomBaseDeDonnees'");
    $databaseExists = ($query->rowCount() > 0);

    // Si la base de données n'existe pas, la créer
    if (!$databaseExists) {
        $connexion->exec("CREATE DATABASE $nomBaseDeDonnees");
    }

    // Sélection de la base de données
    $dsn = "mysql:host=$host;dbname=$nomBaseDeDonnees;charset=utf8mb4";
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);

} catch (PDOException $e) {
    // Afficher un message d'échec uniquement pour le débogage, pas dans la réponse API
    error_log("Échec de la connexion à la base de données : " . $e->getMessage());
    die("Échec de la connexion à la base de données.");
}
```

### Explication du Code

1. **Paramètres de Connexion**

   ```php
   $host = 'localhost';
   $utilisateur = 'root';
   $motDePasse = 'root';
   $nomBaseDeDonnees = 'localFoods';
   ```
   - `host` : Adresse du serveur MySQL (ici, `localhost` pour une connexion locale).
   - `utilisateur` : Nom d'utilisateur MySQL (ici, `root`).
   - `motDePasse` : Mot de passe de l'utilisateur MySQL (ici, `root`).
   - `nomBaseDeDonnees` : Nom de la base de données à utiliser (ici, `localFoods`).

2. **Chaîne de Connexion PDO**

   ```php
   $dsn = "mysql:host=$host;charset=utf8mb4";
   ```
   - Chaîne de connexion utilisée pour se connecter au serveur MySQL sans spécifier encore la base de données.

3. **Options PDO**

   ```php
   $options = [
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       PDO::ATTR_EMULATE_PREPARES => false,
   ];
   ```
   - `PDO::ATTR_ERRMODE` : Mode de gestion des erreurs (lancer des exceptions en cas d'erreurs).
   - `PDO::ATTR_DEFAULT_FETCH_MODE` : Mode de récupération des résultats (en tant que tableaux associatifs).
   - `PDO::ATTR_EMULATE_PREPARES` : Désactivation de l'émulation des instructions préparées.

4. **Connexion à MySQL**

   ```php
   $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);
   ```
   - Connexion au serveur MySQL sans spécifier de base de données.

5. **Vérification et Création de la Base de Données**

   ```php
   $query = $connexion->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$nomBaseDeDonnees'");
   $databaseExists = ($query->rowCount() > 0);

   if (!$databaseExists) {
       $connexion->exec("CREATE DATABASE $nomBaseDeDonnees");
   }
   ```
   - Vérifie si la base de données spécifiée existe.
   - Si elle n'existe pas, elle est créée.

6. **Sélection de la Base de Données**

   ```php
   $dsn = "mysql:host=$host;dbname=$nomBaseDeDonnees;charset=utf8mb4";
   $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);
   ```
   - Change la chaîne de connexion pour spécifier la base de données et reconnecte à MySQL.

7. **Gestion des Exceptions**

   ```php
   catch (PDOException $e) {
       error_log("Échec de la connexion à la base de données : " . $e->getMessage());
       die("Échec de la connexion à la base de données.");
   }
   ```
   - Capture les erreurs de connexion et les journalise tout en arrêtant l'exécution du script.

### 3. Migration de la Base de Données

**Fichier :** `api/migrate.php`

Ce script exécute les migrations de la base de données en :

- Lisant le fichier SQL `database.sql`.
- Créant la base de données si elle n'existe pas.
- Exécutant les commandes SQL pour créer les tables.
- Ajoutant un utilisateur administrateur avec des informations de connexion par défaut.

### Code PHP

```php
<?php

// Inclure le fichier de configuration
require_once('config.php');

// Chemin vers le fichier SQL
$sqlFilePath = 'sql/database.sql';

// Lire le contenu du fichier SQL
$sqlContent = file_get_contents($sqlFilePath);

// Remplacer la variable {{databaseName}} par le nom de la base de données
$sqlContent = str_replace('{{databaseName}}', $nomBaseDeDonnees, $sqlContent);

try {
    // Exécuter le script SQL
    $connexion->exec($sqlContent);

    // Afficher un message de réussite en vert
    echo "\033[32mMigration réussie.\033[0m\n";

    // Ajouter un utilisateur administrateur
    $username = 'admin';
    $password = 'password'; // Mot de passe en clair (à ne jamais utiliser en production)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connexion->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();

    echo "\033[32mUtilisateur administrateur ajouté avec succès.\033[0m\n";

} catch (PDOException $e) {
    // Afficher un message d'échec en rouge
    die("\033[31mÉchec de la migration : " . $e->getMessage() . "\033[0m\n");
}
?>
```

### Explication du Code

1. **Inclusion du Fichier de Configuration**

   ```php
   require_once('config.php');
   ```
   - Charge les paramètres de configuration de connexion à la base de données définis dans `config.php`.

2. **Définition du Chemin vers le Fichier SQL**

   ```php
   $sqlFilePath = 'sql/database.sql';
   ```
   - Spécifie le chemin relatif du fichier SQL contenant les commandes pour créer les tables de la base de données.

3. **Lecture du Contenu du Fichier SQL**

   ```php
   $sqlContent = file_get_contents($sqlFilePath);
   ```
   - Lit le contenu du fichier SQL dans une chaîne de caractères.

4. **Remplacement du Nom de la Base de Données**

   ```php
   $sqlContent = str_replace('{{databaseName}}', $nomBaseDeDonnees, $sqlContent);
   ```
   - Remplace le placeholder `{{databaseName}}` dans le script SQL par le nom de la base de données défini dans `config.php`.

5. **Exécution du Script SQL**

   ```php
   $connexion->exec($sqlContent);
   ```
   - Exécute les commandes SQL pour créer les tables et les autres structures de la base de données.

6. **Affichage d'un Message de Réussite**

   ```php
   echo "\033[32mMigration réussie.\033[0m\n";
   ```
   - Affiche un message de succès en vert dans la console si la migration se passe bien.

7. **Ajout d'un Utilisateur Administrateur**

   ```php
   $username = 'admin';
   $password = 'password'; // Mot de passe en clair (à ne jamais utiliser en production)
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
   
   $stmt = $connexion->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
   $stmt->bindParam(':username', $username);
   $stmt->bindParam(':password', $hashedPassword);
   $stmt->execute();
   ```
   - Crée un utilisateur administrateur avec un mot de passe hashé pour des raisons de sécurité.

8. **Affichage d'un Message d'Erreur**

   ```php
   die("\033[31mÉchec de la migration : " . $e->getMessage() . "\033[0m\n");
   ```
   - Affiche un message d'échec en rouge et arrête l'exécution du script si une erreur se produit.

### 4. Gestion des Requêtes API

**Fichier :** `api/api.php`

Ce fichier gère les requêtes API :

- **En-têtes CORS** : Permet les requêtes cross-origin.
- **Méthodes HTTP** :
  - `POST` : Gère les connexions (`login`), les déconnexions (`logout`), et la création de menus (`createFood`).
  - `GET` : Récupère les menus (`getFoods`), avec des options de pagination.
  - `PUT` : Met à jour les menus (`updateFood`).
  - `DELETE` : Supprime les menus (`deleteFood`).
- **Fonctions principales** :
  - `isLoggedIn()` : Vérifie si l'utilisateur est connecté.
  - `handleLogin()` : Authentifie un utilisateur.
  - `handleLogout()` : Déconnecte un utilisateur.
  - `handleCreateFood()` : Ajoute un nouveau menu.
  - `handleGetFoods()` : Récupère les menus avec pagination.
  - `handleUpdateFood()` : Met à jour un menu existant.
  - `handleDeleteFood()` : Supprime un menu.

### Code PHP

```php
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
            handleUpdateFood();
        }
        break;

    case 'DELETE':
        if ($endpoint === 'foods') {
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
?>
```

### Explication du Code

1. **En-têtes CORS**

   ```php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
   header("Access-Control-Allow-Headers: Content-Type");
   header("Content-Type: application/json");
   ```
   - Configure les en-têtes CORS pour permettre les requêtes depuis n'importe quel domaine et spécifie les méthodes HTTP et les en-têtes acceptés.

2. **Chargement de la Configuration**

   ```php
   require_once('config.php');
   ```
   - Inclut le fichier de configuration qui contient les paramètres de connexion à la base de données.

3. **Gestion des Méthodes HTTP**

   ```php
   $method = $_SERVER['REQUEST_METHOD'];
   $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
   ```
   - Récupère la méthode HTTP et le point de terminaison de la requête.

4. **Gestion des Requêtes OPTIONS**

   ```php
   if ($method === 'OPTIONS') {
       response([], 200);
       exit;
   }
   ```
   - Répond aux requêtes OPTIONS (pré-vol CORS) avec un statut 200.

5. **Gestion des Requêtes selon la Méthode HTTP**

   ```php
   switch ($method) {
       case 'POST':
        if ($endpoint === 'login') {
            handleLogin();
        } elseif ($endpoint === 'logout') {
            handleLogout();
        } else {
            handleCreateFood();
        }
        break;
   ```

   - **POST**: Gère la connexion (`login`), la déconnexion (`logout`), ou la création de menu (`createFood`).
   - **GET**: Récupère les menus (`foods`).
   - **PUT**: Met à jour un menu (`foods`).
   - **DELETE**: Supprime un menu (`foods`).

6. **Fonctions de Gestion des Requêtes**

   - **handleLogin()**: Authentifie un utilisateur en vérifiant le nom d'utilisateur et le mot de passe.
   - **handleLogout()**: Répond avec un message de déconnexion.
   - **handleCreateFood()**: Ajoute un nouveau menu à la base de données.
   - **handleGetFoods()**: Récupère les menus, avec pagination et possibilité de filtrer par ID.
   - **handleUpdateFood()**: Met à jour un menu existant.
   - **handleDeleteFood()**: Supprime un menu par son ID.

7. **Fonction de Réponse**

   ```php
   function response($data, $status)
   {
       http_response_code($status);
       echo json_encode($data);
   }
   ```
   - Envoie une réponse JSON avec un code de statut HTTP.


### Utilisation

1. **Configuration** : Mettez à jour les paramètres de connexion dans `config.php` si nécessaire.
2. **Migration** : Exécutez `migrate.php` pour créer la base de données et ajouter l'utilisateur administrateur.
3. **API** : Utilisez les points de terminaison définis dans `api.php` pour interagir avec l'API. Les méthodes HTTP définissent les opérations possibles (CRUD).

### Exemple de Requêtes

- **Connexion** : `POST /api/api.php?endpoint=login`
  ```json
  {
    "username": "admin",
    "password": "password"
  }
  ```

- **Ajout de Menu** : `POST /api/api.php`
  ```json
  {
    "name": "Plat Local",
    "description": "Délicieux plat traditionnel.",
    "price": 12.50,
    "image_url": "http://example.com/image.jpg"
  }
  ```

- **Récupération des Menus** : `GET /api/api.php?endpoint=foods&page=1&limit=10`

- **Mise à Jour de Menu** : `PUT /api/api.php?endpoint=foods&id=1`
  ```json
  {
    "name": "Plat Local Modifié",
    "description": "Description mise à jour.",
    "price": 15.00,
    "image_url": "http://example.com/new-image.jpg"
  }
  ```

- **Suppression de Menu** : `DELETE /api/api.php?endpoint=foods&id=1`