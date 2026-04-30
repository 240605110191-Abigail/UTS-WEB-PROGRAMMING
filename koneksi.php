<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_blog';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Koneksi database gagal: ' . $conn->connect_error]));
}
 
$conn->set_charset('utf8mb4');

?>