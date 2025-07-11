<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Monitoring Host</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .filter-form { margin-bottom: 20px; padding: 15px; background-color: #f4f4f4; border-radius: 5px; }
        .chart-container { width: 100%; margin-bottom: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-up { color: green; font-weight: bold; }
        .status-down { color: red; font-weight: bold; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h2>Dashboard Monitoring Host</h2>

        <div class="filter-form">
            <form method="GET" id="filterForm">
                <strong>Filter Data:</strong>
                <select name="host_id" id="host_id_selector">
                    <option value="">-- Pilih Host untuk Grafik --</option>
                    <?php
                        // Ambil daftar host unik dari database
                        $host_query = "SELECT DISTINCT host_id, host_name FROM ping_log ORDER BY host_name ASC";
                        $host_res = $conn->query($host_query);
                        while ($h = $host_res->fetch_assoc()) {
                            $selected = (!empty($_GET['host_id']) && $_GET['host_id'] == $h['host_id']) ? 'selected' : '';
                            echo "<option value='{$h['host_id']}' {$selected}>{$h['host_name']}</option>";
                        }
                    ?>
                </select>
                Waktu Mulai: <input type="datetime-local" name="start" value="<?= $_GET['start'] ?? '' ?>">
                Waktu Selesai: <input type="datetime-local" name="end" value="<?= $_GET['end'] ?? '' ?>">
                <button type="submit">Terapkan Filter</button>
                <a href="index.php">Reset</a>
            </form>
        </div>

        <h3>Grafik Latency Host</h3>
        <div class="chart-container">
            <canvas id="latencyChart"></canvas>
        </div>

        <h3>Riwayat Monitoring Terakhir</h3>
        <table>
            <thead>
                <tr><th>Host</th><th>Status</th><th>Latency (detik)</th><th>Warning</th><th>Waktu</th></tr>
            </thead>
            <tbody>
            <?php
                $sql = "SELECT * FROM ping_log";
                $conditions = [];
                if (!empty($_GET['start'])) {
                    $conditions[] = "timestamp >= '{$_GET['start']}'";
                }
                if (!empty($_GET['end'])) {
                    $conditions[] = "timestamp <= '{$_GET['end']}'";
                }
                if (!empty($_GET['host_id'])) {
                    $conditions[] = "host_id = '{$_GET['host_id']}'";
                }

                if (count($conditions) > 0) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                $sql .= " ORDER BY timestamp DESC LIMIT 200";
                $res = $conn->query($sql);

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $status_class = $row['status'] == 'UP' ? 'status-up' : 'status-down';
                        echo "<tr>
                            <td>{$row['host_name']}</td>
                            <td><span class='{$status_class}'>{$row['status']}</span></td>
                            <td>" . (is_null($row['latency']) ? '-' : number_format($row['latency'], 4)) . "</td>
                            <td>" . ($row['warning'] ?? '-') . "</td>
                            <td>{$row['timestamp']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data yang cocok dengan filter.</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

    <script>
        const ctx = document.getElementById('latencyChart').getContext('2d');
        let latencyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Latency (detik)',
                    data: [],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Latency (s)' } } }
            }
        });

        // Fungsi untuk mengambil data dan memperbarui grafik
        function updateChart() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Hanya fetch data jika host dipilih
            if (params.get('host_id')) {
                 fetch(`get_chart_data.php?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        latencyChart.data.labels = data.labels;
                        latencyChart.data.datasets[0].data = data.data;
                        latencyChart.data.datasets[0].label = `Latency (detik) untuk ${data.host_name}`;
                        latencyChart.update();
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }
        }

        // Panggil fungsi saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', updateChart);
    </script>
</body>
</html>