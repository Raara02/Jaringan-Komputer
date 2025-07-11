<?php
include 'koneksi.php';

$sql = "SELECT * FROM (
    SELECT * FROM ping_log ORDER BY timestamp DESC
) AS sub GROUP BY host_name";

$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
header('Content-Type: application/json');
echo json_encode($data);
?>
