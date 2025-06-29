<?php
session_start();

require_once '../config/database.php';
require_once '../models/JurusanModel.php';

// Instantiate Jurusan model
$jurusanModel = new Jurusan($connection);

$message = '';
// Handle form submission for edit jurusan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_jurusan'])) {
    $edit_id = $_POST['edit_id'] ?? '';
    $edit_nama = strtoupper(trim($_POST['edit_nama'] ?? ''));

    if ($edit_id && $edit_nama) {
        $success = $jurusanModel->update($edit_id, $edit_nama);
        if ($success) {
            $_SESSION['message'] = "Jurusan berhasil diupdate.";
        } else {
            $_SESSION['message'] = "Gagal mengupdate jurusan.";
        }
    } else {
        $_SESSION['message'] = "Data tidak lengkap.";
    }
    header("Location: index.php");
    exit;
}

// Display form for editing jurusan
$jurusanToEdit = null;
if (isset($_GET['id'])) {
    $jurusanList = $jurusanModel->getAll();
    foreach ($jurusanList as $jurusan) {
        if ($jurusan['id'] === $_GET['id']) {
            $jurusanToEdit = $jurusan;
            break;
        }
    }
}

if (!$jurusanToEdit) {
    $_SESSION['message'] = "Jurusan tidak ditemukan.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Jurusan</title>
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Edit Jurusan</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message <?php echo (strpos($_SESSION['message'], 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($jurusanToEdit['id']); ?>">
            
            <label for="edit_jurusan_nama">Nama Jurusan:</label>
            <input 
                type="text" 
                id="edit_jurusan_nama" 
                name="edit_nama" 
                required 
                maxlength="10" 
                value="<?php echo htmlspecialchars($jurusanToEdit['nama']); ?>" 
            />
            <button type="submit" name="edit_jurusan">Update Jurusan</button>
            <a href="index.php" style="margin-left: 1rem;">Batal</a>
        </form>
    </div>
</body>
</html>
