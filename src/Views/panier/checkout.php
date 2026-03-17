<div class="container py-5">
    <h1 class="fw-bold mb-4">Finaliser la commande</h1>

    <div class="row g-4 align-items-start">

        <!-- Formulaire livraison -->
        <div class="col-lg-7">
            <form action="/commande/passer" method="POST">

                <!-- Informations de livraison -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">📦 Adresse de livraison</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= $user['nom'] ?>" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= $user['prenom']?>" >
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" name="adresse" class="form-control" value="<?= $user['adresse']?>" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ville</label>
                                <input type="text" name="ville" class="form-control"  >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Code postal</label>
                                <input type="text" name="code_postal" class="form-control" >
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">💳 Paiement</h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Numéro de carte</label>
                                <input type="text" name="carte" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date d'expiration</label>
                                <input type="text" name="expiration" class="form-control" placeholder="MM/AA" maxlength="5" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">CVV</label>
                                <input type="text" name="cvv" class="form-control" placeholder="123" maxlength="3" required>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-3 text-muted small">
                            <span>💳 Visa</span>
                            <span>💳 Mastercard</span>
                            <span>💳 American Express</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark btn-lg w-100">Confirmer la commande →</button>
            </form>
        </div>

        <!-- Récapitulatif commande -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">🧾 Récapitulatif</h5>

                    <?php foreach ($cart as $item) : ?>
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="fs-3">👟</div>
                            <div class="flex-grow-1">
                                <p class="fw-semibold mb-0"><?= $item['marque'] ?> - <?= $item['nom'] ?></p>
                                <small class="text-muted">Taille <?= $item['taille'] ?> · Qté <?= $item['quantite'] ?></small>
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

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span><?= array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $cart)) ?> CHF</span>
                    </div>

                    <div class="d-flex justify-content-between text-muted small mt-4 border-top pt-3">
                        <span>📦 Livraison 2–4 jours</span>
                        <span>🔒 Paiement sécurisé</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>