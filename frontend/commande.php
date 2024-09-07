<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande - Menu</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles pour le corps de la page */
        body {
            font-family: Arial, sans-serif; /* Police par défaut */
            margin: 0; /* Suppression des marges par défaut */
            padding: 0; /* Suppression des espacements par défaut */
            background-color: #f4f4f4; /* Couleur de fond gris clair */
        }
        
        /* Styles pour la section principale */
        main {
            max-width: 800px; /* Largeur maximale du conteneur principal */
            margin: 2rem auto; /* Centrage horizontal et espace vertical */
            padding: 1rem; /* Espacement intérieur */
            background: #fff; /* Fond blanc pour le conteneur */
            border-radius: 8px; /* Coins arrondis */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Ombre légère autour du conteneur */
        }
        
        /* Styles pour les titres principaux */
        h1 {
            color: #333; /* Couleur du texte du titre */
        }
        
        /* Styles pour les détails du menu */
        .menu-details {
            margin-bottom: 1rem; /* Espacement sous le bloc de détails */
        }
        
        .menu-details p {
            margin: 0.5rem 0; /* Espacement entre les paragraphes */
        }
        
        /* Styles pour le conteneur de quantité */
        .quantity-container {
            display: flex; /* Utilisation de flexbox pour l'alignement */
            align-items: center; /* Alignement vertical des éléments */
            margin-bottom: 1rem; /* Espacement sous le conteneur */
        }
        
        /* Styles pour les labels */
        label {
            font-weight: bold; /* Texte en gras */
            margin-right: 10px; /* Espacement à droite du label */
        }
        
        /* Styles pour les champs de saisie numérique */
        input[type="number"] {
            width: 100px; /* Largeur fixe du champ */
            padding: 0.5rem; /* Espacement intérieur */
            margin-right: 0.5rem; /* Espacement à droite du champ */
        }
        
        /* Styles pour les boutons */
        .btn {
            display: inline-block; /* Affichage en bloc en ligne */
            padding: 0.75rem 1.5rem; /* Espacement intérieur */
            color: #fff; /* Couleur du texte du bouton */
            background-color: #28a745; /* Couleur de fond verte */
            border: none; /* Suppression de la bordure par défaut */
            border-radius: 4px; /* Coins arrondis du bouton */
            text-align: center; /* Alignement du texte */
            cursor: pointer; /* Curseur de main pour le bouton */
            text-decoration: none; /* Suppression de la décoration du texte */
        }
        
        /* Effet au survol du bouton */
        .btn:hover {
            background-color: #218838; /* Changement de couleur lors du survol */
        }
        
        /* Styles pour les actions du menu */
        .menu-actions {
            display: flex; /* Utilisation de flexbox pour l'alignement */
            justify-content: flex-end; /* Alignement des éléments à droite */
        }
    </style>
</head>

<body>
    <main>
        <h1 id="menu-name">Commande - Menu</h1>

        <div class="menu-details">
            <!-- Détails du menu -->
            <p><strong>Description:</strong> <span id="menu-description">Chargement...</span></p>
            <p><strong>Prix unitaire:</strong> <span id="menu-price">Chargement...</span> XOF</p>

            <div class="quantity-container">
                <!-- Sélecteur de quantité -->
                <label for="quantity">Quantité:</label>
                <input type="number" id="quantity" value="1" min="1">
            </div>

            <p id="total-price">Prix total: 0 XOF</p>

            <div class="menu-actions">
                <!-- Bouton de paiement -->
                <button id="pay-btn" class="btn">Payer</button>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Récupération de l'ID du menu depuis les paramètres d'URL
            const menuId = new URLSearchParams(window.location.search).get('id');
            const pricePerUnitElement = document.getElementById('menu-price');
            const nameElement = document.getElementById('menu-name');
            const descriptionElement = document.getElementById('menu-description');
            const totalPriceElement = document.getElementById('total-price');
            const quantityInput = document.getElementById('quantity');

            if (!menuId) {
                alert('ID du menu manquant.');
                return;
            }

            // Récupération des détails du menu depuis l'API
            fetch(`http://localhost:8090/api/api.php?endpoint=foods&id=${menuId}`)
                .then(response => response.json())
                .then(menu => {
                    if (menu.name) {
                        // Mise à jour des détails du menu sur la page
                        nameElement.textContent = `Commande - ${menu.name}`;
                        descriptionElement.textContent = menu.description || 'Pas de description disponible.';
                        pricePerUnitElement.textContent = menu.price || 'Pas de prix disponible';
                        updateTotalPrice();
                    } else {
                        alert('Menu non trouvé.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des détails du menu :', error);
                    alert('Erreur lors de la récupération des détails du menu.');
                });

            // Mise à jour du prix total en fonction de la quantité
            quantityInput.addEventListener('input', updateTotalPrice);

            function updateTotalPrice() {
                const quantity = parseInt(quantityInput.value, 10) || 1;
                const pricePerUnit = parseFloat(pricePerUnitElement.textContent) || 0;
                const totalPrice = quantity * pricePerUnit;
                totalPriceElement.textContent = `Prix total: ${totalPrice} XOF`;
            }

            // Gestion du clic sur le bouton de paiement
            document.getElementById('pay-btn').addEventListener('click', function () {
                alert('Paiement en cours...'); // Simuler le paiement
            });
        });
    </script>
</body>

</html>