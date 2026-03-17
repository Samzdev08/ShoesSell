<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg border-0 rounded-4" style="width: 100%; max-width: 420px;">
        <div class="card-body p-5">

            <div class="text-center mb-4">
                <h1 class="fw-bold fs-3 mb-1">👟 SoleShop</h1>
                <h2 class="fs-5 fw-semibold mb-1">Connexion</h2>
                <p class="text-muted small">Accédez à votre compte SoleShop</p>
            </div>

            
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 rounded-3" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            
            <form action="/auth/login/post" method="post">

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control form-control-lg rounded-3"
                        placeholder="exemple@email.com"
                        value="<?= $old_post['email'] ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control form-control-lg rounded-3"
                        placeholder="••••••••"
                        
                    >
                </div>

                <button type="submit" class="btn btn-dark btn-lg w-100 rounded-3 fw-semibold">
                    Se connecter
                </button>

            </form>

            <!-- Lien inscription -->
            <p class="text-center text-muted small mt-4 mb-0">
                Pas de compte ?
                <a href="/auth/register" class="text-dark fw-semibold text-decoration-underline">
                    Inscrivez-vous ici
                </a>
            </p>

        </div>
    </div>
</div>