<!-- Formulaire pour ajouter un menu -->
<form id="createFoodForm" class="form-container">
    <h1>Ajouter un Menu</h1>

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

    <!-- Bouton pour soumettre le formulaire -->
    <button type="submit">Ajouter</button>
</form>

<script>
    // Ajout d'un écouteur d'événement pour le formulaire lors de sa soumission
    document.getElementById('createFoodForm').addEventListener('submit', function (event) {
        // Empêcher le rechargement de la page lors de la soumission du formulaire
        event.preventDefault();

        // Récupération des valeurs saisies dans le formulaire
        const name = document.getElementById('name').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const image_url = document.getElementById('image_url').value;

        // Envoi d'une requête POST au serveur pour ajouter le nouveau menu
        fetch('http://localhost:8090/api/api.php?endpoint=foods', {
            method: 'POST', // Type de requête
            headers: { 'Content-Type': 'application/json' }, // En-tête de la requête pour spécifier le format JSON
            body: JSON.stringify({ name, description, price, image_url }) // Corps de la requête contenant les données du formulaire
        })
        .then(response => response.json()) // Conversion de la réponse en JSON
        .then(data => {
            // Vérification si l'ajout du menu a été un succès
            if (data.status === 201) {
                // Affichage d'un message de succès et redirection vers la page d'accueil
                alert("Menu ajouté avec succès.");
                window.location.href = 'index.php';
            } else {
                // Affichage d'un message d'erreur en cas de problème
                alert("Erreur lors de l'ajout : " + data.message);
            }
        });
    });
</script>
