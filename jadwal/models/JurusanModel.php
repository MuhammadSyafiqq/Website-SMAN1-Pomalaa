<?php
class JurusanModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM jurusan ORDER BY nama ASC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM jurusan WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($id, $nama)
    {
        $stmt = $this->conn->prepare("INSERT INTO jurusan (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $nama);
        return $stmt->execute();
    }

    public function update($id, $nama)
    {
        $stmt = $this->conn->prepare("UPDATE jurusan SET nama = ? WHERE id = ?");
        $stmt->bind_param("ss", $nama, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM jurusan WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function getByKelasJurusan($kelasId, $jurusanId) {
    $query = "
        SELECT j.*, k.nama AS kelas_nama, jur.nama AS jurusan_nama, mp.nama AS mapel_nama
        FROM jadwal_ujian j
        JOIN kelas k ON j.kelas_id = k.id
        JOIN jurusan jur ON j.jurusan_id = jur.id
        JOIN mata_pelajaran mp ON j.mata_pelajaran_id = mp.id
        WHERE j.kelas_id = ? AND j.jurusan_id = ?
        ORDER BY j.tanggal, j.jam_mulai
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ss", $kelasId, $jurusanId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    
}
