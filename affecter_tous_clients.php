<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Affectation de tous les clients au conseiller And1</h2>";

    // 1. Récupérer l'ID du conseiller And1
    $stmt = $bdd->prepare("SELECT ID_Utilisateur FROM UTILISATEURS WHERE Email = 'and1@gmail.com'");
    $stmt->execute();
    $conseiller = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conseiller) {
        $conseillerId = $conseiller['ID_Utilisateur'];
        
        // 2. Affecter tous les clients au conseiller
        $stmt = $bdd->prepare("
            UPDATE UTILISATEURS 
            SET ID_Conseiller = :conseillerId
            WHERE Role = 'client' 
        ");
        $stmt->execute(['conseillerId' => $conseillerId]);
        
        $nombreClientsAffectes = $stmt->rowCount();
        echo "<p>Nombre de clients affectés au conseiller And1 : " . $nombreClientsAffectes . "</p>";
        
        // 3. Afficher la liste des clients affectés
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
            echo "<li><strong>" . htmlspecialchars($client['Prenom']) . " " . 
                 htmlspecialchars($client['Nom']) . "</strong> (" . 
                 htmlspecialchars($client['Email']) . ")</li>";
        }
        echo "</ul>";
        
        echo "<p style='color: green;'>✓ Tous les clients ont été affectés avec succès</p>";
        echo "<p><a href='index.php?page=mes_clients' class='btn' style='background-color: #6f42c1; color: white;'>Voir mes clients</a></p>";
        
    } else {
        echo "<p style='color: red;'>Erreur : Conseiller And1 non trouvé</p>";
    }
    
    echo "<p><a href='index.php' style='color: #6f42c1;'>Retour à l'accueil</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
