<?php
session_start();
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Mon site Slim' ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="d-flex flex-column min-vh-100">

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">

                <a class="navbar-brand fw-bold" href="/">ShoeSell</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item">
                            <a class="nav-link" href="/">Catalogue</a>
                        </li>

                        <?php if (isset($_SESSION['user_id'])) : ?>

                            <li class="nav-item">
                                <a class="nav-link" href="/compte">Mon compte</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="/panier">Panier</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link text-danger" href="/auth/logout">Déconnexion</a>
                            </li>

                        <?php else : ?>

                            <li class="nav-item">
                                <a class="nav-link" href="/auth/login">Connexion</a>
                            </li>

                            <li class="nav-item">
                                <a class="btn btn-primary ms-2" href="/auth/register">Inscription</a>
                            </li>

                        <?php endif ?>

                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <div class="container mt-3">

        <?php if (!empty($flash['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= $flash['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ✕ <?= $flash['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    </div>


    <main class="container flex-grow-1 mt-4">
        <?= $content ?>
    </main>


    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">
                &copy; 2024 ShoeSell. Tous droits réservés.
                • <a href="/mentions-legales" class="text-white text-decoration-underline">Mentions légales</a>
            </p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>