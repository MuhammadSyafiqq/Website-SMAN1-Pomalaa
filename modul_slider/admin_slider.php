<?php
require_once '../config/database.php';
session_start();

$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $search_like = '%' . $connection->real_escape_string($search) . '%';
    $stmt = $connection->prepare("SELECT * FROM slider WHERE judul LIKE ? ORDER BY urutan ASC");
    $stmt->bind_param("s", $search_like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $connection->query("SELECT * FROM slider ORDER BY urutan ASC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Slider</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=<?php echo time(); ?>">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: white;
        margin: 0;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        background: #fff;
        padding: 30px;
        margin-top: 90px;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    h1 {
        text-align: center;
        color: #004030;
    }

    .btn {
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-back { background: #888; color: white; }
    .btn-back:hover { background: #666; }
    .btn-add { background: #004030; color: white; }
    .btn-add:hover { background: #003020; }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        background: #004030;
        padding: 16px 24px;
        border-radius: 8px;
        margin-bottom: 20px;
        gap: 12px;
    }

    .top-bar form {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .top-bar input[type="text"] {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
        color: black;
    }

    .top-bar button {
        padding: 8px 14px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        background-color: #003020;
        color: white;
        cursor: pointer;
    }

    .top-bar button:hover {
        background-color: #002010;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border: 1px solid #ccc;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ccc;
        text-align: left;
    }

    thead th {
        background-color: #004030;
        color: white;
    }

    td img {
        height: 60px;
        border-radius: 6px;
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .actions a {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 4px;
        text-decoration: none;
        white-space: nowrap;
    }

    .edit {
        background: #004030;
        color: white;
    }

    .edit:hover {
        background: #003020;
    }

    .delete {
        background: #dc2626;
        color: white;
    }

    .delete:hover {
        background: #b91c1c;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert.info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .alert.success {
        background: #d4edda;
        color: #155724;
    }

    .alert.error {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 600px) {
        .actions {
            flex-direction: column;
            align-items: flex-start;
        }

        .top-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .top-bar input[type="text"],
        .top-bar button {
            width: 100%;
        }
    }
    </style>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container">
    <h1>Kelola Slider</h1>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'edit'): ?>
            <div class="alert info">✏️ Slider berhasil diperbarui.</div>
        <?php elseif ($_GET['success'] === 'add'): ?>
            <div class="alert success">✅ Slider berhasil ditambahkan!</div>
        <?php elseif ($_GET['success'] === 'delete'): ?>
            <div class="alert error">🗑️ Slider berhasil dihapus.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="top-bar">
        <div>
            <a href="../../dashboard_admin.php" class="btn btn-back">← Kembali</a>
            <a href="tambah_slider.php" class="btn btn-add">+ Tambah Slider</a>
        </div>
        <form method="GET">
            <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th style="min-width: 100px;">Gambar</th>
                    <th>Judul</th>
                    <th style="min-width: 80px;">Urutan</th>
                    <th style="min-width: 80px;">Tampil</th>
                    <th style="min-width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>" alt="Slide">
                                <?php else: ?>
                                    <i>Tidak ada gambar</i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= (int)$row['urutan'] ?></td>
                            <td><?= $row['tampil'] ? 'Ya' : 'Tidak' ?></td>
                            <td class="actions">
                                <a href="edit_slider.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                                <a href="hapus_slider.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Hapus slider ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">Tidak ada data slider</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $connection->close(); ?>
</body>
</html>
