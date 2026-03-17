<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow p-4" style="max-width:450px; width:100%;">
        
        <form action="/auth/register/post" method="post">
            <h2 class="text-center mb-2">Inscription</h2>
            <p class="text-center text-muted mb-4">Créer votre compte SoleShop</p>
            
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control"  value="<?= $old_post['nom'] ?>">
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control"  value="<?= $old_post['prenom'] ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"  value="<?= $old_post['email'] ?>">
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="form-control"  value="<?= $old_post['adresse'] ?>">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" >
                <div class="form-text">Min. 6 caractères</div>
            </div>

            <div class="mb-3">
                <label for="password_verify" class="form-label">Confirmer le mot de passe</label>
                <input type="password" id="password_verify" name="password_verify" class="form-control" >
            </div>

            <button type="submit" class="btn btn-dark w-100">Créer mon compte</button>

            <p class="text-center mt-3">
                Déjà un compte ? <a href="/auth/login">Connectez-vous ici</a>
            </p>

        </form>
    </div>
</div>