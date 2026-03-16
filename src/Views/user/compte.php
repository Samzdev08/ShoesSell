
<div class="page">
    <h1>Mon profil</h1>

    <div class="layout">

        <!-- Carte profil -->
        <aside class="profile-card">
            <div class="avatar">S</div>
            <p class="profile-name">Sam</p>
            <p class="profile-email">sam.tdkz@gmail.com</p>
            <p class="profile-since">Membre depuis 16.03.2026</p>

            <div class="stats-block">
                <p class="stats-label">Statistiques</p>
                <div class="stat-row"><span>Commandes</span><span class="val">2</span></div>
                <div class="stat-row"><span>En attente</span><span class="val">2</span></div>
                <div class="stat-row"><span>Total dépensé</span><span class="val">518.80 CHF</span></div>
            </div>

            <div class="btn-block">
                <a href="/orders" class="btn btn-outline">📦 Mes commandes</a>
                <a href="/products" class="btn btn-primary">👟 Catalogue</a>
            </div>
        </aside>

    
        <div class="panel">

            <div class="tabs">
                <button class="tab" onclick="showTab('info', this)">Mes informations</button>
                <button class="tab" onclick="showTab('password', this)">Mot de passe</button>
                <button class="tab active" onclick="showTab('orders', this)">Dernières commandes</button>
            </div>

            
            <div class="tab-content" id="tab-info">
                <h2 style="font-size:17px;font-weight:800;margin-bottom:20px">Informations personnelles</h2>
                <form>
                    <div class="form-group">
                        <label class="form-label">Nom complet</label>
                        <input type="text" class="form-control" value="Sam">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control" value="sam.tdkz@gmail.com">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:4px">✓ Enregistrer</button>
                </form>
            </div>

            
            <div class="tab-content" id="tab-password">
                <h2 style="font-size:17px;font-weight:800;margin-bottom:20px">Changer le mot de passe</h2>
                <form>
                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:4px">🔒 Changer</button>
                </form>
            </div>

        
            <div class="tab-content active" id="tab-orders">
                <div class="orders-header">
                    <h2>Dernières commandes</h2>
                    <a href="/orders" class="btn-sm">Toutes voir →</a>
                </div>

                <div class="order-row">
                    <span class="order-id">#CMD-2</span>
                    <span class="order-date">16.03.2026</span>
                    <span class="badge badge-yellow">EN ATTENTE</span>
                    <span class="order-total">328.90 CHF</span>
                    <a href="/orders/2" class="btn-voir">Voir</a>
                </div>

                <div class="order-row">
                    <span class="order-id">#CMD-1</span>
                    <span class="order-date">16.03.2026</span>
                    <span class="badge badge-yellow">EN ATTENTE</span>
                    <span class="order-total">189.90 CHF</span>
                    <a href="/orders/1" class="btn-voir">Voir</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function showTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }
</script>