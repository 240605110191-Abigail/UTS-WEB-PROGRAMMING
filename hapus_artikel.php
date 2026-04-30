<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

$stmtGambar = $conn->prepare("SELECT gambar FROM artikel WHERE id = ?");
$stmtGambar->bind_param('i', $id);
$stmtGambar->execute();
$row = $stmtGambar->get_result()->fetch_assoc();
$stmtGambar->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM artikel WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $filePath = __DIR__ . '/uploads_artikel/' . $row['gambar'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus artikel']);
}

$stmt->close();
$conn->close();