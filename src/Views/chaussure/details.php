<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/catalogue" class="text-decoration-none">Catalogue</a></li>
            <li class="breadcrumb-item"><a href="/catalogue/<?= $chaussure['category_id'] ?>" class="text-decoration-none"><?= $chaussure['categorie'] ?></a></li>
            <li class="breadcrumb-item active"><?= $chaussure['nom'] ?></li>
        </ol>
    </nav>

    <div class="row g-5 align-items-start">
        <div class="col-md-6">
            <div class="card bg-light border-0 d-flex align-items-center justify-content-center" style="height: 400px; font-size: 10rem;">
                👟
            </div>
        </div>

        <div class="col-md-6">
            <p class="text-muted text-uppercase fw-semibold mb-1"><?= $chaussure['marque'] ?></p>
            <h1 class="fw-bold mb-1"><?= $chaussure['nom'] ?></h1>
            <p class="badge bg-secondary mb-3"><?= $chaussure['categorie'] ?></p>
            <p class="fs-3 fw-bold text-dark mb-3"><?= $chaussure['prix'] ?> .-</p>
            <p class="text-muted mb-4"><?= $chaussure['description'] ?></p>

            <form action="/panier/ajouter" method="POST">
                <input type="hidden" name="chaussure_id" value="<?= $chaussure['id'] ?>">
                <input type="hidden" name="prix" value="<?= $chaussure['prix'] ?>">
                <input type="hidden" name="nom" value="<?= $chaussure['nom'] ?>">
                <input type="hidden" name="marque" value="<?= $chaussure['marque'] ?>">
                <input type="hidden" name="quantite" value="<?= $chaussure['quantite'] ?>" >

                <p class="fw-semibold mb-2">Sélectionner votre taille</p>
                <ul class="list-unstyled d-flex flex-wrap gap-2 mb-4">
                    <?php if ($sizes): ?>
                        <?php foreach ($sizes as $size): ?>
                            <li>
                                <input type="radio" name="taille" id="taille_<?= $size['taille'] ?>" value="<?= $size['taille'] ?>" class="d-none">
                                <label for="taille_<?= $size['taille'] ?>" class="btn btn-outline-dark btn-sm">
                                    <?= $size['taille'] ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Aucune taille disponible.</p>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <input type="number" name="quantite" min="1" max="5" value="1" class="form-control w-25" onkeydown="return false;">
                    <button type="submit" class="btn btn-dark btn-lg flex-grow-1">🛒 Ajouter au panier</button>
                </div>
            </form>

            <div class="d-flex justify-content-between text-muted border-top pt-3 small">
                <span>📦 Livraison 2–4 jours</span>
                <span>🔄 Retours sous 30 jours</span>
                <span>🔒 Paiement sécurisé</span>
            </div>
        </div>
    </div>
</div>
