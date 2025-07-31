<?php
require_once '../config/database.php';
session_start();
$timeout_duration = 900;

// Session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $connection->real_escape_string($_POST['name']);
    $desc = $connection->real_escape_string($_POST['description']);
    $date = date('Y-m-d');
    $constructor = $connection->real_escape_string($_POST['constructor']);
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    // Cek duplikat berdasarkan name, constructor dan date
    $check_sql = "SELECT * FROM ekstrakurikuler 
                  WHERE name = '$name' AND constructor = '$constructor' AND date = '$date'";
    $check_result = $connection->query($check_sql);

    if ($check_result->num_rows == 0) {
        $sql = "INSERT INTO ekstrakurikuler (name, description, date, constructor, image)
                VALUES ('$name', '$desc', '$date', '$constructor', '$image')";
        $connection->query($sql);
        header("Location: admin_ekskul.php?success=add");
        exit();
    } else {
        header("Location: admin_ekskul.php?error=duplicate");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ekstrakurikuler</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f6f9ff;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #004030;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #004030;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            color: black;
            border-radius: 6px;
            border: 1px solid #004030;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            margin-top: 25px;
            background-color: #004030;
            color: white;
            padding: 12px 25px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #002b21;
        }

        .btn-submit:disabled {
            background-color: #888;
            cursor: not-allowed;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #004030;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            .form-container {
                margin: 30px 15px;
                padding: 20px;
                box-shadow: none;
                border-radius: 0;
            }

            h2 {
                font-size: 20px;
            }

            input[type="text"],
            textarea,
            input[type="file"],
            .btn-submit {
                font-size: 15px;
                padding: 10px;
            }
        }
    </style>
    <script>
        function disableSubmitButton() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerText = "Menyimpan...";
        }
    </script>
</head>
<body>

<div class="form-container">
    <h2>Tambah Ekstrakurikuler</h2>

    <form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" required>

        <label for="constructor">Pembina:</label>
        <input type="text" name="constructor" id="constructor" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="image">Gambar:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit" class="btn-submit" id="submitBtn">Simpan</button>
    </form>

    <a class="back-link" href="admin_ekskul.php">← Kembali ke Daftar Ekskul</a>
</div>

</body>
</html>
