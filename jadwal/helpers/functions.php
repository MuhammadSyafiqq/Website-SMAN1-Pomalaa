<?php

/**
 * Redirect ke halaman utama dengan pesan.
 *
 * @param string $message Pesan yang akan ditampilkan.
 * @param string $location Halaman tujuan (default: index.php)
 */
function redirectWithMessage($message, $location = 'index.php')
{
    $location .= '?message=' . urlencode($message);
    header("Location: $location");
    exit();
}

/**
 * Membuat ID otomatis dengan awalan tertentu.
 *
 * Contoh: JU-0001, KL-0005, dll.
 *
 * @param mysqli $conn Koneksi database
 * @param string $table Nama tabel (e.g. 'kelas')
 * @param string $prefix Awalan ID (e.g. 'KL-')
 * @return string ID berikutnya
 */
function generateNextId($conn, $table, $prefix)
{
    $sql = "SELECT id FROM $table WHERE id LIKE '$prefix%' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $nextNumber = 1;

    if ($result && $row = $result->fetch_assoc()) {
        $lastId = $row['id'];
        $lastNumber = (int) substr($lastId, strlen($prefix));
        $nextNumber = $lastNumber + 1;
    }

    return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Validasi jadwal ujian agar tidak bentrok.
 *
 * @param mysqli $conn
 * @param string $kelasId
 * @param string $jurusanId
 * @param string $mapelId
 * @param string $tanggal
 * @param string $jamMulai
 * @param string $jamSelesai
 * @param string|null $excludeId ID yang sedang diedit (untuk validasi edit)
 * @return array Daftar error
 */
function validateJadwalUjian($conn, $kelasId, $jurusanId, $mapelId, $tanggal, $jamMulai, $jamSelesai, $excludeId = null)
{
    $errors = [];

    // 1. Cek bentrok jadwal di hari yang sama dan kelas yang sama
    $query = "SELECT * FROM jadwal_ujian WHERE kelas_id = ? AND date = ?";
    $params = [$kelasId, $tanggal];
    $types = "ss";

    if ($excludeId !== null) {
        $query .= " AND id != ?";
        $params[] = $excludeId;
        $types .= "s";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($jadwal = $result->fetch_assoc()) {
        // Cek tabrakan waktu
        if (
            ($jamMulai >= $jadwal['jam_mulai'] && $jamMulai < $jadwal['jam_selesai']) ||
            ($jamSelesai > $jadwal['jam_mulai'] && $jamSelesai <= $jadwal['jam_selesai']) ||
            ($jamMulai <= $jadwal['jam_mulai'] && $jamSelesai >= $jadwal['jam_selesai'])
        ) {
            $errors[] = "Terdapat jadwal lain yang bertabrakan di jam tersebut.";
            break;
        }
    }

    // 2. Cek apakah mata pelajaran sudah pernah diujikan untuk kelas+jurusan tersebut
    $query2 = "SELECT COUNT(*) as total FROM jadwal_ujian WHERE kelas_id = ? AND jurusan_id = ? AND mata_pelajaran_id = ?";
    $params2 = [$kelasId, $jurusanId, $mapelId];
    $types2 = "sss";

    if ($excludeId !== null) {
        $query2 .= " AND id != ?";
        $params2[] = $excludeId;
        $types2 .= "s";
    }

    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param($types2, ...$params2);
    $stmt2->execute();
    $stmt2->bind_result($total);
    $stmt2->fetch();

    if ($total > 0) {
        $errors[] = "Mata pelajaran ini sudah pernah dijadwalkan untuk kelas dan jurusan tersebut.";
    }

    return $errors;
}

/**
 * Mengonversi nama hari dalam bahasa Inggris ke Bahasa Indonesia.
 *
 * @param string $dayName Nama hari dalam bahasa Inggris (e.g. 'Monday')
 * @return string Hari dalam Bahasa Indonesia
 */
function translateDayToIndonesian($dayName)
{
    $days = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    return $days[$dayName] ?? $dayName;
}


function getHariIndonesia($tanggal)
{
    $hariInggris = date('l', strtotime($tanggal));
    $map = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];
    return $map[$hariInggris] ?? '';
}

