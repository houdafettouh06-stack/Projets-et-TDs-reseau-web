// public/assets/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const stepUpload = document.getElementById('step-upload');
    const stepMapping = document.getElementById('step-mapping');
    const stepSuccess = document.getElementById('step-success');
    const errorDiv = document.getElementById('upload-error');
    const mappingBody = document.getElementById('mapping-body');
    const btnValidate = document.getElementById('btn-validate-import');
    const hiddenFileName = document.getElementById('temp-file-name');

    // Mappage des champs BDD disponibles
    const dbFields = {
        '': '--- Ignorer cette colonne ---',
        'nom_etudiant': 'Nom Étudiant',
        'prenom_etudiant': 'Prénom Étudiant',
        'classe': 'Classe',
        'email_parent': 'Email Parent',
        'telephone_parent': 'Téléphone Parent',
        'date_absence': 'Date Absence',
        'motif': 'Motif / Justification'
    };

    // --- Drag & Drop ---
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            handleFileUpload(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            handleFileUpload(fileInput.files[0]);
        }
    });

    // --- Upload Logic ---
    function handleFileUpload(file) {
        // Reset errors
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';

        const formData = new FormData();
        formData.append('csv_file', file);

        // Feedback visuel
        dropZone.innerHTML = `<div style="font-size: 2rem;">⏳</div><h3>Analyse de ${file.name}...</h3>`;

        fetch('/api/upload_csv.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMappingStep(data.data);
                } else {
                    showError(data.message);
                }
            })
            .catch(err => {
                showError("Une erreur technique est survenue.");
                console.error(err);
            });
    }

    function showError(msg) {
        dropZone.innerHTML = `<div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
        <h3>Glissez votre fichier ici ou cliquez pour parcourir</h3>`;
        errorDiv.textContent = msg;
        errorDiv.style.display = 'block';
    }

    // --- Mapping Interface ---
    function showMappingStep(data) {
        hiddenFileName.value = data.temp_filename; // Stocke le nom du fichier temp
        mappingBody.innerHTML = '';

        data.mapping_suggestions.forEach((item, index) => {
            const tr = document.createElement('tr');

            // Score badge
            let badgeClass = 'confidence-low';
            if (item.confidence >= 80) badgeClass = 'confidence-high';
            else if (item.confidence >= 50) badgeClass = 'confidence-med';

            // Select construction
            let optionsHtml = '';
            for (const [key, label] of Object.entries(dbFields)) {
                const selected = (key === item.suggested_field && item.suggested_field !== '') ? 'selected' : '';
                optionsHtml += `<option value="${key}" ${selected}>${label}</option>`;
            }

            tr.innerHTML = `
                <td><strong>${item.csv_header}</strong></td>
                <td class="text-muted"><em>Donnée Ligne 1...</em></td> <!-- TODO: remonter la 1ere ligne de donnée aussi serait cool -->
                <td><span class="confidence-badge ${badgeClass}">${item.confidence}%</span></td>
                <td>
                    <select class="mapping-select" data-csv-index="${item.csv_index}">
                        ${optionsHtml}
                    </select>
                </td>
            `;
            mappingBody.appendChild(tr);
        });

        stepUpload.style.display = 'none';
        stepMapping.style.display = 'block';
    }

    // --- Validation & Import ---
    btnValidate.addEventListener('click', () => {
        const mapping = {};
        const selects = document.querySelectorAll('.mapping-select');

        selects.forEach(select => {
            const field = select.value;
            if (field) { // Si un champ BDD est choisi
                mapping[select.dataset.csvIndex] = field;
            }
        });

        const payload = {
            temp_file: hiddenFileName.value,
            mapping: mapping
        };

        btnValidate.disabled = true;
        btnValidate.textContent = 'Importation en cours...';

        fetch('/api/save_import.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    stepMapping.style.display = 'none';
                    stepSuccess.style.display = 'block';
                    document.getElementById('success-message').textContent = data.message;
                } else {
                    alert('Erreur: ' + data.message);
                    btnValidate.disabled = false;
                    btnValidate.textContent = 'Valider et Importer';
                }
            })
            .catch(err => {
                alert('Erreur technique');
                console.error(err);
            });
    });
});
