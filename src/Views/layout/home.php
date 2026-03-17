<div class="container-fluid px-0">
    <div class="bg-dark text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="text-uppercase text-secondary fw-semibold mb-2">Nouvelle Collection 2026</h6>
                    <h1 class="display-4 fw-bold mb-3">La chaussure parfaite pour chaque pas</h1>
                    <p class="lead text-secondary mb-4">Découvrez notre dernière collection de chaussures conçues pour vous offrir le confort et la performance dont vous avez besoin.</p>
                    <div class="d-flex gap-3">
                        <a href="/catalogue" class="btn btn-primary btn-lg">Voir la collection</a>
                        <a href="/register" class="btn btn-outline-light btn-lg">Créer un compte</a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <span style="font-size: 10rem;">👟</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <ul class="nav gap-2 mb-4">
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="all">Tous voir</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Running">Running</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Sneakers">Sneakers</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Skate">Skate</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Basketball">Basketball</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Trail">Trail</a></li>
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" value="Tennis">Tennis</a></li>
        </ul>

        <h3 class="fw-semibold mb-4">Découvrez notre sélection de chaussures de qualité.</h3>

        <?php if ($chaussures): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($chaussures as $chaussure): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm chaussure-item" data-id="<?= $chaussure['id'] ?>" style="cursor: pointer;">
                            <div class="card-body text-center">
                                <div class="fs-1 mb-3">👟</div>
                                <h5 class="card-title fw-bold"><?= $chaussure['marque'] ?></h5>
                                <p class="card-text text-muted"><?= $chaussure['nom'] ?></p>
                                <p class="card-text fw-semibold"><?= $chaussure['prix'] ?> .-</p>
                                <p class="card-text">
                                    <?php if ($chaussure['en_stock']): ?>
                                        <span class="text-success">● En stock</span>
                                    <?php else: ?>
                                        <span class="text-danger">● En rupture de stock</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="/catalogue" class="btn btn-outline-dark">Voir tous les produits</a>
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