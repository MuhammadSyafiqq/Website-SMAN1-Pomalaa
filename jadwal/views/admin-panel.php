<?php
session_start();

require_once 'config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/JurusanModel.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/JadwalUjianModel.php';


$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
$mataPelajaranModel = new MataPelajaranModel($connection);
$jadwalUjianModel = new JadwalUjianModel($connection);

$kelas_list = $kelasModel->getAll()->fetch_all(MYSQLI_ASSOC);
$jurusan_list = $jurusanModel->getAll()->fetch_all(MYSQLI_ASSOC);
$mapel_list = $mataPelajaranModel->getAll()->fetch_all(MYSQLI_ASSOC);
$jadwal_list = $jadwalUjianModel->getGroupedByJurusan()->fetch_all(MYSQLI_ASSOC);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

include __DIR__ . '/layout.php';
