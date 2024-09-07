<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Livraison de Mets Locaux</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers le fichier CSS pour le style global -->
    <link rel="icon" href="favicon.ico" type="image/x-icon"> <!-- Icône de la page -->
</head>

<body>
    <header>
        <nav>
            <ul>
                <!-- Menu de navigation -->
                <li><a href="index.php">Accueil</a></li> <!-- Lien vers la page d'accueil -->
                <li><a href="#" id="loginLink">Connexion</a></li> <!-- Lien pour la connexion -->
            </ul>
        </nav>
    </header>

    <main>
        <h1>Menus Disponibles</h1> <!-- Titre principal de la page -->

        <!-- Formulaire de filtrage des menus -->
        <div id="filter-form" class="filter-form">
            <label for="filter-name">Nom du Menu:</label>
            <input type="text" id="filter-name" placeholder="Rechercher par nom" class="filter-input">

            <label for="filter-price">Prix Max:</label>
            <input type="number" id="filter-price" placeholder="Prix maximum" class="filter-input">

            <button id="filter-button" class="btn filter-btn">Filtrer</button>
            <button id="clear-button" class="btn filter-btn">Réinitialiser</button>
        </div>

        <!-- Conteneur pour afficher les menus -->
        <div id="menus" class="menus-container"></div>
        <!-- Conteneur pour la pagination -->
        <div id="pagination" class="pagination"></div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginLink = document.getElementById('loginLink'); // Lien pour la connexion
            const menusContainer = document.getElementById('menus'); // Conteneur pour les menus
            const paginationContainer = document.getElementById('pagination'); // Conteneur pour la pagination
            const filterButton = document.getElementById('filter-button'); // Bouton de filtrage
            const clearButton = document.getElementById('clear-button'); // Bouton de réinitialisation des filtres
            let currentPage = 1; // Page actuelle pour la pagination
            const itemsPerPage = 10; // Nombre d'éléments par page
            let allMenus = []; // Tableau pour stocker tous les menus récupérés

            // Fonction pour récupérer les menus depuis l'API
            function fetchMenus(page) {
                fetch(`http://localhost:8090/api/api.php?endpoint=foods&page=${page}&limit=${itemsPerPage}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.data && data.data.length > 0) {
                            allMenus = data.data; // Stockage des menus
                            displayMenus(allMenus); // Affichage des menus
                            setupPagination(data.total, page); // Configuration de la pagination
                        } else {
                            menusContainer.innerHTML = `<p>Aucun menu trouvé.</p>`;
                            paginationContainer.innerHTML = '';
                        }
                    })
                    .catch(error => {
                        console.error("Erreur lors du chargement des menus :", error);
                        menusContainer.innerHTML = `<p>Erreur lors du chargement des menus : ${error.message}</p>`;
                    });
            }

            // Fonction pour afficher les menus
            function displayMenus(menus) {
                if (menus.length === 0) {
                    menusContainer.innerHTML = `<p>Aucun menu disponible.</p>`;
                    return;
                }

                menusContainer.innerHTML = menus.map(menu => `
                    <div class="menu-item">
                        <img src="${menu.image_url}" alt="${menu.name}" class="menu-image">
                        <div class="menu-details">
                            <h2>${menu.name}</h2>
                            <p>${menu.description}</p>
                            <p>Prix: ${menu.price} XOF</p>
                            <a href="commande.php?id=${menu.id}" class="btn commander-btn">Commander</a>
                        </div>
                    </div>
                `).join('');
            }

            // Fonction pour appliquer les filtres
            function applyFilters() {
                const filterName = document.getElementById('filter-name').value.toLowerCase();
                const filterPrice = parseFloat(document.getElementById('filter-price').value);

                const filteredMenus = allMenus.filter(menu => {
                    const matchesName = menu.name.toLowerCase().includes(filterName);
                    const matchesPrice = isNaN(filterPrice) || parseFloat(menu.price) <= filterPrice;
                    return matchesName && matchesPrice;
                });

                displayMenus(filteredMenus);
            }

            // Fonction pour réinitialiser les filtres
            function clearFilters() {
                document.getElementById('filter-name').value = '';
                document.getElementById('filter-price').value = '';
                displayMenus(allMenus);
            }

            // Fonction pour configurer la pagination
            function setupPagination(totalItems, currentPage) {
                const totalPages = Math.ceil(totalItems / itemsPerPage);

                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                paginationContainer.innerHTML = Array.from({ length: totalPages }, (_, i) => `
                    <button onclick="changePage(${i + 1})" ${i + 1 === currentPage ? 'class="active"' : ''}>
                        ${i + 1}
                    </button>
                `).join('');
            }

            // Fonction pour changer de page
            function changePage(page) {
                currentPage = page;
                fetchMenus(page);
            }

            // Initialisation de la page en récupérant les menus pour la première page
            fetchMenus(currentPage);

            // Gestion du clic sur le lien de connexion
            loginLink.addEventListener('click', function (event) {
                event.preventDefault();
                fetch('check_session.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.isLoggedIn) {
                            window.location.href = 'admin/index.php'; // Redirection vers la page admin si connecté
                        } else {
                            window.location.href = 'login/index.php'; // Redirection vers la page de connexion si non connecté
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la vérification de session :', error);
                        window.location.href = 'login/index.php'; // Redirection vers la page de connexion en cas d'erreur
                    });
            });

            // Gestion des événements de clic pour les boutons de filtrage et de réinitialisation
            filterButton.addEventListener('click', applyFilters);
            clearButton.addEventListener('click', clearFilters);
        });
    </script>
</body>

</html>
