<?php
require_once '../config/database.php';

$kelasId = $_GET['kelas_id'];
$jurusanId = $_GET['jurusan_id'];

$sql = "SELECT * FROM mata_pelajaran";

if ($kelasId !== 'KLS-X') {
    // Ambil kategori jurusan
    $kategori = '';
    $jurusan = $connection->prepare("SELECT nama FROM jurusan WHERE id = ?");
    $jurusan->bind_param("s", $jurusanId);
    $jurusan->execute();
    $result = $jurusan->get_result()->fetch_assoc();
    $namaJurusan = strtolower($result['nama']);

    if ($namaJurusan === 'ipa') {
        $sql .= " WHERE kategori IN ('IPA', 'Umum')";
    } elseif ($namaJurusan === 'ips') {
        $sql .= " WHERE kategori  IN ('IPS', 'Umum')";
    }
}

$data = $connection->query($sql);
$mataPelajaran = [];
while ($row = $data->fetch_assoc()) {
    $mataPelajaran[] = $row;
}
header('Content-Type: application/json');
echo json_encode($mataPelajaran);
