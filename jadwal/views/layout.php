<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h1>Dashboard Admin</h1>
  
  <?php if ($message): ?>
    <div class="alert"><?= $message ?></div>
  <?php endif; ?>

  <nav>
    <ul>
      <li><a href="#kelas">Kelas</a></li>
      <li><a href="#jurusan">Jurusan</a></li>
      <li><a href="#mapel">Mata Pelajaran</a></li>
      <li><a href="#jadwal">Jadwal Ujian</a></li>
    </ul>
  </nav>
  <section id="kelas"><?php include __DIR__ . '/kelas/index.php'; ?></section>
  <section id="jurusan"><?php include __DIR__ . '/jurusan/index.php'; ?></section>
  <section id="mapel"><?php include __DIR__ . '/partials/form_mapel.php'; ?></section>
  <section id="jadwal"><?php include __DIR__ . '/partials/form_jadwal.php'; ?></section>
</body>
</html>
