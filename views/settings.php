<h2>Configuration du Système</h2>
<div class="card">
    <form action="/settings/save" method="POST">
        <h3>Paramètres SMTP (Email)</h3>
        <p class="text-muted" style="margin-bottom: 1.5rem;">Configurez le serveur d'envoi pour les notifications d'absence.</p>
        
        <div class="form-group">
            <label>Serveur SMTP</label>
            <input type="text" name="smtp_host" class="form-control" value="smtp.office365.com" placeholder="Ex: smtp.gmail.com">
        </div>

        <div class="form-group">
            <label>Port SMTP</label>
            <input type="number" name="smtp_port" class="form-control" value="587" placeholder="Ex: 587">
        </div>

        <div class="form-group">
            <label>Email Expéditeur</label>
            <input type="email" name="smtp_user" class="form-control" placeholder="notification@uemf.ac.ma">
        </div>

        <div class="form-group">
            <label>Mot de passe / App Password</label>
            <input type="password" name="smtp_pass" class="form-control" placeholder="••••••••">
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <button type="submit" class="btn-primary">💾 Sauvegarder la configuration</button>
        </div>
    </form>
</div>
