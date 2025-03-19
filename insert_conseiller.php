<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connexion directe à la base de données
    $bdd = new PDO(
        'mysql:host=localhost;dbname=BUDGET;charset=utf8',
        'root',
        'root',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    echo "Connexion à la base de données réussie<br>";

    // Données du conseiller
    $nom = "Conseiller";
    $prenom = "Test";
    $email = "conseiller@test.com";
    $motDePasse = password_hash("1234A", PASSWORD_DEFAULT);
    $role = "conseiller";

    // Vérifier si l'email existe déjà
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM UTILISATEURS WHERE Email = ?");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "L'email existe déjà, suppression de l'ancien compte...<br>";
        $stmt = $bdd->prepare("DELETE FROM UTILISATEURS WHERE Email = ?");
        $stmt->execute([$email]);
    }

    // Insérer le nouvel utilisateur
    $stmt = $bdd->prepare("INSERT INTO UTILISATEURS (Nom, Prenom, Email, Mdp, Role) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$nom, $prenom, $email, $motDePasse, $role]);

    if ($success) {
        echo "Compte conseiller créé avec succès !<br>";
        echo "Email: conseiller@test.com<br>";
        echo "Mot de passe: 1234A<br>";

        // Vérifier les données insérées
        $stmt = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "Erreur lors de la création du compte<br>";
        print_r($stmt->errorInfo());
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
