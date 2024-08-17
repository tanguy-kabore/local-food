<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Livraison de Mets Locaux</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="index.php">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Connexion</h1>
        <form id="loginForm">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
        <p id="message"></p>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch('http://localhost:8090/api/api.php?endpoint=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Réponse API:', data); // Affiche la réponse API dans la console
                    if (data.status === 200) {
                        // Envoyer l'ID utilisateur à un script pour créer la session
                        fetch('http://localhost:8085/local-food/frontend/login/create_session.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ user_id: data.user_id })
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Erreur HTTP ' + response.status);
                                }
                                return response.json();
                            })
                            .then(sessionData => {
                                if (sessionData.status === 200) {
                                    window.location.href = '../admin/index.php'; // Redirige vers l'espace admin
                                } else {
                                    document.getElementById('message').textContent = sessionData.message;
                                }
                            })
                            .catch(error => {
                                document.getElementById('message').textContent = "Une erreur s'est produite lors de la création de la session.";
                                console.error('Erreur:', error);
                            });
                    } else {
                        document.getElementById('message').textContent = data.message;
                    }
                })
                .catch(error => {
                    document.getElementById('message').textContent = "Une erreur s'est produite. Veuillez réessayer.";
                    console.error('Erreur:', error);
                });
        });
    </script>
</body>

</html>
