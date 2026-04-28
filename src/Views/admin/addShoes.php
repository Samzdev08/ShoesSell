<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="/admin/users" class="btn btn-outline-secondary btn-sm">← Retour</a>
                <h2 class="fw-bold mb-0">Ajouter une chaussure</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="/admin/chaussures/create" enctype="multipart/form-data">

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                placeholder="Ex: Air Max 90">
                        </div>

                        <!-- Marque -->
                        <div class="mb-3">
                            <label for="marque" class="form-label fw-semibold">Marque <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="marque" name="marque"
                                value="<?= htmlspecialchars($_POST['marque'] ?? '') ?>"
                                placeholder="Ex: Nike">
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-3">
                            <label for="categorie_id" class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="categorie_id" name="categorie_id">
                                <option value="">-- Choisir une catégorie --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (($_POST['categorie_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Prix -->
                        <div class="mb-3">
                            <label for="prix" class="form-label fw-semibold">Prix (CHF) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="prix" name="prix"
                                    value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>"
                                    placeholder="Ex: 120.00" step="0.01" min="0">
                                <span class="input-group-text">.-</span>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">URL de l'image</label>
                            <input type="file" class="form-control" id="image" name="image"
                                value="<?= htmlspecialchars($_POST['image'] ?? '') ?>"
                                placeholder="https://exemple.com/image.jpg" accept="image/png, image/jpeg, image/jpg">
                            <!-- Prévisualisation -->
                            <div id="preview-container" class="mt-2 <?= empty($_POST['image']) ? 'd-none' : '' ?>">
                                <img id="image-preview" src="<?= htmlspecialchars($_POST['image'] ?? '') ?>"
                                    alt="Prévisualisation"
                                    class="img-thumbnail"
                                    style="height: 150px; object-fit: contain;">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="3"
                                placeholder="Décrivez la chaussure..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Tailles & stocks -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tailles & stocks</label>
                            <div class="row g-2" id="tailles-container">
                                <?php
                                $tailles = [36, 37, 38, 39, 40, 41, 42, 43, 44, 45];
                                $stocks  = $_POST['stocks']  ?? [];
                                foreach ($tailles as $i => $taille): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">T <?= $taille ?></span>
                                            <input type="number" class="form-control"
                                                name="stocks[<?= $taille ?>]"
                                                value="<?= htmlspecialchars($stocks[$taille] ?? 0) ?>"
                                                min="0" max="10" placeholder="Stock">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Laissez 0 pour les tailles non disponibles.</small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/admin/users" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary px-4">
                                ✚ Créer la chaussure
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('preview-container');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.onerror = () => container.classList.add('d-none');
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            container.classList.add('d-none');
        }
    });
</script>