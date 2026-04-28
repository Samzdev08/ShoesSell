
<div class="container py-5">
    <h1 class="fw-bold mb-4">Mon Panier</h1>

    <?php if (empty($cart)) : ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="fs-1 mb-3">🛒</div>
                <h2 class="fw-bold fs-4 mb-2">Votre panier est vide</h2>
                <p class="text-muted mb-4">Découvrez notre catalogue et ajoutez des chaussures à votre panier.</p>
                <a href="/catalogue" class="btn btn-dark">Voir le catalogue</a>
            </div>
        </div>

    <?php else : ?>
        <div class="row g-4 align-items-start">

            <!-- Tableau articles -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Article</th>
                                    <th>Taille</th>
                                    <th>Qté</th>
                                    <th>Sous-total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $i => $item) : ?>
                                    <tr class="border-top">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= $item['image'] ?>" alt=" <?= htmlspecialchars($item['nom']) ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <p class="fw-semibold mb-0"><?= $item['marque'] ?> - <?= $item['nom'] ?></p>
                                                    <small class="text-muted">Prix unitaire : <?= $item['prix'] ?> CHF</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark"><?= $item['taille'] ?></span>
                                        </td>
                                        <td>
                                            <form action="/panier/maj" method="post">
                                                <input type="hidden" name="id" value="<?= $i ?>">
                                                <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1" max="5" class="form-control form-control-sm" style="width: 70px;" onchange="this.form.submit()" onkeydown="return false;">
                                            </form>
                                        </td>
                                        <td class="fw-semibold">
                                            <?= $item['prix'] * $item['quantite'] ?> CHF
                                        </td>
                                        <td>
                                            <a href="/panier/remove/<?= $i ?>" class="btn btn-outline-danger btn-sm">✕</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">
                        <a href="/panier/vider" class="btn btn-outline-secondary btn-sm btn-clear">Vider le panier</a>
                        
                    </div>
                </div>
            </div>

            <!-- Récapitulatif -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Récapitulatif</h5>

                        <?php foreach ($cart as $item) : ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted"><?= $item['nom'] ?> (×<?= $item['quantite'] ?>)</span>
                                <span><?= $item['prix'] * $item['quantite'] ?> CHF</span>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-2">
                            <span>Total</span>
                            <span>
                                <?= array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $cart)) ?> CHF
                            </span>
                        </div>

                        <p class="text-muted small mb-4">Livraison calculée à la commande</p>

                        <div class="d-grid gap-2">
                            <a href="/commande/checkout" class="btn btn-dark">Commander →</a>
                            <a href="/catalogue" class="btn btn-outline-secondary">Continuer mes achats</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>