<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Définition de l'encodage des caractères et ajustement de la mise en page pour les appareils mobiles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Livraison de Mets Locaux</title>
    <!-- Lien vers le fichier CSS pour le style de la page -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <!-- Liens vers la page d'accueil et la page de connexion -->
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="index.php">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Connexion</h1>
        <!-- Formulaire de connexion avec champs pour le nom d'utilisateur et le mot de passe -->
        <form id="loginForm">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <!-- Bouton pour soumettre le formulaire -->
            <button type="submit">Se connecter</button>
        </form>

        <!-- Message qui affichera les erreurs ou les succès de la connexion -->
        <p id="message"></p>
    </main>

    <script>
        // Ajouter un gestionnaire d'événements pour intercepter la soumission du formulaire
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche la soumission classique du formulaire

            // Récupérer les valeurs saisies dans les champs du formulaire
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Effectuer une requête POST à l'API pour authentifier l'utilisateur
            fetch('http://localhost:8090/api/api.php?endpoint=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password }) // Envoyer les informations de connexion au format JSON
            })
            .then(response => {
                // Vérifie si la réponse est valide
                if (!response.ok) {
                    throw new Error('Erreur HTTP ' + response.status);
                }
                return response.json(); // Convertir la réponse en JSON
            })
            .then(data => {
                console.log('Réponse API:', data); // Afficher la réponse API dans la console pour débogage
                // Vérifier si la connexion a été un succès
                if (data.status === 200) {
                    // Si oui, créer une session pour l'utilisateur
                    fetch('http://localhost:8085/local-food/frontend/login/create_session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: data.user_id }) // Envoyer l'ID utilisateur pour la session
                    })
                    .then(response => {
                        // Vérifie si la réponse est valide
                        if (!response.ok) {
                            throw new Error('Erreur HTTP ' + response.status);
                        }
                        return response.json(); // Convertir la réponse en JSON
                    })
                    .then(sessionData => {
                        // Vérifier si la session a été créée avec succès
                        if (sessionData.status === 200) {
                            // Rediriger l'utilisateur vers la page d'administration
                            window.location.href = '../admin/index.php';
                        } else {
                            // Afficher le message d'erreur
                            document.getElementById('message').textContent = sessionData.message;
                        }
                    })
                    .catch(error => {
                        // Afficher une erreur si la création de la session échoue
                        document.getElementById('message').textContent = "Une erreur s'est produite lors de la création de la session.";
                        console.error('Erreur:', error);
                    });
                } else {
                    // Si la connexion échoue, afficher le message d'erreur renvoyé par l'API
                    document.getElementById('message').textContent = data.message;
                }
            })
            .catch(error => {
                // Afficher une erreur si la requête échoue
                document.getElementById('message').textContent = "Une erreur s'est produite. Veuillez réessayer.";
                console.error('Erreur:', error);
            });
        });
    </script>
</body>

</html>
