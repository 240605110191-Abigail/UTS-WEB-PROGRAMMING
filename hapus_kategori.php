<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

// Cek apakah kategori masih dipakai artikel
$stmtCek = $conn->prepare("SELECT COUNT(*) AS jml FROM artikel WHERE id_kategori = ?");
$stmtCek->bind_param('i', $id);
$stmtCek->execute();
$jml = $stmtCek->get_result()->fetch_assoc()['jml'];
$stmtCek->close();

if ($jml > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM kategori_artikel WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
}

$stmt->close();
$conn->close();