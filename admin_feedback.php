<?php
session_start();
require_once 'theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$sql = "SELECT * FROM feedback ORDER BY created_at DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Feedback</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=11">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            padding: 50px 20px;
            color: white;
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: white;
            color: black;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #00589D;
            color: white;
        }

        td.balasan {
            max-width: 250px;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .btn {
            padding: 6px 12px;
            background-color: #00589D;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block;
            margin-top: 4px;
        }

        .btn:hover {
            background-color: #003f70;
        }

        .btn-danger {
            background-color: #c0392b;
        }

        .btn-danger:hover {
            background-color: #a83226;
        }

        .actions a {
            display: block;
            margin-bottom: 6px;
        }

        .back-link {
            background: #888;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Feedback</h1>
    <a class="back-link" href="dashboard_admin.php">← Kembali ke Dashboard</a>

    <table>
        <thead>
            <tr>
                <th style="width: 120px;">Nama</th>
                <th style="width: 200px;">Komentar</th>
                <th style="width: 150px;">Waktu</th>
                <th style="width: 250px;">Balasan</th>
                <th style="width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['komentar']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    <td class="balasan"><?= $row['balasan'] ? htmlspecialchars($row['balasan']) : '<em>Belum dibalas</em>' ?></td>
                    <td class="actions">
                        <a href="balas_feedback.php?id=<?= $row['id'] ?>" class="btn">Balas</a>
                        <a href="hapus_feedback.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus feedback ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
