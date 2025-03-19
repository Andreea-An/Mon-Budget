<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'bdd/bdd.php';
require_once 'model/utilisateurModel.php';

echo "<pre>";

// Vérifier la connexion à la base de données
echo "État de la connexion à la base de données :\n";
var_dump($bdd);

// Créer une instance de la classe Utilisateur
$utilisateur = new Utilisateur($bdd);

// Données du conseiller
$nom = "Conseiller";
$prenom = "Test";
$email = "conseiller@test.com";
$motDePasse = "1234A";

// Vérifier si l'email existe déjà
$stmt = $bdd->prepare("SELECT COUNT(*) FROM UTILISATEURS WHERE Email = ?");
$stmt->execute([$email]);
$count = $stmt->fetchColumn();

echo "\nVérification de l'email existant :\n";
var_dump($count);

// Créer le compte conseiller
$result = $utilisateur->inscriptionConseiller($nom, $prenom, $email, $motDePasse);
echo "\nRésultat de l'inscription :\n";
var_dump($result);

if ($result) {
    echo "\nCompte créé, vérification des données :\n";
    
    // Vérifier que le compte a bien été créé
    $stmt = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    var_dump($user);

    if ($user) {
        // Tester la connexion directement
        echo "\nTest de connexion avec les identifiants :\n";
        $testConnexion = $utilisateur->connexion($email, $motDePasse);
        var_dump($testConnexion);
    }
} else {
    echo "Erreur lors de la création du compte conseiller\n";
    
    // Vérifier les erreurs PDO
    echo "\nDernière erreur PDO :\n";
    var_dump($bdd->errorInfo());
}

echo "</pre>";
?>
