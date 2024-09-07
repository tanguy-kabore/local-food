<!-- Formulaire pour éditer un menu -->
<form id="editFoodForm">
    <h1>Éditer le Menu</h1>

    <!-- Champ pour saisir le nom du menu -->
    <label for="name">Nom</label>
    <input type="text" id="name" name="name" required> <!-- Champ requis -->

    <!-- Champ pour saisir la description du menu -->
    <label for="description">Description</label>
    <textarea id="description" name="description"></textarea> <!-- Champ facultatif -->

    <!-- Champ pour saisir le prix du menu -->
    <label for="price">Prix</label>
    <input type="text" id="price" name="price" required> <!-- Champ requis -->

    <!-- Champ pour saisir l'URL de l'image du menu -->
    <label for="image_url">URL de l'image</label>
    <input type="text" id="image_url" name="image_url"> <!-- Champ facultatif -->

    <!-- Bouton pour soumettre le formulaire et modifier les informations -->
    <button type="submit">Modifier</button>
</form>

<script>
    // Récupération de l'ID du menu à éditer (variable PHP passée dans le script)
    const id = <?php echo $id; ?>;

    // Requête GET pour récupérer les détails du menu depuis l'API
    fetch(`http://localhost:8090/api/api.php?endpoint=foods&id=${id}`)
        .then(response => response.json()) // Conversion de la réponse en JSON
        .then(data => {
            // Remplissage des champs du formulaire avec les données récupérées
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('price').value = data.price;
            document.getElementById('image_url').value = data.image_url;
        });

    // Ajout d'un écouteur d'événement pour la soumission du formulaire
    document.getElementById('editFoodForm').addEventListener('submit', function (event) {
        // Empêcher le rechargement de la page lors de la soumission
        event.preventDefault();

        // Récupération des valeurs mises à jour dans le formulaire
        const name = document.getElementById('name').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const image_url = document.getElementById('image_url').value;

        // Requête PUT pour mettre à jour les informations du menu
        fetch(`http://localhost:8090/api/api.php?endpoint=foods&id=${id}`, {
            method: 'PUT', // Méthode HTTP pour la mise à jour
            headers: { 'Content-Type': 'application/json' }, // Spécifie que les données sont au format JSON
            body: JSON.stringify({ name, description, price, image_url }) // Corps de la requête avec les données du formulaire
        })
        .then(response => response.json()) // Conversion de la réponse en JSON
        .then(data => {
            // Vérification si la mise à jour a été réussie
            if (data.status === 200) {
                // Affichage d'un message de succès et redirection vers la page d'accueil
                alert("Menu mis à jour avec succès.");
                window.location.href = 'index.php';
            } else {
                // Affichage d'un message d'erreur en cas de problème
                alert("Échec de la mise à jour : " + data.message);
            }
        });
    });
</script>