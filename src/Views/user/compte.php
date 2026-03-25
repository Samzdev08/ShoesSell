<div class="container py-5">
    <h1 class="fw-bold mb-4">Mon profil</h1>

    <div class="row g-4 align-items-start">

        <!-- Carte profil -->
        <div class="col-lg-3">
            <div class="card shadow-sm text-center p-4 mb-4">
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;font-size:26px;font-weight:800">
                    <?= $user['prenom'][0] ?>
                </div>
                <h5 class="fw-bold mb-0"><?= $user['prenom'] ?> <?= $user['nom'] ?></h5>
                <p class="text-muted small mb-1"><?= $user['email'] ?></p>
                <p class="text-muted small mb-3">Membre depuis <?= (new DateTime($user['created_at']))->format('d.m.Y') ?></p>

                <div class="text-start border rounded p-3 mb-3">
                    <p class="fw-bold small mb-2">Statistiques</p>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Commandes</span><span class="fw-semibold"><?= $stats['order_count'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>En attente</span><span class="fw-semibold"><?= $pendingOrders['pending_orders'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span>Wishlist</span><span class="fw-semibold"><?= $stats['wishlist_count'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Total dépensé</span><span class="fw-semibold"><?= $stats['total_spent'] ?> CHF</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="/profil/orders" class="btn btn-outline-dark btn-sm">📦 Mes commandes</a>
                    <a href="/wishlist/" class="btn btn-outline-dark btn-sm">❤️ Ma wishlist</a>
                    <a href="/catalogue" class="btn btn-dark btn-sm">👟 Catalogue</a>

                    <form method="POST" action="/profil/delete">
                        <button type="submit" class="btn btn-outline-danger btn-sm mt-2 w-100">
                            🗑️ Supprimer mon compte
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Panneau onglets -->
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Onglets -->
                    <ul class="nav nav-tabs mb-4" id="profileTabs">
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info">Mes informations</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-password">Mot de passe</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-orders">Dernières commandes</button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- Informations -->
                        <div class="tab-pane fade" id="tab-info">
                            <h5 class="fw-bold mb-4">Informations personnelles</h5>
                            <form method="POST" action="/profil/update">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nom </label>
                                    <input type="text" class="form-control" name="nom" value="<?= $user['nom'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" value="<?= $user['prenom'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">E-mail</label>
                                    <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-labelfw-semibold">Adresse</label>
                                    <input type="text" class="form-control" name="adresse" value="<?= $user['adresse'] ?>">
                                </div>
                                <button type="submit" class="btn btn-dark">✓ Enregistrer</button>
                            </form>
                        </div>

                        <!-- Mot de passe -->
                        <div class="tab-pane fade" id="tab-password">
                            <h5 class="fw-bold mb-4">Changer le mot de passe</h5>
                            <form method="POST" action="/profil/update-password">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mot de passe actuel</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirmer le nouveau mot de passe</label>
                                    <input type="password" class="form-control" name="confirm_password">
                                </div>
                                <button type="submit" class="btn btn-dark">🔒 Changer</button>
                            </form>
                        </div>

                        <!-- Commandes -->
                        <div class="tab-pane fade show active" id="tab-orders">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Dernières commandes</h5>
                                <a href="/profil/orders" class="btn btn-outline-dark btn-sm">Tous voir →</a>
                            </div>

                            <?php if ($recentOrders): ?>
                                <?php foreach ($recentOrders as $order): ?>

                                    <?php
                                    switch ($order['statut']) {
                                        case 'en_attente':
                                            $class = 'secondary';
                                            break;
                                        case 'confirmee':
                                            $class = 'primary';
                                            break;
                                        case 'expediee':
                                            $class = 'info';
                                            break;
                                        case 'livree':
                                            $class = 'success';
                                            break;
                                        case 'annulee':
                                            $class = 'danger';
                                            break;
                                        default:
                                            $class = 'dark';
                                    }
                                    ?>

                                    <div class="d-flex align-items-center gap-3 py-2 border-bottom flex-wrap">
                                        <span class="fw-bold text-muted small">#CMD-<?= $order['id'] ?></span>
                                        <span class="text-muted small"><?= (new \DateTime($order['date_commande']))->format('d.m.Y') ?></span>

                                        <span class="badge bg-<?= $class ?>">
                                            <?= ucfirst(str_replace('_', ' ', $order['statut'])) ?>
                                        </span>

                                        <span class="fw-semibold ms-auto"><?= $order['montant'] ?> CHF</span>

                                        <a href="/commande/<?= $order['id'] ?>/facture" class="upload-pdf btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-file-upload"></i>
                                        </a>

                                        <a href="/profil/orders/<?= $order['id'] ?>" class="btn btn-outline-dark btn-sm">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>