<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'notes_app';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM notes WHERE id = $id");
$note = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($note);
