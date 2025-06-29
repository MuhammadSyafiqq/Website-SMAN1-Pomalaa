<?php
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalQuery = $connection->query("SELECT COUNT(*) as total FROM feedback");
$totalRow = $totalQuery->fetch_assoc();
$totalFeedback = $totalRow['total'];
$totalPages = ceil($totalFeedback / $limit);

$result = $connection->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$feedbackCount = count($feedbacks);

$useSingleColumn = $feedbackCount <= 5;

if (!$useSingleColumn) {
    $leftColumn = array_slice($feedbacks, 0, 5);
    $rightColumn = array_slice($feedbacks, 5);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Feedback</title>
    <link rel="stylesheet" href="assets/style/style.css?v=2">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: #003366;
        }

        .feedback-section {
            padding: 60px 20px;
        }

        .feedback-container {
            max-width: 1200px;
            margin: auto;
        }

        .feedback-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #fff;
        }

        .feedback-list-grid {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .feedback-card {
            display: flex;
            align-items: flex-start;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        .feedback-card img.avatar {
            width: 50px;
            margin-right: 15px;
        }

        .feedback-content h4 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }

        .feedback-content .waktu {
            font-size: 14px;
            color: #555;
        }

        .feedback-content p {
            margin-top: 8px;
            font-style: italic;
            color: #333;
        }

        .balasan {
            margin-top: 10px;
            padding: 10px;
            background-color: #f1f1f1;
            border-left: 4px solid #00589D;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
        }

        .column {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 8px;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid #ccc;
            border-radius: 8px;
            color: #333;
            text-decoration: none;
            background-color: #fff;
            font-weight: bold;
        }

        .pagination .active {
            background-color: #ff7f3f;
            color: #fff;
            border: none;
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<section class="feedback-section">
    <div class="feedback-container">
        <h2 class="feedback-title">FEEDBACK</h2>

        <?php if ($useSingleColumn): ?>
            <div class="feedback-list-grid">
                <div class="column" style="align-items: center;">
                    <?php foreach ($feedbacks as $row): ?>
                        <div class="feedback-card" style="width: 100%;">
                            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" class="avatar" alt="User">
                            <div class="feedback-content">
                                <h4><?= htmlspecialchars($row['nama']) ?></h4>
                                <span class="waktu"><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></span>
                                <p>"<?= htmlspecialchars($row['komentar']) ?>"</p>
                                <?php if (!empty($row['balasan'])): ?>
                                    <div class="balasan">
                                        <strong>Balasan Admin:</strong><br>
                                        <?= nl2br(htmlspecialchars($row['balasan'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="feedback-list-grid">
                <div class="column">
                    <?php foreach ($leftColumn as $row): ?>
                        <div class="feedback-card">
                            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" class="avatar" alt="User">
                            <div class="feedback-content">
                                <h4><?= htmlspecialchars($row['nama']) ?></h4>
                                <span class="waktu"><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></span>
                                <p>"<?= htmlspecialchars($row['komentar']) ?>"</p>
                                <?php if (!empty($row['balasan'])): ?>
                                    <div class="balasan">
                                        <strong>Balasan Admin:</strong><br>
                                        <?= nl2br(htmlspecialchars($row['balasan'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="column">
                    <?php foreach ($rightColumn as $row): ?>
                        <div class="feedback-card">
                            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" class="avatar" alt="User">
                            <div class="feedback-content">
                                <h4><?= htmlspecialchars($row['nama']) ?></h4>
                                <span class="waktu"><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></span>
                                <p>"<?= htmlspecialchars($row['komentar']) ?>"</p>
                                <?php if (!empty($row['balasan'])): ?>
                                    <div class="balasan">
                                        <strong>Balasan Admin:</strong><br>
                                        <?= nl2br(htmlspecialchars($row['balasan'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php
            if ($page > 1) echo '<a href="?page=' . ($page - 1) . '">&#8249;</a>';
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == $page) {
                    echo '<span class="active">' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . '">' . $i . '</a>';
                }
            }
            if ($page < $totalPages) echo '<a href="?page=' . ($page + 1) . '">&#8250;</a>';
            ?>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

<?php $connection->close(); ?>
</body>
</html>
