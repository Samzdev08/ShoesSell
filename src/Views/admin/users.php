<div class="container py-5">
    <h1 class="fw-bold mb-4">🛠️ Dashboard Admin</h1>

    <!-- STATS CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body text-center">
                    <div class="fs-2">👥</div>
                    <h2 class="fw-bold"><?= count($users) ?></h2>
                    <p class="mb-0">Utilisateurs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <div class="fs-2">🛒</div>
                    <h2 class="fw-bold"><?= count($order) ?></h2>
                    <p class="mb-0">Commandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body text-center">
                    <div class="fs-2">⏳</div>
                    <h2 class="fw-bold">
                        <?= count(array_filter($order, fn($o) => $o['statut'] === 'en_attente')) ?>
                    </h2>
                    <p class="mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body text-center">
                    <div class="fs-2">✅</div>
                    <h2 class="fw-bold">
                        <?= count(array_filter($order, fn($o) => $o['statut'] === 'livree')) ?>
                    </h2>
                    <p class="mb-0">Livrées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION COMMANDES -->
    <h2 class="fw-bold mb-3">🛒 Commandes</h2>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Modifier statut</th>
                        <th>Détail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($order): ?>
                        <?php foreach ($order as $commande): ?>
                            <tr>
                                <td class="fw-bold">#<?= $commande['commande_id'] ?></td>
                                <td><?= htmlspecialchars($commande['user_nom'] . ' ' . $commande['user_prenom']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($commande['user_email']) ?></td>
                                <td class="fw-semibold"><?= number_format($commande['montant'], 2) ?> .-</td>
                                <td><?= date('d.m.Y H:i', strtotime($commande['date_commande'])) ?></td>
                                <td>
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
                                    $badge = $badges[$commande['statut']] ?? 'secondary';
                                    $label = $labels[$commande['statut']] ?? $commande['statut'];
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                </td>
                                <td>
                                    <!-- SELECT AUTO-SUBMIT sans bouton -->
                                    <form method="POST" action="/admin/commandes/<?= $commande['commande_id'] ?>/statut">
                                        <select 
                                            name="statut" 
                                            class="form-select form-select-sm"
                                            style="width: 140px;"
                                            onchange="this.form.submit()">
                                            <option value="en_attente" <?= $commande['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                            <option value="confirmee"  <?= $commande['statut'] === 'confirmee'  ? 'selected' : '' ?>>Confirmée</option>
                                            <option value="expediee"   <?= $commande['statut'] === 'expediee'   ? 'selected' : '' ?>>Expédiée</option>
                                            <option value="livree"     <?= $commande['statut'] === 'livree'     ? 'selected' : '' ?>>Livrée</option>
                                            <option value="annulee"    <?= $commande['statut'] === 'annulee'    ? 'selected' : '' ?>>Annulée</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="/admin/commandes/<?= $commande['commande_id'] ?>/items" class="btn btn-outline-dark btn-sm">
                                        👁 Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucune commande trouvée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION UTILISATEURS -->
    <!-- SECTION UTILISATEURS -->
<h2 class="fw-bold mb-3">👥 Utilisateurs</h2>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Adresse</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="fw-bold">#<?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['prenom']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['adresse'] ?? '—') ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Utilisateur</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="POST" action="/admin/users/<?= $user['id'] ?>/delete">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            🗑️
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>