<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/JadwalUjianModel.php';
require_once __DIR__ . '/../helpers/functions.php';

$jadwalModel = new JadwalUjianModel($connection);

// Tambah
if (isset($_POST['add_jadwal_ujian'])) {
    $kelas_id = trim($_POST['kelas_id']);
    $jurusan_id = trim($_POST['jurusan_id']);
    $mata_pelajaran_id = trim($_POST['mata_pelajaran_id']);
    $tanggal = trim($_POST['tanggal']);
    $jam_mulai = trim($_POST['jam_mulai']);
    $jam_selesai = trim($_POST['jam_selesai']);
    
    if ($kelas_id && $jurusan_id && $mata_pelajaran_id && $tanggal && $jam_mulai && $jam_selesai) {
        $id = generateNextId($connection, 'jadwal_ujian', 'JU-');
        
        // Generate hari otomatis dari tanggal
        $hari = date('l', strtotime($tanggal));
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',    
            'Saturday' => 'Sabtu'
        ];
        $hari = $hariIndonesia[$hari];
        
        if ($jadwalModel->add($id, $kelas_id, $jurusan_id, $mata_pelajaran_id, $tanggal, $hari, $jam_mulai, $jam_selesai)) {
            redirectWithMessage("Jadwal ujian berhasil ditambahkan.", "../views/jadwal_ujian/index.php");
        } else {
            redirectWithMessage("Gagal menambahkan jadwal ujian.", "../views/jadwal_ujian/index.php");
        }
    } else {
        redirectWithMessage("Semua field harus diisi.", "../views/jadwal_ujian/index.php");
    }
}

// Edit
if (isset($_POST['edit_jadwal_ujian'])) {
    $id = $_POST['edit_id'];
    $tanggal = trim($_POST['edit_tanggal']);
    $jam_mulai = trim($_POST['edit_jam_mulai']);
    $jam_selesai = trim($_POST['edit_jam_selesai']);

    if (!$id || !$tanggal || !$jam_mulai || !$jam_selesai) {
        redirectWithMessage("Semua field harus diisi.", "../views/jadwal_ujian/index.php");
    }
    
    // Generate hari otomatis dari tanggal
    $hari = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $hari = $hariIndonesia[$hari];

    if ($jadwalModel->update($id, $tanggal, $hari, $jam_mulai, $jam_selesai)) {
        redirectWithMessage("Jadwal ujian berhasil diupdate.", "../views/jadwal_ujian/index.php");
    } else {
        redirectWithMessage("Gagal update jadwal ujian.", "../views/jadwal_ujian/index.php");
    }
}

// Hapus
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    if ($jadwalModel->delete($id)) {
        redirectWithMessage("Jadwal ujian berhasil dihapus.", "../views/jadwal_ujian/index.php");
    } else {
        redirectWithMessage("Gagal menghapus jadwal ujian.", "../views/jadwal_ujian/index.php");
    }
}



