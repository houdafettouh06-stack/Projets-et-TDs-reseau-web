<!-- views/notifications.php -->

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2>Gestion des Notifications</h2>
            <p style="color: var(--text-muted);">Sélectionnez les parents à notifier.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button id="btn-send-email" class="btn-primary" style="background: var(--bg-card); border: 1px solid var(--border);">
                📧 Envoyer Email (<span class="count-selected">0</span>)
            </button>
            <button id="btn-send-whatsapp" class="btn-primary" style="background: #25D366; border: none;">
                💬 Envoyer WhatsApp (<span class="count-selected">0</span>)
            </button>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="mapping-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                    <th>Étudiant</th>
                    <th>Classe</th>
                    <th>Date Absence</th>
                    <th>Contact Parent</th>
                    <th>Statut Email</th>
                    <th>Statut WhatsApp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($absences)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">Aucune absence trouvée. Importez un fichier d'abord.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($absences as $absence): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="select-row" 
                                   value="<?= $absence['id'] ?>"
                                   data-telephone="<?= htmlspecialchars($absence['telephone_parent']) ?>"
                                   data-student="<?= htmlspecialchars($absence['nom_etudiant'] . ' ' . $absence['prenom_etudiant']) ?>"
                                   data-date="<?= htmlspecialchars($absence['date_absence']) ?>">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($absence['nom_etudiant'] . ' ' . $absence['prenom_etudiant']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($absence['classe']) ?></td>
                        <td><?= htmlspecialchars($absence['date_absence']) ?></td>
                        <td>
                            <div>📧 <?= htmlspecialchars($absence['email_parent']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">📱 <?= htmlspecialchars($absence['telephone_parent']) ?></div>
                        </td>
                        <td>
                            <span class="confidence-badge <?= $absence['statut_email'] === 'envoye' ? 'confidence-high' : 'confidence-med' ?>">
                                <?= $absence['statut_email'] === 'non_n' ? 'Non envoyé' : $absence['statut_email'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="confidence-badge <?= $absence['statut_whatsapp'] === 'envoye' ? 'confidence-high' : 'confidence-med' ?>">
                                <?= $absence['statut_whatsapp'] === 'non_n' ? 'Non envoyé' : $absence['statut_whatsapp'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal simulation envoi -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; flex-direction: column;">
    <div style="font-size: 3rem; margin-bottom: 1rem;">📨</div>
    <h3 style="color: white;">Envoi en cours...</h3>
    <div style="width: 300px; height: 4px; background: #334155; margin-top: 1rem; border-radius: 2px;">
        <div id="progress-bar" style="width: 0%; height: 100%; background: var(--primary); transition: width 0.3s;"></div>
    </div>
</div>

<script src="/assets/js/notifications.js"></script>
