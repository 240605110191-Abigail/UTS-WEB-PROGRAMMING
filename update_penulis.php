<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nama_depan    = trim($_POST['nama_depan']    ?? '');
$nama_belakang = trim($_POST['nama_belakang'] ?? '');
$user_name     = trim($_POST['user_name']     ?? '');
$password      = $_POST['password']           ?? '';

if ($id <= 0 || !$nama_depan || !$nama_belakang || !$user_name) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

// Ambil data lama
$stmtOld = $conn->prepare("SELECT foto FROM penulis WHERE id = ?");
$stmtOld->bind_param('i', $id);
$stmtOld->execute();
$old = $stmtOld->get_result()->fetch_assoc();
$stmtOld->close();

if (!$old) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

$foto = $old['foto'];

// Upload foto baru jika ada
if (!empty($_FILES['foto']['name'])) {
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($_FILES['foto']['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mime, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan']);
        exit;
    }
    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 2 MB']);
        exit;
    }

    $ext     = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $fotoBaru = uniqid('foto_', true) . '.' . strtolower($ext);
    $dest    = __DIR__ . '/uploads_penulis/' . $fotoBaru;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto']);
        exit;
    }

    // Hapus foto lama jika bukan default
    if ($foto !== 'default.png' && file_exists(__DIR__ . '/uploads_penulis/' . $foto)) {
        unlink(__DIR__ . '/uploads_penulis/' . $foto);
    }
    $foto = $fotoBaru;
}

// Update password hanya jika diisi
if ($password !== '') {
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt   = $conn->prepare("UPDATE penulis SET nama_depan=?, nama_belakang=?, user_name=?, password=?, foto=? WHERE id=?");
    $stmt->bind_param('sssssi', $nama_depan, $nama_belakang, $user_name, $hashed, $foto, $id);
} else {
    $stmt = $conn->prepare("UPDATE penulis SET nama_depan=?, nama_belakang=?, user_name=?, foto=? WHERE id=?");
    $stmt->bind_param('ssssi', $nama_depan, $nama_belakang, $user_name, $foto, $id);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data penulis berhasil diperbarui']);
} else {
    $err = $conn->errno === 1062 ? 'Username sudah digunakan' : 'Gagal memperbarui data: ' . $stmt->error;
    echo json_encode(['status' => 'error', 'message' => $err]);
}

$stmt->close();
$conn->close();