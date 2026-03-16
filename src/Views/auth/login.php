<div class="register-container">
    <form action="/auth/login/post" method="post">
        <h2>Connexion</h2>
        <p>Acceder a votre compte SoleShop</p>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
        <p>Pas de compte ? <a href="/auth/register">Inscrivez-vous ici</a></p>
    </form>
</div>