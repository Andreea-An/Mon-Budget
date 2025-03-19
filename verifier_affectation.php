<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Vérification des clients affectés</h2>";

    // Récupérer les informations de And1
    $stmt = $bdd->prepare("
        SELECT ID_Utilisateur, Nom, Prenom 
        FROM UTILISATEURS 
        WHERE Email = 'and1@gmail.com'
    ");
    $stmt->execute();
    $conseiller = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($conseiller) {
        // Compter le nombre total de clients affectés
        $stmt = $bdd->prepare("
            SELECT COUNT(*) as total 
            FROM UTILISATEURS 
            WHERE ID_Conseiller = :conseillerId
        ");
        $stmt->execute(['conseillerId' => $conseiller['ID_Utilisateur']]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo "<p><strong>Conseiller :</strong> " . htmlspecialchars($conseiller['Prenom']) . " " . htmlspecialchars($conseiller['Nom']) . "</p>";
        echo "<p><strong>Nombre total de clients affectés :</strong> " . $total . "</p>";

        // Lister tous les clients affectés
        $stmt = $bdd->prepare("
            SELECT Nom, Prenom, Email 
            FROM UTILISATEURS 
            WHERE ID_Conseiller = :conseillerId 
            ORDER BY Nom, Prenom
        ");
        $stmt->execute(['conseillerId' => $conseiller['ID_Utilisateur']]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($clients) > 0) {
            echo "<h3 style='color: #6f42c1;'>Liste des clients affectés :</h3>";
            echo "<ul>";
            foreach ($clients as $client) {
                echo "<li><strong>" . htmlspecialchars($client['Prenom']) . " " . 
                     htmlspecialchars($client['Nom']) . "</strong> (" . 
                     htmlspecialchars($client['Email']) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>Aucun client n'est affecté à ce conseiller.</p>";
        }

        echo "<div style='margin-top: 20px;'>";
        echo "<a href='affecter_tous_clients.php' class='btn' style='background-color: #6f42c1; color: white; text-decoration: none; padding: 10px 20px; margin-right: 10px; border-radius: 5px;'>Affecter tous les clients</a>";
        echo "<a href='index.php?page=mes_clients' class='btn' style='background-color: #6f42c1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;'>Voir mes clients</a>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>Erreur : Conseiller And1 non trouvé</p>";
    }

    echo "</div>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
