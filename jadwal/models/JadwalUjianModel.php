<?php
class JadwalUjianModel {
    private $conn;
    private $table = 'jadwal_ujian';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    private function generateId() {
        $query = "SELECT MAX(RIGHT(id, 3)) AS max_id FROM {$this->table}";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $max = $row['max_id'] ?? '000';
        $next = (int)$max + 1;
        return 'JU-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function getAll() {
        $query = "SELECT j.*, 
                         k.nama AS kelas_nama, 
                         jr.nama AS jurusan_nama, 
                         mp.nama AS mata_pelajaran_nama
                  FROM {$this->table} j
                  JOIN kelas k ON j.kelas_id = k.id
                  JOIN jurusan jr ON j.jurusan_id = jr.id
                  JOIN mata_pelajaran mp ON j.mata_pelajaran_id = mp.id
                  ORDER BY j.tanggal ASC, j.jam_mulai ASC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function tambah($data) {
        $id = $this->generateId();
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (id, kelas_id, jurusan_id, mata_pelajaran_id, tanggal, hari, jam_mulai, jam_selesai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $id, $data['kelas_id'], $data['jurusan_id'], $data['mata_pelajaran_id'], $data['tanggal'], $data['hari'], $data['jam_mulai'], $data['jam_selesai']);
        return $stmt->execute();
    }

    public function hapus($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} 
            SET kelas_id = ?, jurusan_id = ?, mata_pelajaran_id = ?, tanggal = ?, hari = ?, jam_mulai = ?, jam_selesai = ? 
            WHERE id = ?");
        $stmt->bind_param("ssssssss", $data['kelas_id'], $data['jurusan_id'], $data['mata_pelajaran_id'], $data['tanggal'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id);
        return $stmt->execute();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function getByTanggalKelasJurusan($tanggal, $kelas_id, $jurusan_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE tanggal = ? AND kelas_id = ? AND jurusan_id = ?");
        $stmt->bind_param("sss", $tanggal, $kelas_id, $jurusan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
