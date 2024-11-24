<?php
// Koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'notes_app';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambahkan note baru
if (isset($_POST['add_note'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("INSERT INTO notes (title, content) VALUES ('$title', '')");
    header("Location: index.php");
    exit();
}

// Update note
if (isset($_POST['save_note'])) {
    $id = (int)$_POST['id'];
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("UPDATE notes SET content = '$content' WHERE id = $id");
    header("Location: index.php");
    exit();
}

// Ambil semua note
$notes = $conn->query("SELECT * FROM notes ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Note</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container py-5">
    <h1 class="text-center mb-4">Web Note</h1>

    <!-- Button Add Note -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success" onclick="addNote()">+ Add Note</button>
    </div>

    <!-- Notes List -->
    <div class="list-group">
        <?php while ($note = $notes->fetch_assoc()): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($note['title']); ?></strong>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="editNote(<?= $note['id']; ?>)">✏️</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Form untuk Tambah/Update Note -->
<form id="noteForm" action="index.php" method="POST" style="display: none;">
    <input type="hidden" name="id" id="noteId">
    <input type="hidden" name="content" id="noteContent">
</form>

<script>
    function addNote() {
        Swal.fire({
            title: 'Add Note',
            input: 'text',
            inputLabel: 'Enter Note Title',
            inputPlaceholder: 'Title',
            showCancelButton: true,
            confirmButtonText: 'Add',
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'add_note';
                form.appendChild(input);

                const titleInput = document.createElement('input');
                titleInput.type = 'hidden';
                titleInput.name = 'title';
                titleInput.value = result.value;
                form.appendChild(titleInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function editNote(id) {
        fetch(`note.php?id=${id}`)
            .then(response => response.json())
            .then(note => {
                Swal.fire({
                    title: note.title,
                    input: 'textarea',
                    inputValue: note.content,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('noteForm');
                        document.getElementById('noteId').value = id;
                        document.getElementById('noteContent').value = result.value;
                        form.submit();
                    }
                });
            });
    }
</script>
</body>
</html>
