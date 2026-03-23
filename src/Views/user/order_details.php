
<?php
$statusConfig = [
    'en_attente' => ['class' => 'secondary', 'label' => 'En attente'],
    'confirmee'  => ['class' => 'primary',   'label' => 'Confirmée'],
    'expediee'   => ['class' => 'info',      'label' => 'Expédiée'],
    'livree'     => ['class' => 'success',   'label' => 'Livrée'],
    'annulee'    => ['class' => 'danger',    'label' => 'Annulée'],
];
$cfg = $statusConfig[$order['statut']] ?? ['class' => 'dark', 'label' => ucfirst($order['statut'])];
?>

<div class="container py-5">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Commande #CMD-<?= $order['id'] ?></h4>
            <p class="text-muted small mb-0">
                Passée le <?= (new DateTime($order['date_commande']))->format('d.m.Y à H:i') ?>
            </p>
        </div>
        <span class="badge bg-<?= $cfg['class'] ?> fs-6"><?= $cfg['label'] ?></span>
    </div>

    <div class="row g-4">

        <!-- Articles -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Articles</h6>

                    <?php foreach ($items as $i => $item): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 <?= $i < count($items) - 1 ? 'border-bottom' : '' ?>">
                            <div>
                                <p class="fw-semibold mb-0"><?= $item['chaussure_marque'] ?></p>
                                <p class="text-muted small mb-0">
                                    <?= $item['chaussure_nom'] ?> &middot;
                                    Taille EU <?= number_format($item['taille'], 0) ?> &middot;
                                    Qté <?= $item['quantite'] ?>
                                </p>
                            </div>
                            <span class="fw-semibold"><?= number_format($item['prix'] * $item['quantite'], 2) ?> CHF</span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <!-- Résumé -->
        <div class="col-lg-4 d-flex flex-column gap-3">

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Récapitulatif</h6>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Livraison</span>
                        <span class="text-success">Gratuite</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
                        <span>Total</span>
                        <span><?= number_format($order['montant'], 2) ?> CHF</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Livraison</h6>
                    <p class="small mb-1 fw-semibold"><?= $user['prenom'] . ' ' . $user['nom'] ?></p>
                    <p class="small text-muted mb-1"><?= $user['email'] ?></p>
                    <p class="small text-muted mb-0"><?= $user['adresse'] ?></p>
                </div>
            </div>

            <a href="/profil/" class="btn btn-outline-dark btn-sm">← Retour aux commandes</a>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>