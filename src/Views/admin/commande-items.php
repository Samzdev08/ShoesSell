<div class="container py-5">
    <a href="/admin/users" class="btn btn-outline-dark mb-4">← Retour</a>
    <h1 class="fw-bold mb-4">📦 Détail de la commande #<?= $commandeId ?></h1>

    <?php if ($items): ?>

        <!-- INFO COMMANDE + LIVRAISON -->
        <div class="row g-4 mb-4">

            <!-- Livraison -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">📮 Livraison</h5>
                        <p class="mb-1">
                            <span class="text-muted">Client :</span>
                            <strong><?= htmlspecialchars($items[0]['shipping_prenom'] . ' ' . $items[0]['shipping_nom']) ?></strong>
                        </p>
                        <p class="mb-1">
                            <span class="text-muted">Adresse :</span>
                            <?= htmlspecialchars($items[0]['shipping_adresse'] ?? '—') ?>
                        </p>
                        <p class="mb-0">
                            <span class="text-muted">NPA / Ville :</span>
                            <?= $items[0]['shipping_npa'] ?> <?= htmlspecialchars($items[0]['shipping_ville']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statut + Total -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">📋 Récapitulatif</h5>
                        <?php
                        $badges = [
                            'en_attente' => 'warning text-dark',
                            'confirmee'  => 'info text-dark',
                            'expediee'   => 'primary',
                            'livree'     => 'success',
                            'annulee'    => 'danger'
                        ];
                        $labels = [
                            'en_attente' => 'En attente',
                            'confirmee'  => 'Confirmée',
                            'expediee'   => 'Expédiée',
                            'livree'     => 'Livrée',
                            'annulee'    => 'Annulée'
                        ];
                        ?>
                        <p class="mb-1">
                            <span class="text-muted">Statut :</span>
                            <span class="badge bg-<?= $badges[$items[0]['statut']] ?? 'secondary' ?>">
                                <?= $labels[$items[0]['statut']] ?? $items[0]['statut'] ?>
                            </span>
                        </p>
                        <p class="mb-1">
                            <span class="text-muted">Nombre d'articles :</span>
                            <strong><?= count($items) ?></strong>
                        </p>
                        <p class="mb-0 fs-5">
                            <span class="text-muted">Total :</span>
                            <strong><?= number_format($items[0]['montant'], 2) ?> .-</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ITEMS -->
        <h2 class="fw-bold mb-3">🛍️ Articles</h2>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Chaussure</th>
                            <th>Marque</th>
                            <th>Taille</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total ligne</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i => $item): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <span class="me-2">👟</span>
                                    <strong><?= htmlspecialchars($item['chaussure_nom']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($item['marque']) ?></td>
                                <td><?= $item['taille'] ?></td>
                                <td><?= $item['quantite'] ?></td>
                                <td><?= number_format($item['prix_unitaire'], 2) ?> .-</td>
                                <td class="fw-bold"><?= number_format($item['total_ligne'], 2) ?> .-</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold">Total commande :</td>
                            <td class="fw-bold fs-6"><?= number_format($items[0]['montant'], 2) ?> .-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-warning">Aucun article trouvé pour cette commande.</div>
    <?php endif; ?>
</div>