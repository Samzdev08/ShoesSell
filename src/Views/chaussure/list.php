<div class="list-shoes-container">
    <h1>Catalogue des chaussures</h1>
    <div class="filter-side">
        <h2>Categorie</h2>
        <ul>
            <li class="filter" value="all">Toutes les categories</li>
            <li class="filter" value="running">Running</li>
            <li class="filter" value="casual">Casual</li>
            <li class="filter" value="formal">Formal</li>
            <li class="filter" value="sport">Sport</li>
            <li class="filter" value="skate">Skate</li>
            <li class="filter" value="basketball">Basketball</li>
            <li class="filter" value="trail">Trail</li>
            <li class="filter" value="tennis">Tennis</li>
        </ul>
    </div>
    <div class="list-shoes">
        <div class="header-list">
            <div class="search-container">
                <input type="text" placeholder="Rechercher...">
                <button class="search">🔍</button>
            </div>
            <div class="result-numb">
                <p><?= count($chaussures) ?> : résultat(s) </p>
            </div>
        </div>
        <div class="content-list">
            <?php if ($chaussures): ?>
                <div class="chaussures-list">
                    <?php foreach ($chaussures as $chaussure): ?>
                        <div class="chaussure-item" data-id="<?= $chaussure['id'] ?>">
                            <div class="emoji">👟</div>
                            <h2><?= $chaussure['marque'] ?></h2>
                            <p> <?= $chaussure['nom'] ?> </p>
                            <p><?= $chaussure['prix'] ?> .-</p>
                            <p> <?php if ($chaussure['en_stock']): ?>  ● En stock <?php else: ?> ● En rupture de stock <?php endif; ?> </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucune chaussure trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>