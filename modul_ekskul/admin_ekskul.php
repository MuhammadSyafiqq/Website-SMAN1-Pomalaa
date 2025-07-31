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

$result = $connection->query("SELECT * FROM ekstrakurikuler ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ekstrakurikuler</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f6fc;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 100px auto 50px;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #004030;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background-color: #888;
            color: white;
            margin-bottom: 10px;
        }

        .btn-back:hover {
            background-color: #666;
        }

        .btn-add {
            background-color: #004030;
            color: white;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background-color: #003020;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .notif {
            margin-bottom: 20px;
            padding: 12px 18px;
            border-radius: 8px;
            color: #0f5132;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
        }

        thead th {
            background-color: #004030;
            color: white;
            padding: 12px;
            text-align: left;
        }

        tbody td {
            padding: 12px;
            border-top: 1px solid #ddd;
            background: white;
        }

        td img {
            height: 60px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .actions a {
            display: inline-block;
            margin-right: 6px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
        }

        .actions .edit {
            background-color: #004030;
            color: white;
        }

        .actions .edit:hover {
            background-color: #003020;
        }

        .actions .delete {
            background-color: #dc2626;
            color: white;
        }

        .actions .delete:hover {
            background-color: #b91c1c;
        }

        /* RESPONSIVE STYLING */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                padding: 12px;
                background: #fff;
            }

            tbody td {
                border: none;
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
            }

            tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #004030;
                flex: 1;
            }

            .actions {
                display: flex;
                justify-content: flex-start;
                gap: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container">
    <h1>Kelola Ekstrakurikuler</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="notif">
            <?php
                switch ($_GET['success']) {
                    case 'add': echo "✅ Ekstrakurikuler berhasil ditambahkan."; break;
                    case 'edit': echo "✏️ Ekstrakurikuler berhasil diperbarui."; break;
                    case 'delete':
                    case 'hapus': echo "🗑️ Ekstrakurikuler berhasil dihapus."; break;
                }
            ?>
        </div>
    <?php endif; ?>

    <div class="top-actions">
        <a href="../dashboard_admin.php" class="btn btn-back">← Kembali</a>
        <a href="tambah_ekskul.php" class="btn btn-add">+ Tambah Ekskul</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Pembina</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Gambar">
                    <?php if (!empty($row['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Ekskul">
                    <?php else: ?>
                        <i>Tidak ada</i>
                    <?php endif; ?>
                </td>
                <td data-label="Nama"><?= htmlspecialchars($row['name']) ?></td>
                <td data-label="Pembina"><?= htmlspecialchars($row['constructor']) ?></td>
                <td data-label="Tanggal"><?= date('d M Y', strtotime($row['date'])) ?></td>
                <td data-label="Aksi" class="actions">
                    <a href="edit_ekskul.php?id=<?= $row['id_ekskul'] ?>" class="edit">Edit</a>
                    <a href="hapus_ekskul.php?id=<?= $row['id_ekskul'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus ekskul ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php $connection->close(); ?>
</body>
</html>
