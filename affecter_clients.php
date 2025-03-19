<?php
require_once 'bdd/bdd.php';

try {
    // Récupérer l'ID du conseiller And1
    $stmt = $bdd->prepare("SELECT ID_Utilisateur FROM UTILISATEURS WHERE Email = 'and1@gmail.com'");
    $stmt->execute();
    $conseiller = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conseiller) {
        $conseillerId = $conseiller['ID_Utilisateur'];
        
        // Liste des emails des clients à affecter
        $clientsEmails = ['and@gmail.com', 'emma@gmail.com', 'sophie@gmail.com'];
        
        // Affecter les clients spécifiques au conseiller
        $stmt = $bdd->prepare("
            UPDATE UTILISATEURS 
            SET ID_Conseiller = :conseillerId
            WHERE Email IN (:email1, :email2, :email3)
        ");
        $stmt->execute([
            'conseillerId' => $conseillerId,
            'email1' => $clientsEmails[0],
            'email2' => $clientsEmails[1],
            'email3' => $clientsEmails[2]
        ]);
        
        $nombreClientsAffectes = $stmt->rowCount();
        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2 style='color: #6f42c1;'>Affectation des clients</h2>";
        echo "<p>Nombre de clients affectés au conseiller And1 : " . $nombreClientsAffectes . "</p>";
        
        // Afficher les clients affectés
        $stmt = $bdd->prepare("
            SELECT Nom, Prenom, Email 
            FROM UTILISATEURS 
            WHERE ID_Conseiller = :conseillerId
            ORDER BY Nom, Prenom
        ");
        $stmt->execute(['conseillerId' => $conseillerId]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3 style='color: #6f42c1;'>Liste des clients affectés :</h3>";
        echo "<ul>";
        foreach ($clients as $client) {
            echo "<li><strong>" . htmlspecialchars($client['Prenom']) . " " . htmlspecialchars($client['Nom']) . "</strong> (" . htmlspecialchars($client['Email']) . ")</li>";
        }
        echo "</ul>";
        echo "<p><a href='index.php' style='color: #6f42c1;'>Retour à l'accueil</a></p>";
        echo "</div>";
        
    } else {
        echo "Conseiller And1 non trouvé";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
