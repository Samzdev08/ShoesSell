
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Ma wishlist <span class="text-muted fw-normal fs-6">(<?= count($wishlist) ?> article<?= count($wishlist) > 1 ? 's' : '' ?>)</span></h4>
        <a href="/profil/" class="btn btn-outline-dark btn-sm">← Retour au profil</a>
    </div>
    <?php if ($wishlist): ?>
        <div class="row g-4">
            <?php foreach ($wishlist as $item): ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['chaussure_categorie']) ?></span>
                                <form method="POST" action="/wishlist/remove">
                                    <input type="hidden" name="chaussure_id" value="<?= $item['chaussure_id'] ?>">
                                    <button type="submit" class="btn btn-sm text-danger border-0 p-0" title="Retirer de la wishlist">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                </form>
                            </div>

                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($item['chaussure_nom']) ?></h6>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($item['chaussure_marque']) ?></p>

                            <p class="fw-bold fs-5 mb-1"><?= number_format($item['chaussure_prix'], 2) ?> CHF</p>
                            <p class="text-muted small mb-3">Ajouté le <?= (new DateTime($item['date_ajout']))->format('d.m.Y') ?></p>

                            <a href="/chaussure/<?= $item['chaussure_id'] ?>" class="btn btn-dark btn-sm mt-auto">
                                Voir l'article
                            </a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="fa-regular fa-heart fa-2x mb-3"></i>
                <p class="mb-3">Votre wishlist est vide.</p>
                <a href="/catalogue" class="btn btn-dark btn-sm">👟 Voir le catalogue</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>