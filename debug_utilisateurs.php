<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2>Vérification de la base de données</h2>";

    // 1. Vérifier And1
    $stmt = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE Email = 'and1@gmail.com'");
    $stmt->execute();
    $and1 = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3>Information sur And1:</h3>";
    if ($and1) {
        echo "<pre>";
        print_r($and1);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>And1 n'existe pas dans la base de données!</p>";
    }

    // 2. Compter les clients
    $stmt = $bdd->query("
        SELECT Role, COUNT(*) as total 
        FROM UTILISATEURS 
        GROUP BY Role
    ");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Nombre d'utilisateurs par rôle:</h3>";
    echo "<ul>";
    foreach ($counts as $count) {
        echo "<li>" . $count['Role'] . ": " . $count['total'] . "</li>";
    }
    echo "</ul>";

    // 3. Liste tous les utilisateurs
    $stmt = $bdd->query("
        SELECT ID_Utilisateur, Email, Role, ID_Conseiller, Nom, Prenom
        FROM UTILISATEURS 
        ORDER BY Role, Email
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Liste de tous les utilisateurs:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Rôle</th><th>Nom</th><th>Prénom</th><th>ID Conseiller</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['ID_Utilisateur']) . "</td>";
        echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['Role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['Nom']) . "</td>";
        echo "<td>" . htmlspecialchars($user['Prenom']) . "</td>";
        echo "<td>" . ($user['ID_Conseiller'] ? $user['ID_Conseiller'] : 'Non assigné') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "</div>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
