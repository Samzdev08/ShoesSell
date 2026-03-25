<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;

class CommandeController
{
    public function checkout(Request $request, Response $response)
    {
        $user = User::find($_SESSION['user_id']);


        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Checkout',
            'user' => $user,
            'cart' => $_SESSION['cart']
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'panier/checkout.php');
    }
    public function addOrder(Request $request, Response $response)
    {
        $data = filter_input_array(INPUT_POST, [
            'nom'              => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom'           => FILTER_SANITIZE_SPECIAL_CHARS,
            'shipping_adresse' => FILTER_SANITIZE_SPECIAL_CHARS,
            'shipping_npa'     => FILTER_SANITIZE_NUMBER_INT,
            'shipping_ville'   => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $data['user_id'] = $_SESSION['user_id'];
        $data['total']   = array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $_SESSION['cart']));
        $data['cart']    = $_SESSION['cart'];

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['shipping_adresse']) || empty($data['shipping_npa']) || empty($data['shipping_ville'])) {
            $_SESSION['flash']['error'] = 'Veuillez remplir tous les champs obligatoires.';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        if (!is_numeric($data['shipping_npa']) || strlen($data['shipping_npa']) != 4) {
            $_SESSION['flash']['error'] = 'Veuillez entrer un NPA valide (4 chiffres).';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['nom']) || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['prenom'])) {
            $_SESSION['flash']['error'] = 'Le nom et le prénom ne doivent contenir que des lettres.';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        $commandeId = Commande::create($data);
        foreach ($data['cart'] as $item) {
            CommandeItem::create([
                'chaussure_id' => $item['id'],
                'taille'       => $item['taille'],
                'quantite'     => $item['quantite'],
                'prix'         => (float) $item['prix'],
            ], $commandeId);
        }


        $_SESSION['cart'] = [];
        $_SESSION['flash']['success'] = 'Votre commande a été passée avec succès !';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function facture(Request $request, Response $response, array $args)
    {
        $idUser   = $_SESSION['user_id'];
        $id       = $args['id'];
        $commande = Commande::orderById($idUser, $id);
        $items    = CommandeItem::getItemsByCommandeId($id);

        $statutConfig = match ($commande['statut']) {
            'livree'     => ['color' => '#28a745', 'bg' => '#d4edda', 'label' => 'Livrée'],
            'expediee'   => ['color' => '#17a2b8', 'bg' => '#d1ecf1', 'label' => 'Expédiée'],
            'confirmee'  => ['color' => '#007bff', 'bg' => '#cce5ff', 'label' => 'Confirmée'],
            'en_attente' => ['color' => '#e67e22', 'bg' => '#ffeeba', 'label' => 'En attente'],
            'annulee'    => ['color' => '#dc3545', 'bg' => '#f8d7da', 'label' => 'Annulée'],
            default      => ['color' => '#333',    'bg' => '#eee',    'label' => $commande['statut']]
        };

        $itemsHtml = '';
        $i = 0;
        foreach ($items as $item) {
            $rowBg = $i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            $itemsHtml .= '
        <tr style="background:' . $rowBg . ';">
            <td style="padding:14px 18px; font-size:13px; color:#222; font-weight:600;">'
                . htmlspecialchars($item['chaussure_nom']) . '</td>
            <td style="padding:14px 18px; font-size:13px; text-align:center; color:#666;">'
                . $item['taille'] . '</td>
            <td style="padding:14px 18px; font-size:13px; text-align:center; color:#666;">'
                . $item['quantite'] . '</td>
            <td style="padding:14px 18px; font-size:13px; text-align:right; color:#666;">'
                . number_format($item['prix'], 2) . ' CHF</td>
            <td style="padding:14px 18px; font-size:13px; text-align:right; font-weight:700; color:#111;">'
                . number_format($item['prix'] * $item['quantite'], 2) . ' CHF</td>
        </tr>';
            $i++;
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin:0; padding:0; box-sizing:border-box; }
                body { font-family: Arial, sans-serif; background:#fff; color:#333; font-size:14px; }

                .topbar { background:#111; padding:24px 40px; }
                .brand { font-size:30px; font-weight:900; color:#fff; letter-spacing:4px; text-transform:uppercase; }
                .brand span { color:#e63946; }
                .tagline { font-size:11px; color:#666; letter-spacing:2px; text-transform:uppercase; margin-top:4px; }
                .redbar { background:#e63946; height:4px; }

                .content { padding:40px; }

                .facture-head { margin-bottom:40px; padding-bottom:25px; border-bottom:2px solid #f0f0f0; }
                .facture-title { font-size:36px; font-weight:900; color:#111; text-transform:uppercase; letter-spacing:2px; }
                .facture-title span { color:#e63946; }
                .facture-date { font-size:12px; color:#999; margin-top:8px; letter-spacing:1px; text-transform:uppercase; }
                .statut-badge {
                    display:inline-block;
                    margin-top:12px;
                    padding:5px 16px;
                    border-radius:30px;
                    font-size:11px;
                    font-weight:700;
                    letter-spacing:1px;
                    text-transform:uppercase;
                    color:' . $statutConfig['color'] . ';
                    background:' . $statutConfig['bg'] . ';
                    border:1px solid ' . $statutConfig['color'] . ';
                }

                .info-table { width:100%; border-collapse:separate; border-spacing:15px 0; margin-bottom:40px; }
                .info-box { background:#f8f9fa; border-radius:6px; padding:20px 24px; vertical-align:top; width:50%; }
                .info-box.black { border-top:3px solid #111; }
                .info-box.red   { border-top:3px solid #e63946; }
                .info-box-title { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#aaa; margin-bottom:14px; font-weight:700; }
                .info-box p { font-size:13px; color:#444; line-height:2.2; }
                .info-box strong { color:#111; }

                .table-title { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#aaa; font-weight:700; margin-bottom:12px; }
                table.articles { width:100%; border-collapse:collapse; }
                table.articles thead tr { background:#111; }
                table.articles thead th { padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; }
                table.articles tbody tr { border-bottom:1px solid #f0f0f0; }
                table.articles tbody tr:last-child { border-bottom:none; }

                .total-section { margin-top:25px; }
                .total-table { width:100%; border-collapse:collapse; }
                .total-box { background:#111; color:#fff; padding:20px 35px; border-radius:8px; text-align:right; width:280px; }
                .total-label { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#777; margin-bottom:8px; }
                .total-amount { font-size:28px; font-weight:900; color:#fff; }
                .total-amount span { color:#e63946; }

                .footer { margin-top:55px; padding-top:20px; border-top:1px solid #eee; text-align:center; }
                .footer p { font-size:11px; color:#bbb; line-height:2.2; }
                .footer strong { color:#888; }
            </style>
        </head>
        <body>

            <div class="topbar">
                <div class="brand">SHOE<span>SELL</span></div>
                <div class="tagline">La chaussure parfaite pour chaque pas</div>
            </div>
            <div class="redbar"></div>

            <div class="content">

                <div class="facture-head">
                    <div class="facture-title">FACTURE <span>#' . str_pad($commande['id'], 5, '0', STR_PAD_LEFT) . '</span></div>
                    <div class="facture-date">Émise le ' . date('d/m/Y à H:i', strtotime($commande['date_commande'])) . '</div>
                    <div><span class="statut-badge">' . $statutConfig['label'] . '</span></div>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="info-box black">
                            <div class="info-box-title">Détails commande</div>
                            <p>
                                <strong>Numéro :</strong> #' . str_pad($commande['id'], 5, '0', STR_PAD_LEFT) . '<br>
                                <strong>Date :</strong> ' . date('d/m/Y', strtotime($commande['date_commande'])) . '<br>
                                <strong>Statut :</strong> ' . $statutConfig['label'] . '
                            </p>
                        </td>
                        <td class="info-box red">
                            <div class="info-box-title">Paiement</div>
                            <p>
                                <strong>Montant :</strong> ' . number_format($commande['montant'], 2) . ' CHF<br>
                                <strong>Méthode :</strong> Carte bancaire<br>
                                <strong>Devise :</strong> CHF
                            </p>
                        </td>
                    </tr>
                </table>

                <div class="table-title">Articles commandés</div>
                <table class="articles">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Produit</th>
                            <th style="text-align:center;">Taille</th>
                            <th style="text-align:center;">Qté</th>
                            <th style="text-align:right;">Prix unit.</th>
                            <th style="text-align:right;">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>' . $itemsHtml . '</tbody>
                </table>

                <div class="total-section">
                    <table class="total-table">
                        <tr>
                            <td></td>
                            <td class="total-box">
                                <div class="total-label">Montant total</div>
                                <div class="total-amount">' . number_format($commande['montant'], 2) . ' <span>CHF</span></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <p>Merci pour votre confiance — <strong>ShoeSell</strong></p>
                    <p>Questions ? <strong>support@shoesell.ch</strong></p>
                    <p>© ' . date('Y') . ' ShoeSell. Tous droits réservés.</p>
                </div>

            </div>
        </body>
        </html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();
        $response->getBody()->write($pdfContent);

        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="facture-' . str_pad($commande['id'], 5, '0', STR_PAD_LEFT) . '.pdf"');
    }
}
