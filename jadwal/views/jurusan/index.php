<?php

require_once '../config/database.php';
require_once '../models/JurusanModel.php';

$jurusanModel = new JurusanModel($connection);
$data = $jurusanModel->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Jurusan</title>
</head>
<body>
    <h2>Manajemen Jurusan</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:10px; border-radius:5px;">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="../../controllers/JurusanController.php">
        <input type="text" name="jurusan_nama" placeholder="Nama Jurusan" required>
        <button type="submit" name="add_jurusan">Tambah Jurusan</button>
    </form>

    <h3>Daftar Jurusan</h3>
    <table border="1">
        <thead>
            <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td>
                        <a href="../../controllers/JurusanController.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Hapus jurusan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
