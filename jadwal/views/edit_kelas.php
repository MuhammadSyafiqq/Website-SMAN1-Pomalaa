<?php
session_start();

require_once '../config/database.php';
require_once '../models/Kelas.php';

// Instantiate Kelas model
$kelasModel = new Kelas($connection);

$message = '';
// Handle form submission for edit kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kelas'])) {
    $edit_id = $_POST['edit_id'] ?? '';
    $edit_nama = strtoupper(trim($_POST['edit_nama'] ?? ''));

    if ($edit_id && $edit_nama) {
        $success = $kelasModel->update($edit_id, $edit_nama);
        if ($success) {
            $_SESSION['message'] = "Kelas berhasil diupdate.";
        } else {
            $_SESSION['message'] = "Gagal mengupdate kelas.";
        }
    } else {
        $_SESSION['message'] = "Data tidak lengkap.";
    }
    header("Location: index.php");
    exit;
}

// Display form for editing kelas
$kelasToEdit = null;
if (isset($_GET['id'])) {
    $kelasList = $kelasModel->getAll();
    foreach ($kelasList as $kelas) {
        if ($kelas['id'] === $_GET['id']) {
            $kelasToEdit = $kelas;
            break;
        }
    }
}

if (!$kelasToEdit) {
    $_SESSION['message'] = "Kelas tidak ditemukan.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Kelas</title>
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Edit Kelas</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message <?php echo (strpos($_SESSION['message'], 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($kelasToEdit['id']); ?>">
            
            <label for="edit_kelas_nama">Nama Kelas:</label>
            <input 
                type="text" 
                id="edit_kelas_nama" 
                name="edit_nama" 
                required 
                maxlength="10" 
                value="<?php echo htmlspecialchars($kelasToEdit['nama']); ?>" 
            />
            <button type="submit" name="edit_kelas">Update Kelas</button>
            <a href="index.php" style="margin-left: 1rem;">Batal</a>
        </form>
    </div>
</body>
</html>
