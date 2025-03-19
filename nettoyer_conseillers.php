<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Nettoyage des conseillers</h2>";

    // 1. D'abord, on retire le rôle conseiller de tous les autres utilisateurs
    $stmt = $bdd->prepare("
        UPDATE UTILISATEURS 
        SET Role = 'client'
        WHERE Email != 'and1@gmail.com' 
        AND Role = 'conseiller'
    ");
    $stmt->execute();
    
    $nombreConseillersSupprimés = $stmt->rowCount();
    echo "<p>Nombre de conseillers supprimés : " . $nombreConseillersSupprimés . "</p>";

    // 2. On s'assure que And1 est bien conseiller
    $stmt = $bdd->prepare("
        UPDATE UTILISATEURS 
        SET Role = 'conseiller'
        WHERE Email = 'and1@gmail.com'
    ");
    $stmt->execute();

    // 3. Vérification finale
    $stmt = $bdd->prepare("
        SELECT Email, Role, Nom, Prenom 
        FROM UTILISATEURS 
        WHERE Role = 'conseiller'
    ");
    $stmt->execute();
    $conseillers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3 style='color: #6f42c1;'>Liste des conseillers restants :</h3>";
    echo "<ul>";
    foreach ($conseillers as $conseiller) {
        echo "<li><strong>" . htmlspecialchars($conseiller['Prenom']) . " " . 
             htmlspecialchars($conseiller['Nom']) . "</strong> (" . 
             htmlspecialchars($conseiller['Email']) . ")</li>";
    }
    echo "</ul>";

    echo "<p style='color: green;'>✓ Nettoyage terminé avec succès</p>";
    echo "<p><a href='index.php' style='color: #6f42c1;'>Retour à l'accueil</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
