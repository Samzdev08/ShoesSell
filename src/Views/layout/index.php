<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Mon site Slim' ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<?php
session_start();
var_dump($_SESSION);
?>

<body>
    <header>
        <h1>ShoeSell</h1>
        <nav>
            <a href="/">Accueil</a>
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
    <main>
        <h1>Bienvenue sur ShoeSell</h1>
        <p>Découvrez notre sélection de chaussures de qualité.</p>
        <?php if($chaussures): ?>
            <div class="chaussures-list">
                <?php foreach($chaussures as $chaussure): ?>
                    <div class="chaussure-item" data-id="<?= $chaussure['id'] ?>">
                        <h2><?= $chaussure['nom'] ?></h2>
                        <p>Prix : <?= $chaussure['prix']?> €</p>
                        <p>Marque : <?=$chaussure['marque'] ?></p>
                        <p>Description : <?= $chaussure['description'] ?></p>
                        <button>Voir plus</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucune chaussure disponible pour le moment.</p>
        <?php endif; ?>
    </main>
</body>

</html>