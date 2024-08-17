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