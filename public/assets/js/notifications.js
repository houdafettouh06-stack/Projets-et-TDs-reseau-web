// public/assets/js/notifications.js

document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.select-row');
    const countSpans = document.querySelectorAll('.count-selected');
    const btnEmail = document.getElementById('btn-send-email');
    const btnWhatsapp = document.getElementById('btn-send-whatsapp');
    const loadingOverlay = document.getElementById('loading-overlay');
    const progressBar = document.getElementById('progress-bar');

    // Manage Selection
    function updateCount() {
        const checked = document.querySelectorAll('.select-row:checked');
        countSpans.forEach(span => span.textContent = checked.length);
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    // Send Logic
    function sendNotifications(type) {
        const selectedIds = Array.from(document.querySelectorAll('.select-row:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un étudiant.');
            return;
        }

        if (!confirm(`Confirmer l'envoi de ${selectedIds.length} notifications via ${type} ?`)) return;

        loadingOverlay.style.display = 'flex';
        progressBar.style.width = '0%';

        // Simulation de progression pour l'UX
        let progress = 0;
        const interval = setInterval(() => {
            progress += 5;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 100);

        // --- LOGIQUE WHATSAPP REEL ---
        if (type === 'whatsapp') {
            const checkboxes = document.querySelectorAll('.select-row:checked');
            checkboxes.forEach(cb => {
                let phone = cb.dataset.telephone || '';
                const student = cb.dataset.student;
                const date = cb.dataset.date;

                // 1. Nettoyage : enlever espaces, tirets
                phone = phone.replace(/[\s\-\.]/g, '');

                // 2. Formatage International (Hypothèse MAROC +212 par défaut)
                // Si commence par 0, on remplace par 212
                if (phone.startsWith('0')) {
                    phone = '212' + phone.substring(1);
                }

                // Message
                const message = `Bonjour, nous vous informons que l'élève ${student} a été marqué absent le ${date}. Merci de contacter l'administration.`;
                const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

                // Ouverture (Attention aux bloqueurs de popups si plusieurs)
                window.open(url, '_blank');
            });
        }
        // -----------------------------

        fetch('/api/send_notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ids: selectedIds,
                channel: type
            })
        })
            .then(res => res.json())
            .then(data => {
                clearInterval(interval);
                progressBar.style.width = '100%';

                setTimeout(() => {
                    loadingOverlay.style.display = 'none';
                    if (data.status === 'success') {
                        alert(`${data.count} messages envoyés !`);
                        location.reload(); // Recharger pour maj statuts
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                }, 500);
            })
            .catch(err => {
                clearInterval(interval);
                loadingOverlay.style.display = 'none';
                alert('Erreur technique');
                console.error(err);
            });
    }

    btnEmail.addEventListener('click', () => sendNotifications('email'));
    btnWhatsapp.addEventListener('click', () => sendNotifications('whatsapp'));
});
