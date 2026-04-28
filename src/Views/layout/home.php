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
                        <a href="/auth/register" class="btn btn-outline-light btn-lg">Créer un compte</a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <span style="font-size: 10rem;">👟</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <form method="GET" action="/" class="d-flex align-items-center gap-2 mb-4">

            <select name="category" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <option value="1">Running</option>
                <option value="2">Sneakers</option>
                <option value="3">Skate</option>
                <option value="4">Basketball</option>
                <option value="5">Trail</option>
                <option value="6">Tennis</option>
            </select>

            <a href="/" class="btn btn-outline-secondary">✕ Réinitialiser</a>
        </form>

        <h3 class="fw-semibold mb-4">Découvrez notre sélection de chaussures de qualité.</h3>

        <?php if ($chaussures): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($chaussures as $chaussure): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm chaussure-item" data-id="<?= $chaussure['id'] ?>" style="cursor: pointer;">
                            <div class="card-body text-center position-relative">

                                <!-- Bouton wishlist -->
                                <form method="POST" action="/wishlist/add" class="position-absolute top-0 end-0 m-2">
                                    <input type="hidden" name="chaussure_id" value="<?= $chaussure['id'] ?>">
                                    <button type="submit"
                                        class="btn btn-sm border-0 p-1 wishlist-btn"
                                        onclick="event.stopPropagation()">
                                        <i class="fa-<?= $chaussure['in_wishlist'] ? 'solid' : 'regular' ?> fa-heart"
                                            style="color: <?= $chaussure['in_wishlist'] ? '#dc3545' : '#adb5bd' ?>; font-size: 1.1rem;"></i>
                                    </button>
                                </form>

                                <img src="<?= $chaussure['image'] ?>" alt="<?= htmlspecialchars($chaussure['nom']) ?>" class="card-img-top mb-3" style="height: 200px; object-fit: cover;">
                                <h5 class="card-title fw-bold"><?= $chaussure['marque'] ?></h5>
                                <p class="card-text text-muted"><?= $chaussure['nom'] ?></p>
                                <p class="card-text fw-semibold"><?= $chaussure['prix'] ?> .-</p>
                                <p class="card-text">
                                    <?php if ($chaussure['en_stock'] >= 1): ?>
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