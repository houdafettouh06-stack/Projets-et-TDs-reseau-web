<!-- views/import.php -->

<div class="card" id="step-upload">
    <h2>1. Charger le fichier d'absences</h2>
    <p style="color: var(--text-muted); margin-bottom: 1rem;">
        Le fichier doit être au format CSV. Le système détectera automatiquement les colonnes.
    </p>

    <div class="upload-zone" id="drop-zone">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
        <h3>Glissez votre fichier ici ou cliquez pour parcourir</h3>
        <p class="text-muted">Délimiteurs supportés : virgule, point-virgule, tabulation</p>
        <input type="file" id="file-input" accept=".csv" style="display: none;">
    </div>
    
    <div id="upload-error" style="color: var(--error); margin-top: 1rem; display: none;"></div>
</div>

<div class="card" id="step-mapping">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>2. Vérifier le mapping des colonnes</h2>
        <button id="btn-validate-import" class="btn-primary">Valider et Importer</button>
    </div>
    
    <p style="color: var(--text-muted); margin-bottom: 1rem;">
        Confirmez que les colonnes de votre fichier correspondent bien aux champs de la base de données.
    </p>

    <input type="hidden" id="temp-file-name">

    <table class="mapping-table">
        <thead>
            <tr>
                <th>Colonne CSV Détectée</th>
                <th>Exemple (1ère ligne)</th>
                <th>Confiance</th>
                <th>Champ Destination (BDD)</th>
            </tr>
        </thead>
        <tbody id="mapping-body">
            <!-- Rempli par JS -->
        </tbody>
    </table>
</div>

<div class="card" id="step-success" style="text-align: center; padding: 4rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
    <h2 style="color: var(--success);">Import terminé avec succès !</h2>
    <p id="success-message" style="color: var(--text-muted); margin-bottom: 2rem;"></p>
    <a href="/notifications" class="btn-primary">Voir les absences importées</a>
    <button onclick="location.reload()" style="background: transparent; border: 1px solid var(--border); color: var(--text-muted); padding: 0.75rem 1.5rem; border-radius: 8px; margin-left: 1rem; cursor: pointer;">Nouvel import</button>
</div>
