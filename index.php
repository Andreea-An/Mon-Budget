<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Inclusion des fonctions d'authentification
require_once 'core/auth.php';

// Récupération de la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Gestion des redirections avant tout output HTML
if ($page === 'logOut') {
    handleLogout();
}

// Pages protégées nécessitant une connexion
$protected_pages = ['dashboard', 'portefeuille', 'profil'];

// Vérification des droits d'accès selon le rôle
if (in_array($page, $protected_pages)) {
    redirectIfNotLoggedIn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonBudget - Gérez vos finances en toute simplicité</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Force la couleur noire sur tous les champs */
        input.form-control,
        input.form-control::placeholder,
        .input-group-text,
        .form-control,
        .form-control::placeholder {
            color: #000 !important;
        }
        
        /* Assure que le texte reste noir même après focus */
        .form-control:focus {
            color: #000 !important;
        }
        
        /* Style pour la navbar et le footer */
        .navbar {
            background-color: #6f42c1;
        }
        .navbar-brand, .nav-link {
            color: #000 !important;
        }
        .navbar-toggler {
            border-color: rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top" style="background-color: #6f42c1;">
    <div class="container">
        <a class="navbar-brand text-dark" href="/BUDGET/index.php">
            <i class="fas fa-wallet me-2"></i>
            MonBudget
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                style="border-color: rgba(0,0,0,0.5);">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="/BUDGET/index.php?page=signIn">
                            <i class="fas fa-user-plus me-1"></i>
                            Inscription
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="/BUDGET/index.php?page=logIn">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Connexion
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'conseiller' || $_SESSION['role'] == 'admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/BUDGET/index.php?page=mes_clients">
                                <i class="fas fa-users me-1"></i>
                                Mes Clients
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'client'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/BUDGET/index.php?page=dashboard">
                                <i class="fas fa-chart-line me-1"></i>
                                Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/BUDGET/index.php?page=portefeuille">
                                <i class="fas fa-wallet me-1"></i>
                                Mon portefeuille
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'conseiller'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/BUDGET/index.php?page=dashboard">
                                <i class="fas fa-chart-line me-1"></i>
                                Tableau de bord
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/BUDGET/index.php?page=dashboard">
                                <i class="fas fa-chart-line me-1"></i>
                                Administration
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="/BUDGET/index.php?page=profil">
                            <i class="fas fa-user me-1"></i>
                            Profil
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="controller/utilisateurController.php?utilisateur=deconnexion">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Déconnexion
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php
// Routing des pages
switch ($page) {
    case 'home':
        include 'view/home.php';
        break;

    case 'logIn':
        include 'view/logIn.php';
        break;

    case 'signIn':
        include 'view/signIn.php';
        break;

    case 'dashboard':
        include 'view/dashboard.php';
        break;

    case 'portefeuille':
        if ($_SESSION['role'] === 'client') {
            include 'view/portefeuille.php';
        } else {
            header('Location: index.php?page=dashboard');
            exit();
        }
        break;

    case 'profil':
        include 'view/profil.php';
        break;

    case 'mes_clients':
        include 'view/mes_clients.php';
        break;

    default:
        include 'view/404.php';
        break;
}
?>

<!-- Footer -->
<footer class="footer mt-auto py-3" style="background-color: #6f42c1;">
    <div class="container text-center">
        <span class="text-dark"> 2025 MonBudget - Tous droits réservés</span>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
