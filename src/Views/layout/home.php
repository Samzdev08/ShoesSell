<div class="main-container">
    <div class="pub-container">
        <div class="left-side">
            <h3>Nouvelle Collection 2026</h3>
            <h1>La chaussure parfaite pour chaque pas</h1>
            <p>Découvrez notre dernière collection de chaussures conçues pour vous offrir le confort et la performance dont vous avez besoin.</p>
            <div class="link">
                <a href="/catalogue">Voir la collection</a>
                <a href="/register">Créer un compte</a>
            </div>
        </div>
        <div class="right-side">
            👟
        </div>
    </div>
    <div class="content">
        <ul class="chip-list">
            <li class="chip" value="all">Tous voir</li>
            <li class="chip" value="Running">Running</li>
            <li class="chip" value="Sneakers">Sneakers</li>
            <li class="chip" value="Skate">Skate</li>
            <li class="chip" value="Basketball">Basketball</li>
            <li class="chip" value="Trail">Trail</li>
            <li class="chip" value="Tennis">Tennis</li>
        </ul>
        <h3>Découvrez notre sélection de chaussures de qualité.</h3>
        <?php if ($chaussures): ?>
            <div class="chaussures-list">
                <?php foreach ($chaussures as $chaussure): ?>
                    <div class="chaussure-item" data-id="<?= $chaussure['id'] ?>">
                        <div class="emoji">👟</div>
                        <h2><?= $chaussure['marque'] ?></h2>
                        <p> <?= $chaussure['nom'] ?> </p>
                        <p><?= $chaussure['prix'] ?> .-</p>
                        <p> <?php if ($chaussure['en_stock']): ?>  ● En stock <?php else: ?> ● En rupture de stock <?php endif; ?> </p>
                    </div>
                <?php endforeach; ?>
                <a href="/catalogue">Voir tous les produits</a>
            </div>
        <?php else: ?>
            <p>Aucune chaussure disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

<script>

    document.querySelectorAll('.chaussure-item').forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-id');
            window.location.href = `/chaussure/${id}`;
        });
    });
</script>