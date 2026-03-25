<div class="container py-5">
    <h1 class="fw-bold mb-4">Catalogue des chaussures</h1>
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Catégorie</h5>
                    <form method="GET" action="/catalogue">
                        <select name="category" class="form-select mb-3" >
                            <option value="">Toutes les catégories</option>
                            <option value="1" <?= ($category == 1) ? 'selected' : '' ?>>Running</option>
                            <option value="2" <?= ($category == 2) ? 'selected' : '' ?>>Sneakers</option>
                            <option value="3" <?= ($category == 3) ? 'selected' : '' ?>>Skate</option>
                            <option value="4" <?= ($category == 4) ? 'selected' : '' ?>>Basketball</option>
                            <option value="5" <?= ($category == 5) ? 'selected' : '' ?>>Trail</option>
                            <option value="6" <?= ($category == 6) ? 'selected' : '' ?>>Tennis</option>
                        </select>

                        <div class="input-group mb-3">
                            <input
                                type="text"
                                name="marque"
                                class="form-control"
                                placeholder="Rechercher une marque..."
                                value="<?= htmlspecialchars($_GET['marque'] ?? '') ?>">
                            <button type="submit" class="btn btn-dark">🔍</button>
                        </div>

                        <a href="/catalogue" class="btn btn-outline-secondary w-100">✕ Réinitialiser</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-end align-items-center mb-4">
                <p class="mb-0 text-muted"><?= count($chaussures) ?> résultat(s)</p>
            </div>

            <?php if ($chaussures): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                    <?php foreach ($chaussures as $chaussure): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm chaussure-item" data-id="<?= $chaussure['id'] ?>" style="cursor: pointer;">
                                <div class="card-body text-center position-relative">

                                    <!-- Bouton wishlist -->
                                    <form method="POST" action="/wishlist/add" class="position-absolute top-0 end-0 m-2">
                                        <input type="hidden" name="chaussure_id" value="<?= $chaussure['id'] ?>">
                                        <button type="submit"
                                            class="btn btn-sm border-0 p-1 wishlist-btn"
                                            onclick="event.stopPropagation()"
                                            title="<?= $chaussure['in_wishlist'] ? 'Retirer de la wishlist' : 'Ajouter à la wishlist' ?>">
                                            <i class="fa-<?= $chaussure['in_wishlist'] ? 'solid' : 'regular' ?> fa-heart"
                                                style="color: <?= $chaussure['in_wishlist'] ? '#dc3545' : '#adb5bd' ?>; font-size: 1.1rem;"></i>
                                        </button>
                                    </form>

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
            <?php else: ?>
                <p class="text-muted">Aucune chaussure trouvée.</p>
            <?php endif; ?>
        </div>

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