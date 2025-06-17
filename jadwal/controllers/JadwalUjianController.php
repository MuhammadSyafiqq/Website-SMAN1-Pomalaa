<?php
require_once '../config/database.php';
require_once '../models/JadwalUjian.php'

// Initialize messages
$message = "";

// Handle jadwal_ujian insertion
if (isset($_POST['add_jadwal_ujian'])) {
    $kelas_id = $_POST['kelas_id'];
    $jurusan_id = $_POST['jurusan_id'];
    $mp_id = $_POST['mata_pelajaran_id'];
    $date = $_POST['date'];
    $hari = date('l', strtotime($date));
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    if ($kelas_id && $jurusan_id && $mp_id && $date && $jam_mulai && $jam_selesai) {
        // Validasi jam mulai harus lebih kecil dari jam selesai
        if ($jam_mulai >= $jam_selesai) {
            $message = "Jam mulai harus lebih kecil dari jam selesai.";
        } else {
            // Gunakan fungsi validasi yang lebih lengkap
            $validation_errors = validateJadwalUjian($connection, $kelas_id, $jurusan_id, $mp_id, $date, $jam_mulai, $jam_selesai);
            
            if (!empty($validation_errors)) {
                $message = "Gagal menambahkan jadwal ujian: " . implode(" ", $validation_errors);
            } else {
                // Check if mata_pelajaran_id exists
                $stmt_check_mp = $connection->prepare("SELECT id FROM mata_pelajaran WHERE id = ?");
                $stmt_check_mp->bind_param("s", $mp_id);
                $stmt_check_mp->execute();
                $result_check_mp = $stmt_check_mp->get_result();
                
                if ($result_check_mp->num_rows === 0) {
                    $message = "Error: Mata pelajaran yang dipilih tidak ditemukan.";
                } else {
                    // Generate ID otomatis untuk jadwal ujian
                    $new_ju_id = generateNextId($connection, 'jadwal_ujian', 'JU-');
                    
                    // Insert jadwal ujian
                    $stmt = $connection->prepare("INSERT INTO jadwal_ujian (id, kelas_id, jurusan_id, mata_pelajaran_id, date, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $new_ju_id, $kelas_id, $jurusan_id, $mp_id, $date, $hari, $jam_mulai, $jam_selesai);
                    
                    if ($stmt->execute()) {
                        $message = "Jadwal Ujian berhasil ditambahkan dengan ID: $new_ju_id";
                    } else {
                        $message = "Gagal menambahkan Jadwal Ujian: " . $connection->error;
                    }
                    $stmt->close();
                }
                $stmt_check_mp->close();
            }
        }
    } else {
        $message = "Semua field harus diisi.";
    }
    redirectWithMessage($message);
}

if (isset($_POST['edit_jadwal_ujian'])) {
    $id = $_POST['edit_id'];
    $kelas_id = $_POST['edit_kelas_id'];
    $jurusan_id = $_POST['edit_jurusan_id'];
    $mata_pelajaran_id = $_POST['edit_mata_pelajaran_id'];
    $date = $_POST['edit_date'];
    $hari = date('l', strtotime($date));
    $jam_mulai = $_POST['edit_jam_mulai'];
    $jam_selesai = $_POST['edit_jam_selesai'];
    // Debugging output
    error_log("Editing Jadwal Ujian: ID: $id, Kelas ID: $kelas_id, Jurusan ID: $jurusan_id, Mata Pelajaran ID: $mata_pelajaran_id, Date: $date, Jam Mulai: $jam_mulai, Jam Selesai: $jam_selesai");
    // Validasi jam mulai harus lebih kecil dari jam selesai
    if ($jam_mulai >= $jam_selesai) {
        $message = "Jam mulai harus lebih kecil dari jam selesai.";
    } else {
        // Use validation function with exclude current id
        $validation_errors = validateJadwalUjian($connection, $kelas_id, $jurusan_id, $mata_pelajaran_id, $date, $jam_mulai, $jam_selesai, $id);
        
        if (!empty($validation_errors)) {
            $message = "Gagal mengupdate jadwal ujian: " . implode(" ", $validation_errors);
        } else {
            $stmt = $connection->prepare("UPDATE jadwal_ujian SET kelas_id = ?, jurusan_id = ?, mata_pelajaran_id = ?, date = ?, hari = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?");
            $stmt->bind_param("ssssssss", $kelas_id, $jurusan_id, $mata_pelajaran_id, $date, $hari, $jam_mulai, $jam_selesai, $id);
            
            if ($stmt->execute()) {
                $message = "Jadwal Ujian berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate jadwal ujian: " . $connection->error;
            }
            $stmt->close();
        }
    }
    redirectWithMessage($message);
}

if (isset($_GET['delete'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    if (in_array($table, ['kelas', 'jurusan', 'mata_pelajaran', 'jadwal_ujian'])) {
        $stmt = $connection->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $message = ucfirst($table) . " berhasil dihapus.";
        } else {
            $message = "Gagal menghapus " . $table . ": " . $connection->error;
        }
        $stmt->close();
    }
     redirectWithMessage($message);
}

function redirectWithMessage($message) {
    session_start();
    $_SESSION['message'] = $message;
    
    // Get current script name without query parameters
    $current_url = strtok($_SERVER['REQUEST_URI'], '?');
    
    // Redirect to clean URL
    header("Location: " . $current_url);
    exit();
}

?>