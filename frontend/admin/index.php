<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Admin - Livraison de Mets Locaux</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="index.php">Admin</a></li>
                <li><a href="../login/destroy_session.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h1>Espace Admin</h1>
        <!-- Formulaire d'ajout de menu -->
        <h2>Ajouter un Menu</h2>
        <form id="createFoodForm">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" required>
            <label for="description">Description</label>
            <textarea id="description" name="description"></textarea>
            <label for="price">Prix</label>
            <input type="text" id="price" name="price" required>
            <label for="image_url">URL de l'image</label>
            <input type="text" id="image_url" name="image_url">
            <button type="submit">Ajouter</button>
        </form>

        <!-- Formulaires pour mettre à jour et supprimer les menus (à développer) -->
    </main>

    <script>
        document.getElementById('createFoodForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du formulaire

            // Récupération des valeurs du formulaire
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const price = document.getElementById('price').value;
            const image_url = document.getElementById('image_url').value;

            // Envoi des données au serveur
            fetch('http://localhost:8090/api/api.php?endpoint=foods', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, description, price, image_url })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 201) {
                    alert("Menu ajouté avec succès.");
                    // Réinitialiser le formulaire
                    document.getElementById('createFoodForm').reset();
                } else {
                    alert("Échec de l'ajout du menu : " + data.message);
                }
            })
            .catch(error => {
                alert("Une erreur s'est produite : " + error.message);
            });
        });
    </script>
</body>
</html>
