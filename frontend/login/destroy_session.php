<?php
session_start();  // Démarre la session pour accéder aux variables de session existantes

// Supprime toutes les variables de session
session_unset();  

// Détruit la session actuelle pour s'assurer que toutes les informations de session sont supprimées
session_destroy(); 

// Redirige l'utilisateur vers la page de connexion après la déconnexion
header('Location: ../login/index.php'); 

exit;  // Arrête l'exécution du script pour s'assurer que la redirection est immédiate
?>
