<div class="container py-5">
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/catalogue" class="text-decoration-none">Catalogue</a></li>
            <li class="breadcrumb-item"><a href="/catalogue/<?= $chaussure['category_id'] ?>" class="text-decoration-none"><?= $chaussure['categorie'] ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($chaussure['nom']) ?></li>
        </ol>
    </nav>

    <div class="row g-5 align-items-start">

        <div class="col-md-6">
            <img src="<?= $chaussure['image'] ?>"
                alt="<?= htmlspecialchars($chaussure['nom']) ?>"
                class="img-fluid rounded shadow mb-4"
                style="object-fit: cover; max-height: 500px; width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        </div>

        <div class="col-md-6">
            <p class="text-muted text-uppercase fw-semibold mb-1"><?= htmlspecialchars($chaussure['marque']) ?></p>
            <h1 class="fw-bold mb-1"><?= htmlspecialchars($chaussure['nom']) ?></h1>
            <p class="badge bg-secondary mb-3"><?= htmlspecialchars($chaussure['categorie']) ?></p>
            <p class="fs-3 fw-bold text-dark mb-3"><?= $chaussure['prix'] ?> .-</p>
            <p class="text-muted mb-4"><?= htmlspecialchars($chaussure['description']) ?></p>

            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                <form action="/panier/ajouter" method="POST">
                    <input type="hidden" name="chaussure_id" value="<?= $chaussure['id'] ?>">
                    <input type="hidden" name="prix" value="<?= $chaussure['prix'] ?>">
                    <input type="hidden" name="nom" value="<?= htmlspecialchars($chaussure['nom']) ?>">
                    <input type="hidden" name="marque" value="<?= htmlspecialchars($chaussure['marque']) ?>">
                    <input type="hidden" name="image" value="<?= $chaussure['image'] ?>">

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

            <?php else: ?>
                <div class="d-flex gap-2 mb-4">
                    <a href="/admin/chaussures/edit/<?= $chaussure['id'] ?>" class="btn btn-outline-warning">✏️ Modifier</a>
                    <form action="/admin/chaussures/delete/<?= $chaussure['id'] ?>" method="POST">
                        <button type="submit" class="btn btn-outline-danger">🗑️ Supprimer</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between text-muted border-top pt-3 small">
                <span>📦 Livraison 2–4 jours</span>
                <span>🔄 Retours sous 30 jours</span>
                <span>🔒 Paiement sécurisé</span>
            </div>
        </div>

    </div>
</div>