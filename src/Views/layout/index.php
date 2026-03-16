<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Mon site Slim' ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<?php session_destroy() ?>

<body>
    <header>
        <h1>ShoeSell</h1>
        <nav>
            <a href="/">Catalogue</a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="/compte">Mon compte</a>
                <a href="/panier">Panier</a>
                <a href="/auth/logout">Déconnexion</a>
            <?php else : ?>
                <a class="login" href="/auth/login">Connexion</a>
                <a class="register" href="/auth/register">Inscription</a>

            <?php endif ?>
        </nav>
    </header>
    <?php if (isset($flash)) : ?>
        <?php if (isset($flash['success'])) : ?>
            <div class="flash flash-success">✓ <?= $flash['success'] ?></div>
        <?php endif; ?>
        <?php if (isset($flash['error'])) : ?>
            <div class="flash flash-error">✕ <?= $flash['error'] ?></div>
        <?php endif; ?>
    <?php endif; ?>
    <main class="container">
        <?= $content ?>
    </main>
    <footer>
        <p>&copy; 2024 ShoeSell. Tous droits réservés. &bull; <a href="/mentions-legales">Mentions légales</a></p>
    </footer>
</body>

</html>