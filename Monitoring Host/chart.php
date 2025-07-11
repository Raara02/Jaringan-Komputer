<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Ambil parameter dari request
$host_id = $_GET['host_id'] ?? null;
$start_time = $_GET['start'] ?? null;
$end_time = $_GET['end'] ?? null;

if (!$host_id) {
    echo json_encode(['labels' => [], 'data' => [], 'host_name' => '']);
    exit();
}

$sql = "SELECT host_name, latency, timestamp FROM ping_log WHERE host_id = ?";
$params = [$host_id];
$types = "i";

if ($start_time && $end_time) {
    $sql .= " AND timestamp BETWEEN ? AND ?";
    $params[] = $start_time;
    $params[] = $end_time;
    $types .= "ss";
}

$sql .= " ORDER BY timestamp ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$data = [];
$host_name = '';

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['timestamp'];
    $data[] = $row['latency'];
    if (empty($host_name)) {
        $host_name = $row['host_name'];
    }
}

$stmt->close();
$conn->close();

echo json_encode([
    'labels' => $labels,
    'data' => $data,
    'host_name' => $host_name
]);
?>