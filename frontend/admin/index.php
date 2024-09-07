<?php
// Démarre la session pour maintenir l'authentification de l'utilisateur
session_start();

// Vérifie si l'utilisateur est connecté, sinon, le redirige vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/index.php');
    exit;
}

// Récupère l'action demandée (ajout, édition, visualisation) et l'ID (pour modification ou visualisation) à partir des paramètres GET
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Menus</title>
    <!-- Définition d'une icône vide pour l'onglet -->
    <link rel="icon" href="data:;base64,=">
    <!-- Inclusion de la feuille de styles CSS pour styliser la page -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- En-tête de la page avec un menu de navigation -->
    <header>
        <nav>
            <ul>
                <!-- Lien vers la page d'accueil -->
                <li><a href="index.php">Accueil</a></li>
                <!-- Lien pour se déconnecter en détruisant la session -->
                <li><a href="../login/destroy_session.php">Se Déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- En-tête principale de la section gestion des menus -->
        <div class="menu-header">
            <h1>Administration des Menus</h1>
            <!-- Lien pour ajouter un nouveau menu -->
            <a href="index.php?action=add" class="btn">Ajouter un Menu</a>
        </div>

        <!-- Condition pour inclure les fichiers de création, visualisation ou édition des menus selon l'action -->
        <?php if ($action == 'add'): ?>
            <!-- Inclusion du fichier pour ajouter un menu -->
            <?php include 'add.php'; ?>
        <?php elseif ($action == 'view' && !empty($id)): ?>
            <!-- Inclusion du fichier pour visualiser un menu si un ID est fourni -->
            <?php include 'view.php'; ?>
        <?php elseif ($action == 'edit' && !empty($id)): ?>
            <!-- Inclusion du fichier pour éditer un menu si un ID est fourni -->
            <?php include 'edit.php'; ?>
        <?php else: ?>
            <!-- Formulaire pour filtrer les menus par nom ou par prix -->
            <div id="filter-form" class="filter-form">
                <label for="filter-name">Nom du Menu:</label>
                <input type="text" id="filter-name" placeholder="Rechercher par nom" class="filter-input">

                <label for="filter-price">Prix Max:</label>
                <input type="number" id="filter-price" placeholder="Rechercher par prix" min="0" class="filter-input">

                <button onclick="applyFilters()" class="filter-btn">Filtrer</button>
                <button onclick="clearFilters()" class="filter-btn">Réinitialiser</button>
            </div>

            <!-- Div pour afficher la liste des menus après récupération ou filtrage -->
            <div id="menu-list"></div>

            <!-- Script JavaScript pour gérer la récupération et l'affichage des menus -->
            <script>
                // Variable globale pour stocker les menus récupérés de l'API
                let menus = [];

                // Fonction pour récupérer les menus depuis l'API
                function fetchMenus() {
                    fetch('http://localhost:8090/api/api.php?endpoint=foods')
                        .then(response => response.json()) // Convertir la réponse en JSON
                        .then(result => {
                            menus = result.data || []; // Stocker les données des menus dans la variable globale
                            displayMenus(menus); // Afficher les menus sur la page
                        })
                        .catch(error => console.error('Erreur lors de la récupération des menus:', error)); // Gérer les erreurs de la requête
                }

                // Fonction pour afficher les menus sur la page
                function displayMenus(filteredMenus) {
                    const menuList = document.getElementById('menu-list'); // Sélectionne l'élément qui contiendra la liste des menus
                    menuList.innerHTML = ''; // Vide la liste actuelle avant l'affichage

                    // Si des menus sont trouvés, les afficher un par un
                    if (filteredMenus.length > 0) {
                        filteredMenus.forEach(menu => {
                            // Crée un élément HTML pour chaque menu avec ses détails et ses actions (voir, éditer, supprimer)
                            menuList.innerHTML += `
                                <div class="menu-item">
                                    <div class="menu-details">
                                        <h3>${menu.name}</h3>
                                        <p>Prix: ${menu.price} XOF</p>
                                    </div>
                                    <div class="menu-actions">
                                        <a href="index.php?action=view&id=${menu.id}" class="btn">Voir</a>
                                        <a href="index.php?action=edit&id=${menu.id}" class="btn">Éditer</a>
                                        <button class="btn danger" onclick="deleteMenu(${menu.id})">Supprimer</button>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        // Si aucun menu n'est trouvé, afficher un message d'absence de menus
                        menuList.innerHTML = "<p>Aucun menu disponible.</p>";
                    }
                }

                // Fonction pour appliquer les filtres de recherche par nom et prix
                function applyFilters() {
                    const filterName = document.getElementById('filter-name').value.toLowerCase(); // Récupère la valeur du filtre par nom
                    const filterPrice = document.getElementById('filter-price').value; // Récupère la valeur du filtre par prix

                    // Filtre les menus en fonction du nom et du prix
                    const filteredMenus = menus.filter(menu => {
                        const matchesName = menu.name.toLowerCase().includes(filterName); // Vérifie si le nom correspond
                        const matchesPrice = filterPrice === '' || parseFloat(menu.price) <= parseFloat(filterPrice); // Vérifie si le prix correspond
                        return matchesName && matchesPrice; // Retourne seulement les menus correspondant aux filtres
                    });

                    displayMenus(filteredMenus); // Affiche les menus filtrés
                }

                // Fonction pour réinitialiser les filtres et afficher tous les menus
                function clearFilters() {
                    document.getElementById('filter-name').value = ''; // Réinitialise le champ de filtre par nom
                    document.getElementById('filter-price').value = ''; // Réinitialise le champ de filtre par prix
                    displayMenus(menus); // Affiche tous les menus sans filtres
                }

                // Fonction pour supprimer un menu
                function deleteMenu(id) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce menu ?')) { // Demande de confirmation avant la suppression
                        fetch(`http://localhost:8090/api/api.php?endpoint=foods&id=${id}`, {
                            method: 'DELETE' // Méthode HTTP DELETE pour supprimer le menu
                        })
                            .then(response => response.json()) // Convertir la réponse en JSON
                            .then(data => {
                                if (data.status === 200) { // Si la suppression est réussie
                                    alert('Menu supprimé avec succès.'); // Affiche un message de succès
                                    fetchMenus(); // Recharge la liste des menus après suppression
                                } else {
                                    alert('Erreur lors de la suppression : ' + data.message); // Affiche un message d'erreur en cas d'échec
                                }
                            });
                    }
                }

                // Appel initial pour récupérer et afficher les menus lors du chargement de la page
                fetchMenus();
            </script>
        <?php endif; ?>
    </main>
</body>

</html>
