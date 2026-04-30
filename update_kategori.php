<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nama_kategori = trim($_POST['nama_kategori'] ?? '');
$keterangan    = trim($_POST['keterangan']    ?? '');

if ($id <= 0 || !$nama_kategori) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("UPDATE kategori_artikel SET nama_kategori=?, keterangan=? WHERE id=?");
$stmt->bind_param('ssi', $nama_kategori, $keterangan, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Kategori berhasil diperbarui']);
} else {
    $err = $conn->errno === 1062 ? 'Nama kategori sudah ada' : 'Gagal memperbarui data: ' . $stmt->error;
    echo json_encode(['status' => 'error', 'message' => $err]);
}

$stmt->close();
$conn->close();