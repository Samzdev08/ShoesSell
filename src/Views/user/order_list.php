<?php
$statusConfig = [
    'en_attente' => ['class' => 'secondary', 'label' => 'En attente'],
    'confirmee'  => ['class' => 'primary',   'label' => 'Confirmée'],
    'expediee'   => ['class' => 'info',      'label' => 'Expédiée'],
    'livree'     => ['class' => 'success',   'label' => 'Livrée'],
    'annulee'    => ['class' => 'danger',    'label' => 'Annulée'],
];
?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Mes orders</h4>
        <a href="/profil/" class="btn btn-outline-dark btn-sm">← Retour au profil</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <?php if ($orders): ?>
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">order</th>
                            <th>Date</th>
                            <th>Nombre articles</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $cfg = $statusConfig[$order['statut']] ?? ['class' => 'dark', 'label' => ucfirst($order['statut'])];
                        ?>
                            <tr>
                                <td class="ps-4 fw-semibold">#CMD-<?= $order['id'] ?></td>
                                <td class="text-muted small align-middle">
                                    <?= (new DateTime($order['date_order']))->format('d.m.Y') ?>
                                </td>
                                <td class="text-muted small align-middle">
                                    <?= $order['nb_articles'] ?>
                                </td>
                                <td class="fw-semibold align-middle">
                                    <?= number_format($order['montant'], 2) ?> CHF
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-<?= $cfg['class'] ?>"><?= $cfg['label'] ?></span>
                                </td>
                                <td class="text-end pe-4 align-middle">
                                    <a href="/commande/<?= $order['id'] ?>/facture" class="upload-pdf btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-file-upload"></i>
                                    </a>
                                    <a href="/profil/orders/<?= $order['id'] ?>" class="btn btn-outline-dark btn-sm">Voir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <p class="mb-3">Vous n'avez pas encore passé de order.</p>
                    <a href="/catalogue" class="btn btn-dark btn-sm">👟 Voir le catalogue</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>