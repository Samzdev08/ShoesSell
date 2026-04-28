<div class="container py-5">
    <h1 class="fw-bold mb-4">Finaliser la commande</h1>

    <div class="row g-4 align-items-start">

        <!-- Formulaire livraison -->
        <div class="col-lg-7">
            <form action="/commande/add" method="POST">

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">📦 Adresse de livraison</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= $user['prenom'] ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Adresse (rue + numéro)</label>
                                <input type="text" name="shipping_adresse" class="form-control" value="<?= $user['adresse'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">NPA</label>
                                <input type="text" name="shipping_npa" class="form-control" placeholder="1000" pattern="[0-9]{4}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Localité</label>
                                <input type="text" name="shipping_ville" class="form-control" placeholder="Genève" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark btn-lg w-100">✓ Confirmer la commande →</button>
            </form>
        </div>

        <!-- Récapitulatif commande -->
        <div class="col-lg-5">
            <div class="card shadow-sm" style="position: sticky; top: 88px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">🧾 Votre commande</h5>

                    <?php foreach ($cart as $item) : ?>
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                             <img src="<?= $item['image'] ?>" alt=" <?= htmlspecialchars($item['nom']) ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <p class="fw-semibold mb-0"><?= $item['marque'] ?> - <?= $item['nom'] ?></p>
                                <small class="text-muted">Taille <?= $item['taille'] ?> × <?= $item['quantite'] ?></small>
                            </div>
                            <span class="fw-semibold"><?= $item['prix'] * $item['quantite'] ?> CHF</span>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Sous-total</span>
                        <span><?= array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $cart)) ?> CHF</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-3">
                        <span>Livraison</span>
                        <span class="text-success">Gratuite</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Total</span>
                        <span><?= array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $cart)) ?> CHF</span>
                    </div>

                    <div class="alert alert-warning d-flex gap-2 align-items-start py-2 px-3 mb-3">
                        <span>💵</span>
                        <small>Le paiement s'effectue lors de la récupération du colis.</small>
                    </div>

                    <div class="d-flex justify-content-between text-muted small border-top pt-3">
                        <span>📦 Livraison 2–4 jours</span>
                        <span>✅ Commande sécurisée</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>