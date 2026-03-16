<div class="register-container">
    <form action="/auth/register/post" method="post">
        <h2>Inscription</h2>
        <p>Créer votre compte SoleShop</p>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required value="<?= $old_post['nom'] ?>">

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required value="<?= $old_post['prenom'] ?>">

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required value="<?= $old_post['email'] ?>">

        <label for="adresse">Adresse :</label>
        <input type="text" id="adresse" name="adresse" required value="<?= $old_post['adresse'] ?>">

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <label for="password_verify">Confirmer le mot de passe :</label>
        <input type="password" id="password_verify" name="password_verify" required>
        

        <button type="submit">Créer mon compte</button>
        <p>Déjà un compte ? <a href="/auth/login">Connectez-vous ici</a></p>
</div>