<?php
require_once 'bdd/bdd.php';

try {
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2 style='color: #6f42c1;'>Vérification des dépenses</h2>";

    // 1. Vérifier les clients de And1
    $stmt = $bdd->prepare("
        SELECT ID_Utilisateur, Nom, Prenom, Email 
        FROM UTILISATEURS 
        WHERE ID_Conseiller = (SELECT ID_Utilisateur FROM UTILISATEURS WHERE Email = 'and1@gmail.com')
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Clients de And1:</h3>";
    if (count($clients) > 0) {
        echo "<ul>";
        foreach ($clients as $client) {
            echo "<li>" . htmlspecialchars($client['Prenom'] . ' ' . $client['Nom']) . 
                 " (" . htmlspecialchars($client['Email']) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>Aucun client n'est affecté à And1</p>";
    }

    // 2. Vérifier les dépenses existantes
    $stmt = $bdd->query("
        SELECT 
            u.Nom, u.Prenom, u.Email,
            d.Montant, d.Description, d.Date,
            c.Nom as Categorie
        FROM DEPENSES d
        JOIN UTILISATEURS u ON d.Utilisateur = u.ID_Utilisateur
        LEFT JOIN CATEGORIES c ON d.Categorie = c.ID_Categorie
        ORDER BY d.Date DESC
    ");
    $depenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Toutes les dépenses dans la base de données:</h3>";
    if (count($depenses) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Client</th><th>Email</th><th>Montant</th><th>Description</th><th>Date</th><th>Catégorie</th></tr>";
        foreach ($depenses as $depense) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($depense['Prenom'] . ' ' . $depense['Nom']) . "</td>";
            echo "<td>" . htmlspecialchars($depense['Email']) . "</td>";
            echo "<td>" . number_format($depense['Montant'], 2, ',', ' ') . " €</td>";
            echo "<td>" . htmlspecialchars($depense['Description']) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($depense['Date'])) . "</td>";
            echo "<td>" . htmlspecialchars($depense['Categorie']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Aucune dépense n'existe dans la base de données</p>";
        
        // 3. Créer des dépenses de test si aucune n'existe
        echo "<h3>Création de dépenses de test</h3>";
        
        // Récupérer quelques clients au hasard
        $stmt = $bdd->query("SELECT ID_Utilisateur FROM UTILISATEURS WHERE Role = 'client' LIMIT 3");
        $clients_test = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($clients_test) > 0) {
            // Récupérer une catégorie
            $stmt = $bdd->query("SELECT ID_Categorie FROM CATEGORIES LIMIT 1");
            $categorie = $stmt->fetch(PDO::FETCH_COLUMN);
            
            if ($categorie) {
                $depenses_test = [
                    ['montant' => 150.00, 'description' => 'Courses alimentaires'],
                    ['montant' => 45.50, 'description' => 'Restaurant'],
                    ['montant' => 89.90, 'description' => 'Facture électricité']
                ];
                
                $stmt = $bdd->prepare("
                    INSERT INTO DEPENSES (Utilisateur, Montant, Description, Date, Categorie)
                    VALUES (:utilisateur, :montant, :description, NOW(), :categorie)
                ");
                
                $depenses_ajoutees = 0;
                foreach ($clients_test as $client) {
                    foreach ($depenses_test as $depense) {
                        $stmt->execute([
                            'utilisateur' => $client,
                            'montant' => $depense['montant'],
                            'description' => $depense['description'],
                            'categorie' => $categorie
                        ]);
                        $depenses_ajoutees++;
                    }
                }
                
                echo "<p style='color: green;'>✓ " . $depenses_ajoutees . " dépenses de test ont été créées</p>";
                echo "<p>Actualisez la page pour voir les nouvelles dépenses</p>";
            } else {
                echo "<p style='color: red;'>Erreur : Aucune catégorie trouvée</p>";
            }
        } else {
            echo "<p style='color: red;'>Erreur : Aucun client trouvé pour créer des dépenses de test</p>";
        }
    }

    echo "<div style='margin-top: 20px;'>";
    echo "<a href='index.php' style='color: #6f42c1;'>Retour à l'accueil</a>";
    echo "</div>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
