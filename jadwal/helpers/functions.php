<?php

/**
 * Redirect ke halaman utama dengan pesan.
 *
 * @param string $message Pesan yang akan ditampilkan.
 * @param string $location Halaman tujuan (default: index.php)
 */
function redirectWithMessage($message) {
    $_SESSION['flash_message'] = $message;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
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
function generateNextId($conn, $table, $prefix) {
    $query = "SELECT id FROM $table WHERE id LIKE ? ORDER BY id DESC LIMIT 1";
    $likePrefix = $prefix . '%';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $likePrefix);
    $stmt->execute();
    $result = $stmt->get_result();
    $lastId = $result->fetch_assoc();

    if ($lastId) {
        // Ambil angka terakhir dari ID dan tambahkan
        $num = intval(substr($lastId['id'], strlen($prefix))) + 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    } else {
        // Jika belum ada ID
        return $prefix . '0001';
    }
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
/**
 * Mengonversi tanggal ke format Indonesia.
 *
 * @param string $tanggal Tanggal dalam format 'Y-m-d'
 * @return string Tanggal dalam format 'd F Y'
 */
