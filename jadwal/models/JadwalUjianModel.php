<?php
class JadwalUjianModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll()
    {
        $sql = "SELECT ju.*, 
                       k.nama as kelas_nama,
                       j.nama as jurusan_nama,
                       mp.nama as mata_pelajaran_nama
                FROM jadwal_ujian ju
                JOIN kelas k ON ju.kelas_id = k.id
                JOIN jurusan j ON ju.jurusan_id = j.id
                JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
                ORDER BY ju.tanggal ASC, ju.jam_mulai ASC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT ju.*, 
                                            k.nama as kelas_nama,
                                            j.nama as jurusan_nama,
                                            mp.nama as mata_pelajaran_nama
                                     FROM jadwal_ujian ju
                                     JOIN kelas k ON ju.kelas_id = k.id
                                     JOIN jurusan j ON ju.jurusan_id = j.id
                                     JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
                                     WHERE ju.id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($id, $kelas_id, $jurusan_id, $mata_pelajaran_id, $tanggal, $hari, $jam_mulai, $jam_selesai)
    {
        $stmt = $this->conn->prepare("INSERT INTO jadwal_ujian (id, kelas_id, jurusan_id, mata_pelajaran_id, tanggal, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $id, $kelas_id, $jurusan_id, $mata_pelajaran_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
        return $stmt->execute();
    }

    public function update($id, $tanggal, $hari, $jam_mulai, $jam_selesai)
    {
        $stmt = $this->conn->prepare("UPDATE jadwal_ujian SET tanggal = ?, hari = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?");
        $stmt->bind_param("sssss", $tanggal, $hari, $jam_mulai, $jam_selesai, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM jadwal_ujian WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function getByKelas($kelas_id)
    {
        $stmt = $this->conn->prepare("SELECT ju.*, 
                                            k.nama as kelas_nama,
                                            j.nama as jurusan_nama,
                                            mp.nama as mata_pelajaran_nama
                                     FROM jadwal_ujian ju
                                     JOIN kelas k ON ju.kelas_id = k.id
                                     JOIN jurusan j ON ju.jurusan_id = j.id
                                     JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
                                     WHERE ju.kelas_id = ?
                                     ORDER BY ju.tanggal ASC, ju.jam_mulai ASC");
        $stmt->bind_param("s", $kelas_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getByTanggal($tanggal)
    {
        $stmt = $this->conn->prepare("SELECT ju.*, 
                                            k.nama as kelas_nama,
                                            j.nama as jurusan_nama,
                                            mp.nama as mata_pelajaran_nama
                                     FROM jadwal_ujian ju
                                     JOIN kelas k ON ju.kelas_id = k.id
                                     JOIN jurusan j ON ju.jurusan_id = j.id
                                     JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
                                     WHERE ju.tanggal = ?
                                     ORDER BY ju.jam_mulai ASC");
        $stmt->bind_param("s", $tanggal);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getByKelasJurusan($kelas_id, $jurusan_id)
    {
        $stmt = $this->conn->prepare("SELECT ju.*, 
                                            k.nama as kelas_nama,
                                            j.nama as jurusan_nama,
                                            mp.nama as mata_pelajaran_nama
                                     FROM jadwal_ujian ju
                                     JOIN kelas k ON ju.kelas_id = k.id
                                     JOIN jurusan j ON ju.jurusan_id = j.id
                                     JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
                                     WHERE ju.kelas_id = ? AND ju.jurusan_id = ?
                                     ORDER BY ju.tanggal ASC, ju.jam_mulai ASC");
        $stmt->bind_param("ss", $kelas_id, $jurusan_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
