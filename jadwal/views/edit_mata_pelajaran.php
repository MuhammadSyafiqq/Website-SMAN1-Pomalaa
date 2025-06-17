<?php
session_start();

require_once '../config/database.php';
require_once '../models/MataPelajaran.php';

// Instantiate MataPelajaran model
$mataPelajaranModel = new MataPelajaran($connection);

$message = '';
// Handle form submission for edit mata pelajaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_mata_pelajaran'])) {
    $edit_id = $_POST['edit_id'] ?? '';
    $edit_nama = trim($_POST['edit_nama'] ?? '');
    $edit_kategori = $_POST['edit_kategori'] ?? '';

    if ($edit_id && $edit_nama && $edit_kategori) {
        $success = $mataPelajaranModel->update($edit_id, $edit_nama, $edit_kategori);
        if ($success) {
            $_SESSION['message'] = "Mata Pelajaran berhasil diupdate.";
        } else {
            $_SESSION['message'] = "Gagal mengupdate mata pelajaran.";
        }
    } else {
        $_SESSION['message'] = "Data tidak lengkap.";
    }
    header("Location: index.php");
    exit;
}

// Display form for editing mata pelajaran
$mataPelajaranToEdit = null;
if (isset($_GET['id'])) {
    $allMataPelajaran = $mataPelajaranModel->getAll();
    foreach ($allMataPelajaran as $mp) {
        if ($mp['id'] === $_GET['id']) {
            $mataPelajaranToEdit = $mp;
            break;
        }
    }
}

if (!$mataPelajaranToEdit) {
    $_SESSION['message'] = "Mata pelajaran tidak ditemukan.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Mata Pelajaran</title>
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Edit Mata Pelajaran</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message <?php echo (strpos($_SESSION['message'], 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($mataPelajaranToEdit['id']); ?>">
            
            <label for="edit_mp_nama">Nama Mata Pelajaran:</label>
            <input 
                type="text" 
                id="edit_mp_nama" 
                name="edit_nama" 
                required 
                maxlength="100" 
                value="<?php echo htmlspecialchars($mataPelajaranToEdit['nama']); ?>" 
            />
            
            <label for="edit_mp_kategori">Kategori:</label>
            <select name="edit_kategori" id="edit_mp_kategori" required>
                <option value="umum" <?php echo ($mataPelajaranToEdit['kategori'] === 'umum') ? 'selected' : ''; ?>>Umum</option>
                <option value="ipa" <?php echo ($mataPelajaranToEdit['kategori'] === 'ipa') ? 'selected' : ''; ?>>Peminatan IPA</option>
                <option value="ips" <?php echo ($mataPelajaranToEdit['kategori'] === 'ips') ? 'selected' : ''; ?>>Peminatan IPS</option>
            </select>

            <button type="submit" name="edit_mata_pelajaran">Update Mata Pelajaran</button>
            <a href="index.php" style="margin-left: 1rem;">Batal</a>
        </form>
    </div>
</body>
</html>
