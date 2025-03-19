<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Exécution de la requête SQL</h2>";

    // Récupérer l'ID de And1
    $stmt = $bdd->prepare("SELECT ID_Utilisateur FROM UTILISATEURS WHERE Email = 'and1@gmail.com'");
    $stmt->execute();
    $conseiller = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($conseiller) {
        // Affecter 3 clients au conseiller
        $stmt = $bdd->prepare("
            UPDATE UTILISATEURS 
            SET ID_Conseiller = :conseillerId
            WHERE Role = 'client' 
            AND (ID_Conseiller IS NULL OR ID_Conseiller != :conseillerId)
            LIMIT 3
        ");
        $stmt->execute(['conseillerId' => $conseiller['ID_Utilisateur']]);
        
        $nombreClientsAffectes = $stmt->rowCount();
        echo "<p>Nombre de clients affectés : " . $nombreClientsAffectes . "</p>";

        // Afficher les clients affectés
        $stmt = $bdd->prepare("
            SELECT Nom, Prenom, Email 
            FROM UTILISATEURS 
            WHERE ID_Conseiller = :conseillerId
            ORDER BY Nom, Prenom
        ");
        $stmt->execute(['conseillerId' => $conseiller['ID_Utilisateur']]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3 style='color: #6f42c1;'>Clients affectés :</h3>";
        echo "<ul>";
        foreach ($clients as $client) {
            echo "<li><strong>" . htmlspecialchars($client['Prenom']) . " " . 
                 htmlspecialchars($client['Nom']) . "</strong> (" . 
                 htmlspecialchars($client['Email']) . ")</li>";
        }
        echo "</ul>";

        echo "<p style='color: green;'>✓ Requête exécutée avec succès</p>";
        echo "<p><a href='index.php?page=mes_clients' class='btn' style='background-color: #6f42c1; color: white;'>Voir mes clients</a></p>";
    } else {
        echo "<p style='color: red;'>Erreur : Conseiller And1 non trouvé</p>";
    }

    echo "</div>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
