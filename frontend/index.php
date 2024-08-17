<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Livraison de Mets Locaux</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#" id="loginLink">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Menus Disponibles</h1>
        <div id="menus"></div>
        <div id="pagination"></div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginLink = document.getElementById('loginLink');
            const menusContainer = document.getElementById('menus');
            const paginationContainer = document.getElementById('pagination');
            let currentPage = 1;
            const itemsPerPage = 10;

            function fetchMenus(page) {
                fetch(`http://localhost:8090/api/api.php?endpoint=foods&page=${page}&limit=${itemsPerPage}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Réponse JSON :", data);
                        if (data.data) {
                            displayMenus(data.data);
                            setupPagination(data.total, page);
                        } else {
                            menusContainer.innerHTML = `<p>Erreur de chargement des menus : Les données sont manquantes.</p>`;
                        }
                    })
                    .catch(error => {
                        menusContainer.innerHTML = `<p>Une erreur s'est produite : ${error.message}</p>`;
                    });
            }

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
                        </div>
                    </div>
                `).join('');
            }

            function setupPagination(totalItems, currentPage) {
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                paginationContainer.innerHTML = Array.from({ length: totalPages }, (_, i) => `
                    <button onclick="changePage(${i + 1})" ${i + 1 === currentPage ? 'class="active"' : ''}>
                        ${i + 1}
                    </button>
                `).join('');
            }

            function changePage(page) {
                currentPage = page;
                fetchMenus(page);
            }

            fetchMenus(currentPage);

            loginLink.addEventListener('click', function (event) {
                event.preventDefault();
                fetch('check_session.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.isLoggedIn) {
                            window.location.href = 'admin/index.php';
                        } else {
                            window.location.href = 'login/index.php';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la vérification de session :', error);
                        window.location.href = 'login/index.php';
                    });
            });
        });
    </script>
</body>

</html>
