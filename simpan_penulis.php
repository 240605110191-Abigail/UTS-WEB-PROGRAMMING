<?php
header('Content-Type: application/json');
require 'koneksi.php';

$nama_depan   = trim($_POST['nama_depan']   ?? '');
$nama_belakang = trim($_POST['nama_belakang'] ?? '');
$user_name    = trim($_POST['user_name']    ?? '');
$password     = $_POST['password']          ?? '';

if (!$nama_depan || !$nama_belakang || !$user_name || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    exit;
}

// Upload foto
$foto = 'default.png';
if (!empty($_FILES['foto']['name'])) {
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mime     = $finfo->file($_FILES['foto']['tmp_name']);
    $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mime, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WEBP']);
        exit;
    }
    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 2 MB']);
        exit;
    }

    $ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto = uniqid('foto_', true) . '.' . strtolower($ext);
    $dest = __DIR__ . '/uploads_penulis/' . $foto;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload foto']);
        exit;
    }
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO penulis (nama_depan, nama_belakang, user_name, password, foto) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $nama_depan, $nama_belakang, $user_name, $hashed, $foto);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data penulis berhasil disimpan']);
} else {
    // Hapus foto yang sudah terupload jika query gagal
    if ($foto !== 'default.png' && file_exists(__DIR__ . '/uploads_penulis/' . $foto)) {
        unlink(__DIR__ . '/uploads_penulis/' . $foto);
    }
    $err = $conn->errno === 1062 ? 'Username sudah digunakan' : 'Gagal menyimpan data: ' . $stmt->error;
    echo json_encode(['status' => 'error', 'message' => $err]);
}

$stmt->close();
$conn->close();