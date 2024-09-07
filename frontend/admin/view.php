<h1>Détails du Menu</h1> <!-- Titre principal de la page affichant les détails du menu -->
<div id="menu-details"></div> <!-- Conteneur où les détails du menu seront affichés dynamiquement -->

<script>
    // Appel à l'API pour récupérer les détails d'un menu spécifique
    fetch(`http://localhost:8090/api/api.php?endpoint=foods&id=${<?php echo $id; ?>}`)
        .then(response => response.json()) // Conversion de la réponse en JSON
        .then(data => {
            // Insertion des détails du menu dans le conteneur 'menu-details'
            document.getElementById('menu-details').innerHTML = `
                <p>Nom: ${data.name}</p> <!-- Affichage du nom du menu -->
                <p>Description: ${data.description}</p> <!-- Affichage de la description du menu -->
                <p>Prix: ${data.price}</p> <!-- Affichage du prix du menu -->
                <img src="${data.image_url}" alt="Image du menu"> <!-- Affichage de l'image du menu -->
            `;
        })
        .catch(error => {
            // Gérer les erreurs éventuelles lors de l'appel API
            console.error('Erreur lors de la récupération des données du menu:', error);
        });
</script>
