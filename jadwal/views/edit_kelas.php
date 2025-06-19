<?php
session_start();
require_once '../config/database.php';
require_once '../models/KelasModel.php';

$kelasModel = new KelasModel($connection);
$kelasList = $kelasModel->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Kelas</title>
</head>
<body>
    <h2>Manajemen Kelas</h2>

    <?php if (isset($_SESSION['message'])): ?>
    <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:10px; border-radius:5px;">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

    <form method="POST" action="../controllers/KelasController.php">
        <input type="text" name="kelas_nama" placeholder="Nama Kelas" required>
        <button type="submit" name="add_kelas">Tambah Kelas</button>
    </form>

    <h3>Daftar Kelas</h3>
    <table border="1">
        <thead>
            <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while($row = $kelasList->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td>
                        <a href="../controllers/kelasController.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Hapus kelas ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
