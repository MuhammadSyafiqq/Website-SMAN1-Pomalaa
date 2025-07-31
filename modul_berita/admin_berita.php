<?php
require_once '../config/database.php';
session_start();

// Timeout
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
if (!empty($search)) {
    $search_like = '%' . $connection->real_escape_string($search) . '%';
    $stmt = $connection->prepare("SELECT b.id_berita, b.title, b.date, u.nama, b.image 
                                  FROM berita b 
                                  LEFT JOIN user u ON b.id_user = u.id_user 
                                  WHERE b.title LIKE ?
                                  ORDER BY b.date DESC");
    $stmt->bind_param("s", $search_like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $connection->query("SELECT b.id_berita, b.title, b.date, u.nama, b.image 
                                  FROM berita b 
                                  LEFT JOIN user u ON b.id_user = u.id_user 
                                  ORDER BY b.date DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 110px auto 40px;
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
        }

        .btn-back:hover {
            background-color: #666;
        }

        .btn-add {
            background-color: #00634b;
            color: white;
        }

        .btn-add:hover {
            background-color: #004030;
        }

        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert.info {
            background-color: #dbeafe;
            color: #1e3a8a;
        }

        .alert.error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            background: #004030;
            margin-bottom: 20px;
            border-radius: 8px;
            padding: 15px;
        }

        .top-bar form {
            display: flex;
            gap: 20px;
        }

        .top-bar input[type="text"] {
            padding: 8px 12px;
            color: black;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 200px;
        }

        table {
            width: 100%;
            background-color: #004030;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        thead th {
            background-color: #004030;
            color: white;
            padding: 12px;
            text-align: left;
            border-right: 1px solid #ddd;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            background-color: white;
            padding: 12px;
            vertical-align: middle;
            color: #000;
            border-top: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }

        tbody td:last-child {
            border-right: none;
        }

        tbody tr:last-child td {
            border-bottom: 1px solid #ddd;
        }

        td img {
            height: 60px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .actions a {
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
            background-color: #00634b;
        }

        .actions .delete {
            background-color: #dc2626;
            color: white;
        }

        .actions .delete:hover {
            background-color: #b91c1c;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .top-bar form {
                width: 100%;
                flex-direction: column;
                gap: 10px;
            }

            .top-bar input[type="text"] {
                width: 100%;
            }

            table thead {
                display: none;
            }

            table, table tbody, table tr, table td {
                display: block;
                width: 100%;
            }

            table tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                padding: 10px;
                background: #f9f9f9;
            }

            table td {
                padding: 8px 10px;
                border: none;
                text-align: left;
                position: relative;
            }

            table td::before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 4px;
                color: #004030;
            }

            td img {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container">
    <h1>Kelola Berita</h1>

    <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
        <div class="alert success">Berita berhasil ditambahkan!</div>
    <?php elseif (isset($_GET['edited']) && $_GET['edited'] == 1): ?>
        <div class="alert info">Berita berhasil diperbarui.</div>
    <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="alert error">Berita berhasil dihapus.</div>
    <?php endif; ?>

    <div class="top-bar">
        <div>
            <a href="../../dashboard_admin.php" class="btn btn-back">← Kembali ke Dashboard</a>
            <a href="tambah_berita.php" class="btn btn-add">+ Tambah Berita</a>
        </div>
        <form method="GET">
            <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-add">Cari</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Ditulis oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="Gambar">
                        <?php if (!empty($row['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar">
                        <?php else: ?>
                            <i>Tidak ada</i>
                        <?php endif; ?>
                    </td>
                    <td data-label="Judul"><?= htmlspecialchars($row['title']) ?></td>
                    <td data-label="Tanggal"><?= date('d M Y', strtotime($row['date'])) ?></td>
                    <td data-label="Ditulis oleh"><?= htmlspecialchars($row['nama']) ?></td>
                    <td data-label="Aksi" class="actions">
                        <a href="edit_berita.php?id=<?= $row['id_berita'] ?>" class="edit">Edit</a>
                        <a href="hapus_berita.php?id=<?= $row['id_berita'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus berita ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; padding: 20px;">Tidak ada berita ditemukan.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $connection->close(); ?>
</body>
</html>
