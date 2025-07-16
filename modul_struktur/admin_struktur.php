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

// Cek apakah ada pencarian
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $search_query = "%" . $connection->real_escape_string($search) . "%";
    $stmt = $connection->prepare("SELECT * FROM struktur WHERE nama LIKE ? OR nip LIKE ? OR position LIKE ? OR status LIKE ? ORDER BY id_struktur DESC");
    $stmt->bind_param("ssss", $search_query, $search_query, $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $connection->query("SELECT * FROM struktur ORDER BY id_struktur DESC");
}

$notif = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') {
        $notif = 'Data berhasil ditambahkan.';
    } elseif ($_GET['success'] === 'edit') {
        $notif = 'Data berhasil diperbarui.';
    } elseif ($_GET['success'] === 'delete') {
        $notif = 'Data berhasil dihapus.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Struktur</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=17">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 60px 20px;
            color: #fff;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #ffffff;
            color: #000;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 25px;
            gap: 10px;
        }

        .btn {
            padding: 10px 16px;
            background-color: #00589D;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #003f70;
        }

        .btn-danger {
            background-color: #c0392b;
        }

        .search-box {
            display: flex;
            gap: 8px;
            flex: 1;
            justify-content: end;
        }

        .search-box input[type="text"] {
            padding: 8px 12px;
            color: black;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #00589D;
            color: white;
        }

        .photo-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .actions a {
            display: inline-block;
            margin: 3px 5px 3px 0;
        }

        .notif {
            padding: 12px 18px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 6px;
            border-left: 6px solid #3c763d;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Struktur Pegawai</h1>

    <?php if ($notif): ?>
        <div class="notif"><?= htmlspecialchars($notif) ?></div>
    <?php endif; ?>

    <div class="top-actions">
        <a href="../dashboard_admin.php" class="btn" style="background: gray;">← Kembali ke Dashboard</a>
        <a href="tambah_struktur.php" class="btn">+ Tambah Struktur</a>

        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">Cari</button>
        </form>
    </div>

    <table>
        <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Jabatan</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['nip']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if (!empty($row['photo'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['photo']) ?>" class="photo-preview" alt="Foto">
                        <?php else: ?>
                            <em>Tidak ada</em>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="edit_struktur.php?id=<?= $row['id_struktur'] ?>" class="btn">Edit</a>
                        <a href="hapus_struktur.php?id=<?= $row['id_struktur'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;"><em>Tidak ada data ditemukan.</em></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
