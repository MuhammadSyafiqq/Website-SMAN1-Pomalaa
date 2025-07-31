<?php
require_once '../config/database.php';
session_start();

$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

$search = $_GET['search'] ?? '';
$level = $_GET['level'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM prestasi WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND title LIKE '%" . $connection->real_escape_string($search) . "%'";
}
if (!empty($level)) {
    $sql .= " AND level = '" . $connection->real_escape_string($level) . "'";
}
if (!empty($category)) {
    $sql .= " AND category = '" . $connection->real_escape_string($category) . "'";
}
$sql .= " ORDER BY date DESC";

$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Prestasi</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f6fc;
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 100px auto 40px;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #004030;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background-color: #888;
            color: white;
            margin-bottom: 15px;
        }

        .btn-back:hover {
            background-color: #666;
        }

        .btn-add {
            float: right;
            background-color: #004030;
            color: white;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background-color: #003222;
        }

        .notif {
            margin-bottom: 20px;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 15px;
        }

        .notif.success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .notif.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-form input[type="text"],
        .filter-form select {
            color: black;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .filter-form button {
            background-color: #004030;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .filter-form button:hover {
            background-color: #003222;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            min-width: 800px;
        }

        thead th {
            background-color: #004030;
            color: white;
            padding: 12px;
            text-align: left;
        }

        tbody td {
            background-color: white;
            padding: 12px;
            color: #000;
            vertical-align: middle;
        }

        td img {
            height: 60px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .actions a {
            margin-right: 8px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .actions .edit {
            background-color: #004030;
            color: white;
        }

        .actions .edit:hover {
            background-color: #003222;
        }

        .actions .delete {
            background-color: #dc2626;
            color: white;
        }

        .actions .delete:hover {
            background-color: #a31e1e;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .container {
                margin-top: 80px;
                padding: 20px;
            }

            .btn-add, .btn-back {
                float: none;
                width: 100%;
                margin-top: 10px;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form input,
            .filter-form select,
            .filter-form button {
                width: 100%;
            }

            td img {
                max-width: 100px;
                height: auto;
            }
        }
    </style>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container">
    <h1>Kelola Prestasi</h1>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'add'): ?>
            <div class="notif success">✅ Prestasi berhasil ditambahkan.</div>
        <?php elseif ($_GET['success'] === 'edit'): ?>
            <div class="notif success">✏️ Prestasi berhasil diperbarui.</div>
        <?php elseif ($_GET['success'] === 'delete'): ?>
            <div class="notif success">🗑️ Prestasi berhasil dihapus.</div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="../dashboard_admin.php" class="btn btn-back">← Kembali ke Dashboard</a>
    <a href="tambah_prestasi.php" class="btn btn-add">+ Tambah Prestasi</a>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
        <select name="level">
            <option value="">-- Pilih Level --</option>
            <?php
            $levels = ['SEKOLAH', 'KABUPATEN', 'PROVINSI', 'NASIONAL', 'INTERNASIONAL'];
            foreach ($levels as $l) {
                echo "<option value=\"$l\" " . ($level == $l ? 'selected' : '') . ">$l</option>";
            }
            ?>
        </select>
        <select name="category">
            <option value="">-- Pilih Kategori --</option>
            <?php
            $categories = ['sekolah','siswa', 'guru', 'ekstrakurikuler'];
            foreach ($categories as $c) {
                echo "<option value=\"$c\" " . ($category == $c ? 'selected' : '') . ">$c</option>";
            }
            ?>
        </select>
        <button type="submit">🔍 Cari</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Level</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['level']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar">
                            <?php else: ?>
                                <em>Tidak ada</em>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="edit_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="edit">Edit</a>
                            <a href="hapus_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus prestasi ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="notif warning">⚠️ Tidak ada data ditemukan untuk filter yang digunakan.</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php $connection->close(); ?>
