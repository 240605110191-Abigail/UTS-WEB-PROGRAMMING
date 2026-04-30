<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

// Cek apakah penulis masih memiliki artikel
$stmtCek = $conn->prepare("SELECT COUNT(*) AS jml FROM artikel WHERE id_penulis = ?");
$stmtCek->bind_param('i', $id);
$stmtCek->execute();
$jml = $stmtCek->get_result()->fetch_assoc()['jml'];
$stmtCek->close();

if ($jml > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Penulis tidak dapat dihapus karena masih memiliki artikel']);
    exit;
}

// Ambil nama foto
$stmtFoto = $conn->prepare("SELECT foto FROM penulis WHERE id = ?");
$stmtFoto->bind_param('i', $id);
$stmtFoto->execute();
$row = $stmtFoto->get_result()->fetch_assoc();
$stmtFoto->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM penulis WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Hapus file foto
    if ($row['foto'] !== 'default.png' && file_exists(__DIR__ . '/uploads_penulis/' . $row['foto'])) {
        unlink(__DIR__ . '/uploads_penulis/' . $row['foto']);
    }
    echo json_encode(['status' => 'success', 'message' => 'Data penulis berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
}

$stmt->close();
$conn->close();