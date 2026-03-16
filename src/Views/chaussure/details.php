
<div class="container-detail-shoes">
    <p class="history"><a href="/">Accueil</a> / <a href="/catalogue">Catalogue</a>
        / <a href="/catalogue/order=<?= $chaussure['categorie'] ?>"><?= $chaussure['categorie'] ?>
            / <?= $chaussure['nom'] ?></a></p>
    <div class="product-container">
        <div class="image-container">
            👟
        </div>
        <div class="details-container">
            <p><?= $chaussure['marque'] ?></p>
            <h2><?= $chaussure['nom'] ?></h2>
            <p><?= $chaussure['categorie'] ?></p>
            <p class="price"><?= $chaussure['prix'] ?> .-</p>
            <p class="description"><?= $chaussure['description'] ?></p>
            <p>Selectioner votre taille</p>
            <ul class="size-shoes">
                <?php foreach ($sizes as $size): ?>
                    <li class="size" value="<?= $size['taille'] ?>"><?= $size['taille'] ?></li>
                <?php endforeach; ?>
            </ul>
            <input type="number" name="quantity" id="quantity" min="1" value="1">
            <button class="add-to-cart">🛒 Ajouter au panier</button>

            <div class="text-section">
                📦 Livraison 2–4 jours 🔄 Retours sous 30 jours 🔒 Paiement sécurisé
            </div>
        </div>
    </div>
</div>