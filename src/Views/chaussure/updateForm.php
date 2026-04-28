<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
        

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="/catalogue" class="btn btn-outline-secondary btn-sm">← Retour</a>
                <h2 class="fw-bold mb-0">Modifier une chaussure</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="/admin/chaussures/edit/<?= $chaussure['id'] ?>" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="image_url" value="<?= $chaussure['image'] ?>">

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                value="<?= htmlspecialchars($chaussure['nom']) ?>">
                        </div>

                        <!-- Marque -->
                        <div class="mb-3">
                            <label for="marque" class="form-label fw-semibold">Marque <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="marque" name="marque"
                                value="<?= htmlspecialchars($chaussure['marque']) ?>">
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-3">
                            <label for="categorie_id" class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="categorie_id" name="categorie_id">
                                <option value="">-- Choisir une catégorie --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= ($chaussure['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
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
                                    value="<?= htmlspecialchars($chaussure['prix']) ?>"
                                    step="0.01" min="0">
                                <span class="input-group-text">.-</span>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Image actuelle</label>
                            <div class="mb-2">
                                <img src="<?= $chaussure['image'] ?>" alt="<?= htmlspecialchars($chaussure['nom']) ?>"
                                    class="img-thumbnail" style="height: 150px; object-fit: contain;">
                            </div>
                            <label for="image" class="form-label fw-semibold">Nouvelle image <span class="text-muted fw-normal">(laisser vide pour garder l'actuelle)</span></label>
                            <input type="file" class="form-control" id="image" name="image"  accept="image/png, image/jpeg, image/jpg">
                            <div id="preview-container" class="mt-2 d-none">
                                <img id="image-preview" src="" alt="Prévisualisation"
                                    class="img-thumbnail" style="height: 150px; object-fit: contain;">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="3"><?= htmlspecialchars($chaussure['description']) ?></textarea>
                        </div>

                        <!-- Tailles & stocks -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tailles & stocks</label>
                            <div class="row g-2">
                                <?php
                                $tailles = [36, 37, 38, 39, 40, 41, 42, 43, 44, 45];
                                
                                $stocksMap = [];

                                foreach ($sizes as $s) {
                                    
                                    $stocksMap[$s['taille']] = $s['stock'];
                                    
                                }
                               
                                foreach ($tailles as $taille): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">T <?= $taille ?></span>
                                            <input type="number" class="form-control"
                                                name="stocks[<?= $taille ?>]"
                                                value="<?= $stocksMap[$taille . '.0'] ?? 0?>"
                                                min="0" max="10">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Laissez 0 pour les tailles non disponibles.</small>
                        </div>
                        

                        <!-- Boutons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/catalogue" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-warning px-4">
                                ✏️ Mettre à jour
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('image').addEventListener('change', function () {
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