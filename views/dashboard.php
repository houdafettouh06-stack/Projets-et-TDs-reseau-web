<!-- views/dashboard.php -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Carte Total Absences -->
    <div class="card" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem;">
        <div style="font-size: 2.5rem; background: rgba(21, 101, 192, 0.1); width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">📉</div>
        <div>
            <h3 style="margin: 0; font-size: 2.5rem; font-weight: 800; color: var(--secondary);"><?php echo $totalAbsences; ?></h3>
            <span style="color: var(--text-muted); font-weight: 500;">Total Absences</span>
        </div>
    </div>

    <!-- Carte Notifications Envoyées -->
    <div class="card" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem;">
        <div style="font-size: 2.5rem; background: rgba(16, 185, 129, 0.1); width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">📨</div>
        <div>
            <h3 style="margin: 0; font-size: 2.5rem; font-weight: 800; color: var(--success);"><?php echo $notifsSent; ?></h3>
            <span style="color: var(--text-muted); font-weight: 500;">Notifications Envoyées</span>
        </div>
    </div>
</div>

<div class="charts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
    <!-- Graphique Barres : Par Classe -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem;">📊 Absences par Classe</h3>
        <div style="height: 300px;">
            <canvas id="chartClasses"></canvas>
        </div>
    </div>

    <!-- Graphique Donut : Par Motif -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem;">🍩 Motifs d'Absence</h3>
        <div style="height: 300px; display: flex; justify-content: center;">
            <canvas id="chartReasons"></canvas>
        </div>
    </div>
</div>

<script>
    // Configuration Chart.js
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = '#64748b';

    // Données injectées depuis PHP
    const dataClasses = <?php echo json_encode($statsClasse); ?>;
    const dataReasons = <?php echo json_encode($statsReason); ?>;

    // 1. Graphique Classes (Bar)
    if (document.getElementById('chartClasses')) {
        const ctxClasses = document.getElementById('chartClasses').getContext('2d');
        new Chart(ctxClasses, {
            type: 'bar',
            data: {
                labels: dataClasses.map(d => d.label),
                datasets: [{
                    label: "Nombre d'absences",
                    data: dataClasses.map(d => d.count),
                    backgroundColor: '#1565C0',
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Graphique Motifs (Doughnut)
    if (document.getElementById('chartReasons')) {
        const ctxReasons = document.getElementById('chartReasons').getContext('2d');
        new Chart(ctxReasons, {
            type: 'doughnut',
            data: {
                labels: dataReasons.map(d => d.label || 'Non justifié'),
                datasets: [{
                    data: dataReasons.map(d => d.count),
                    backgroundColor: [
                        '#43A047', // Vert
                        '#1565C0', // Bleu
                        '#F57C00', // Orange
                        '#E53935', // Rouge
                        '#8E24AA', // Violet
                        '#546E7A'  // Gris
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }
</script>
