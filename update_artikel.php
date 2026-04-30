<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id          = isset($_POST['id'])          ? (int)$_POST['id']          : 0;
$judul       = trim($_POST['judul']         ?? '');
$id_penulis  = isset($_POST['id_penulis'])  ? (int)$_POST['id_penulis']  : 0;
$id_kategori = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 0;
$isi         = trim($_POST['isi']           ?? '');

if ($id <= 0 || !$judul || !$id_penulis || !$id_kategori || !$isi) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

// Ambil gambar lama
$stmtOld = $conn->prepare("SELECT gambar FROM artikel WHERE id = ?");
$stmtOld->bind_param('i', $id);
$stmtOld->execute();
$old = $stmtOld->get_result()->fetch_assoc();
$stmtOld->close();

if (!$old) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

$gambar = $old['gambar'];

if (!empty($_FILES['gambar']['name'])) {
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($_FILES['gambar']['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mime, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan']);
        exit;
    }
    if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 2 MB']);
        exit;
    }

    $ext        = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $gambarBaru = uniqid('artikel_', true) . '.' . strtolower($ext);
    $dest       = __DIR__ . '/uploads_artikel/' . $gambarBaru;

    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $dest)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar']);
        exit;
    }

    if (file_exists(__DIR__ . '/uploads_artikel/' . $gambar)) {
        unlink(__DIR__ . '/uploads_artikel/' . $gambar);
    }
    $gambar = $gambarBaru;
}

$stmt = $conn->prepare("UPDATE artikel SET id_penulis=?, id_kategori=?, judul=?, isi=?, gambar=? WHERE id=?");
$stmt->bind_param('iisssi', $id_penulis, $id_kategori, $judul, $isi, $gambar, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui artikel: ' . $stmt->error]);
}

$stmt->close();
$conn->close();