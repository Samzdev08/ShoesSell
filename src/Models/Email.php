<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAIL_USERNAME'];
        $this->mail->Password   = str_replace(' ', '', $_ENV['MAIL_PASSWORD']);
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port       = (int) $_ENV['MAIL_PORT'];
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    private function send(string $to, string $subject, string $body): bool
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[Email] Erreur : ' . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendWelcomeEmail(string $toEmail, string $prenom, string $nom): bool
    {
        $subject = 'Bienvenue sur ShoesSell !';
        $body    = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="margin:0; padding:0; font-family:Arial, sans-serif; background:#fff; color:#333; font-size:14px;">

            <div style="background:#111; padding:24px 40px;">
                <div style="font-size:30px; font-weight:900; color:#fff; letter-spacing:4px; text-transform:uppercase;">
                    SHOE<span style="color:#e63946;">SELL</span>
                </div>
                <div style="font-size:11px; color:#666; letter-spacing:2px; text-transform:uppercase; margin-top:4px;">
                    La chaussure parfaite pour chaque pas
                </div>
            </div>
            <div style="background:#e63946; height:4px;"></div>

            <div style="padding:40px;">

                <div style="margin-bottom:40px; padding-bottom:25px; border-bottom:2px solid #f0f0f0;">
                    <div style="font-size:36px; font-weight:900; color:#111; text-transform:uppercase; letter-spacing:2px;">
                        BIENVENUE <span style="color:#e63946;">CHEZ NOUS</span>
                    </div>
                    <div style="font-size:12px; color:#999; margin-top:8px; letter-spacing:1px; text-transform:uppercase;">
                        Compte créé le ' . date('d/m/Y à H:i') . '
                    </div>
                </div>

                <div style="background:#f8f9fa; border-radius:6px; padding:20px 24px; margin-bottom:30px; border-top:3px solid #e63946;">
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#aaa; margin-bottom:14px; font-weight:700;">
                        Informations du compte
                    </div>
                    <p style="font-size:13px; color:#444; line-height:2.2;">
                        <strong style="color:#111;">Prénom :</strong> ' . htmlspecialchars($prenom) . '<br>
                        <strong style="color:#111;">Nom :</strong> ' . htmlspecialchars($nom) . '<br>
                        <strong style="color:#111;">Email :</strong> ' . htmlspecialchars($toEmail) . '
                    </p>
                </div>

                <div style="background:#f8f9fa; border-radius:6px; padding:28px 24px; margin-bottom:30px; border-top:3px solid #111;">
                    <p style="font-size:14px; color:#444; line-height:2; margin-bottom:12px;">
                        Bonjour <strong style="color:#111;">' . htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) . '</strong>,
                    </p>
                    <p style="font-size:14px; color:#444; line-height:2;">
                        Votre compte <strong style="color:#111;">ShoeSell</strong> a été créé avec succès.
                        Vous pouvez dès maintenant explorer notre catalogue et trouver la chaussure parfaite pour chaque occasion.
                    </p>
                </div>

                <div style="text-align:center; margin:35px 0;">
                    <a href="http://localhost:8081/catalogue"
                       style="display:inline-block; background:#e63946; color:#fff; padding:14px 36px;
                              border-radius:5px; text-decoration:none; font-size:13px; font-weight:700;
                              text-transform:uppercase; letter-spacing:2px;">
                        Découvrir le catalogue
                    </a>
                </div>

                <div style="margin-top:55px; padding-top:20px; border-top:1px solid #eee; text-align:center;">
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        Merci pour votre confiance — <strong style="color:#888;">ShoeSell</strong>
                    </p>
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        Questions ? <strong style="color:#888;">support@shoesell.ch</strong>
                    </p>
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        © ' . date('Y') . ' ShoeSell. Tous droits réservés.
                    </p>
                </div>

            </div>
        </body>
        </html>';

        return $this->send($toEmail, $subject, $body);
    }

    public function sendOrderConfirmationEmail(string $toEmail, string $prenom, string $nom, int $commandeId, array $cart, float $total): bool
    {
        $subject = 'Confirmation de votre commande #' . str_pad($commandeId, 5, '0', STR_PAD_LEFT);

        $itemsHtml = '';
        $i = 0;
        foreach ($cart as $item) {
            $rowBg = $i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            $itemsHtml .= '
        <tr style="background:' . $rowBg . '; border-bottom:1px solid #f0f0f0;">
            <td style="padding:14px 18px; font-size:13px; color:#222; font-weight:600;">'
                . htmlspecialchars($item['nom']) . '</td>
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

        $body = '
            <!DOCTYPE html>
            <html>
            <head><meta charset="UTF-8"></head>
            <body style="margin:0; padding:0; font-family:Arial, sans-serif; background:#fff; color:#333; font-size:14px;">

                <div style="background:#111; padding:24px 40px;">
                    <div style="font-size:30px; font-weight:900; color:#fff; letter-spacing:4px; text-transform:uppercase;">
                        SHOE<span style="color:#e63946;">SELL</span>
                    </div>
                    <div style="font-size:11px; color:#666; letter-spacing:2px; text-transform:uppercase; margin-top:4px;">
                        La chaussure parfaite pour chaque pas
                    </div>
                </div>
                <div style="background:#e63946; height:4px;"></div>

                <div style="padding:40px;">

                    <div style="margin-bottom:40px; padding-bottom:25px; border-bottom:2px solid #f0f0f0;">
                        <div style="font-size:36px; font-weight:900; color:#111; text-transform:uppercase; letter-spacing:2px;">
                            COMMANDE <span style="color:#e63946;">#' . str_pad($commandeId, 5, '0', STR_PAD_LEFT) . '</span>
                        </div>
                        <div style="font-size:12px; color:#999; margin-top:8px; letter-spacing:1px; text-transform:uppercase;">
                            Confirmée le ' . date('d/m/Y à H:i') . '
                        </div>
                        <div style="margin-top:12px;">
                            <span style="display:inline-block; padding:5px 16px; border-radius:30px; font-size:11px;
                                        font-weight:700; letter-spacing:1px; text-transform:uppercase;
                                        color:#e67e22; background:#ffeeba; border:1px solid #e67e22;">
                                En attente
                            </span>
                        </div>
                    </div>

                    <div style="background:#f8f9fa; border-radius:6px; padding:20px 24px; margin-bottom:30px; border-top:3px solid #e63946;">
                        <div style="font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#aaa; margin-bottom:14px; font-weight:700;">
                            Informations client
                        </div>
                        <p style="font-size:13px; color:#444; line-height:2.2;">
                            <strong style="color:#111;">Nom :</strong> ' . htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) . '<br>
                            <strong style="color:#111;">Email :</strong> ' . htmlspecialchars($toEmail) . '
                        </p>
                    </div>

                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#aaa; font-weight:700; margin-bottom:12px;">
                        Articles commandés
                    </div>
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#111;">
                                <th style="padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; text-align:left;">Produit</th>
                                <th style="padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; text-align:center;">Taille</th>
                                <th style="padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; text-align:center;">Qté</th>
                                <th style="padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; text-align:right;">Prix unit.</th>
                                <th style="padding:14px 18px; color:#fff; font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; text-align:right;">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>' . $itemsHtml . '</tbody>
                    </table>

                    <div style="margin-top:25px;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td></td>
                                <td style="background:#111; color:#fff; padding:20px 35px; border-radius:8px; text-align:right; width:280px;">
                                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#777; margin-bottom:8px;">
                                        Montant total
                                    </div>
                                    <div style="font-size:28px; font-weight:900; color:#fff;">
                                        ' . number_format($total, 2) . ' <span style="color:#e63946;">CHF</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align:center; margin:35px 0;">
                        <a href="http://localhost:8081/profil/"
                        style="display:inline-block; background:#e63946; color:#fff; padding:14px 36px;
                                border-radius:5px; text-decoration:none; font-size:13px; font-weight:700;
                                text-transform:uppercase; letter-spacing:2px;">
                            Voir mes commandes
                        </a>
                    </div>

                    <div style="margin-top:55px; padding-top:20px; border-top:1px solid #eee; text-align:center;">
                        <p style="font-size:11px; color:#bbb; line-height:2.2;">
                            Merci pour votre confiance — <strong style="color:#888;">ShoeSell</strong>
                        </p>
                        <p style="font-size:11px; color:#bbb; line-height:2.2;">
                            Questions ? <strong style="color:#888;">support@shoesell.ch</strong>
                        </p>
                        <p style="font-size:11px; color:#bbb; line-height:2.2;">
                            © ' . date('Y') . ' ShoeSell. Tous droits réservés.
                        </p>
                    </div>

                </div>
            </body>
            </html>';

        return $this->send($toEmail, $subject, $body);
    }

    public function sendOrderStatusEmail(string $toEmail, string $prenom, string $nom, int $commandeId, string $statut): bool
    {
        $statutConfig = match ($statut) {
            'livree'     => ['color' => '#28a745', 'bg' => '#d4edda', 'label' => 'Livrée',     'message' => 'Votre commande a été livrée. Nous espérons qu\'elle vous satisfait pleinement !'],
            'expediee'   => ['color' => '#17a2b8', 'bg' => '#d1ecf1', 'label' => 'Expédiée',   'message' => 'Votre commande est en route ! Vous la recevrez très prochainement.'],
            'confirmee'  => ['color' => '#007bff', 'bg' => '#cce5ff', 'label' => 'Confirmée',  'message' => 'Votre commande a été confirmée et est en cours de préparation.'],
            'en_attente' => ['color' => '#e67e22', 'bg' => '#ffeeba', 'label' => 'En attente', 'message' => 'Votre commande est en attente de traitement.'],
            'annulee'    => ['color' => '#dc3545', 'bg' => '#f8d7da', 'label' => 'Annulée',    'message' => 'Votre commande a été annulée. Contactez-nous pour plus d\'informations.'],
            default      => ['color' => '#333',    'bg' => '#eee',    'label' => $statut,       'message' => 'Le statut de votre commande a été mis à jour.']
        };

        $subject = 'Mise à jour de votre commande #' . str_pad($commandeId, 5, '0', STR_PAD_LEFT);

        $body = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="margin:0; padding:0; font-family:Arial, sans-serif; background:#fff; color:#333; font-size:14px;">

            <div style="background:#111; padding:24px 40px;">
                <div style="font-size:30px; font-weight:900; color:#fff; letter-spacing:4px; text-transform:uppercase;">
                    SHOE<span style="color:#e63946;">SELL</span>
                </div>
                <div style="font-size:11px; color:#666; letter-spacing:2px; text-transform:uppercase; margin-top:4px;">
                    La chaussure parfaite pour chaque pas
                </div>
            </div>
            <div style="background:#e63946; height:4px;"></div>

            <div style="padding:40px;">

                <div style="margin-bottom:40px; padding-bottom:25px; border-bottom:2px solid #f0f0f0;">
                    <div style="font-size:36px; font-weight:900; color:#111; text-transform:uppercase; letter-spacing:2px;">
                        COMMANDE <span style="color:#e63946;">#' . str_pad($commandeId, 5, '0', STR_PAD_LEFT) . '</span>
                    </div>
                    <div style="font-size:12px; color:#999; margin-top:8px; letter-spacing:1px; text-transform:uppercase;">
                        Mise à jour le ' . date('d/m/Y à H:i') . '
                    </div>
                    <div style="margin-top:12px;">
                        <span style="display:inline-block; padding:5px 16px; border-radius:30px; font-size:11px;
                                     font-weight:700; letter-spacing:1px; text-transform:uppercase;
                                     color:' . $statutConfig['color'] . ';
                                     background:' . $statutConfig['bg'] . ';
                                     border:1px solid ' . $statutConfig['color'] . ';">
                            ' . $statutConfig['label'] . '
                        </span>
                    </div>
                </div>

                <div style="background:#f8f9fa; border-radius:6px; padding:28px 24px; margin-bottom:30px; border-top:3px solid #111;">
                    <p style="font-size:14px; color:#111; line-height:2; margin-bottom:12px;">
                        Bonjour <strong style="color:#111;">' . htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) . '</strong>,
                    </p>
                    <p style="font-size:14px; color:#111; line-height:2;">
                        ' . $statutConfig['message'] . '
                    </p>
                </div>

                <div style="background:' . $statutConfig['bg'] . '; border-radius:6px; padding:20px 24px; margin-bottom:30px; border-top:3px solid ' . $statutConfig['color'] . ';">
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#555; margin-bottom:14px; font-weight:700;">
                        Détails de la mise à jour
                    </div>
                    <p style="font-size:13px; color:#111; line-height:2.2;">
                        <strong style="color:#111;">Numéro :</strong> #' . str_pad($commandeId, 5, '0', STR_PAD_LEFT) . '<br>
                        <strong style="color:#111;">Nouveau statut :</strong>
                        <span style="color:' . $statutConfig['color'] . '; font-weight:700;">' . $statutConfig['label'] . '</span><br>
                        <strong style="color:#111;">Date :</strong> ' . date('d/m/Y à H:i') . '
                    </p>
                </div>

                <div style="text-align:center; margin:35px 0;">
                    <a href="http://localhost:8081/profil/"
                       style="display:inline-block; background:#e63946; color:#fff; padding:14px 36px;
                              border-radius:5px; text-decoration:none; font-size:13px; font-weight:700;
                              text-transform:uppercase; letter-spacing:2px;">
                        Voir mes commandes
                    </a>
                </div>

                <div style="margin-top:55px; padding-top:20px; border-top:1px solid #eee; text-align:center;">
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        Merci pour votre confiance — <strong style="color:#888;">ShoeSell</strong>
                    </p>
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        Questions ? <strong style="color:#888;">support@shoesell.ch</strong>
                    </p>
                    <p style="font-size:11px; color:#bbb; line-height:2.2;">
                        © ' . date('Y') . ' ShoeSell. Tous droits réservés.
                    </p>
                </div>

            </div>
        </body>
        </html>';

        return $this->send($toEmail, $subject, $body);
    }
}
