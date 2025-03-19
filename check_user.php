<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connexion à la base de données
    $bdd = new PDO(
        'mysql:host=localhost;dbname=BUDGET;charset=utf8',
        'root',
        'root',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Vérifier l'utilisateur
    $email = "conseiller@test.com";
    $stmt = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<pre>";
    if ($user) {
        echo "Utilisateur trouvé dans la base de données:\n";
        print_r($user);
        
        // Vérifier le mot de passe
        $mdp = "1234A";
        $verification = password_verify($mdp, $user['Mdp']);
        echo "\nVérification du mot de passe '1234A':\n";
        var_dump($verification);
        
        echo "\nHash stocké:\n";
        echo $user['Mdp'];
    } else {
        echo "Aucun utilisateur trouvé avec l'email: $email";
    }
    echo "</pre>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
