<?php
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

$kelas_id = $_POST['kelas_id'] ?? null;
$jurusan_id = $_POST['jurusan_id'] ?? null;

if (!$kelas_id || !$jurusan_id) {
    exit;
}

// Ambil nama jurusan
$stmt = $connection->prepare("SELECT nama FROM jurusan WHERE id = ?");
$stmt->bind_param("i", $jurusan_id);
$stmt->execute();
$stmt->bind_result($jurusan_nama);
$stmt->fetch();
$stmt->close();

$jurusan_nama = strtolower(trim($jurusan_nama));
$kategori = [];

if ($jurusan_nama === 'ipa') {
    $kategori = ['umum', 'ipa'];
} elseif ($jurusan_nama === 'ips') {
    $kategori = ['umum', 'ips'];
}

$options = "";

if (!empty($kategori)) {
    $placeholders = implode(',', array_fill(0, count($kategori), '?'));
    $query = "
        SELECT mp.id, mp.nama 
        FROM mata_pelajaran mp 
        WHERE mp.kategori IN ($placeholders)
        AND mp.id NOT IN (
            SELECT mata_pelajaran_id 
            FROM jadwal_ujian 
            WHERE kelas_id = ? AND jurusan_id = ?
        )
    ";
    $types = str_repeat("s", count($kategori)) . "ii";
    $params = array_merge($kategori, [$kelas_id, $jurusan_id]);

    $stmt = $connection->prepare($query);

    $bind_names[] = $types;
    foreach ($params as $key => $value) {
        $bind_name = 'bind' . $key;
        $$bind_name = $value;
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value=\"" . $row['id'] . "\">" . htmlspecialchars($row['nama']) . "</option>";
    }
    $stmt->close();
}

echo $options ?: "<option value=''>Tidak ada mata pelajaran tersedia</option>";
$connection->close();
?>
