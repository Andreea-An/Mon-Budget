<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Vérification des clients</h2>";

    // 1. Vérifier les utilisateurs et leurs rôles
    $stmt = $bdd->query("
        SELECT Email, Role, ID_Conseiller,
        CASE 
            WHEN Role = 'client' THEN 'Client'
            WHEN Role = 'conseiller' THEN 'Conseiller'
            ELSE Role 
        END as TypeUtilisateur
        FROM UTILISATEURS
        ORDER BY Role, Email
    ");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3 style='color: #6f42c1;'>Liste des utilisateurs :</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Email</th><th>Type</th><th>ID Conseiller</th></tr>";
    
    foreach ($utilisateurs as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['TypeUtilisateur']) . "</td>";
        echo "<td>" . ($user['ID_Conseiller'] ? $user['ID_Conseiller'] : 'Non assigné') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 2. Afficher le conseiller And1
    $stmt = $bdd->prepare("
        SELECT ID_Utilisateur, Email, Role 
        FROM UTILISATEURS 
        WHERE Email = 'and1@gmail.com'
    ");
    $stmt->execute();
    $and1 = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3 style='color: #6f42c1;'>Informations sur And1 :</h3>";
    if ($and1) {
        echo "<p>ID: " . $and1['ID_Utilisateur'] . "</p>";
        echo "<p>Email: " . htmlspecialchars($and1['Email']) . "</p>";
        echo "<p>Rôle: " . htmlspecialchars($and1['Role']) . "</p>";
    } else {
        echo "<p style='color: red;'>And1 non trouvé dans la base de données!</p>";
    }

    echo "<p><a href='index.php' style='color: #6f42c1;'>Retour à l'accueil</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
