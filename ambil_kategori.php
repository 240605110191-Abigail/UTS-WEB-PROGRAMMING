<?php
header('Content-Type: application/json');
require 'koneksi.php';

$result = $conn->query("SELECT id, nama_kategori, keterangan FROM kategori_artikel ORDER BY id ASC");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
$conn->close();