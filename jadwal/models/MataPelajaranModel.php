<?php
class MataPelajaranModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll() {
        $query = "SELECT * FROM mata_pelajaran";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    
        return $rows;
    }
    

    public function getNamaById($id) {
        $stmt = $this->conn->prepare("SELECT nama FROM jurusan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($nama);
        $stmt->fetch();
        $stmt->close();
        return $nama;
    }
    
    public function getById($id)
{
    $stmt = $this->conn->prepare("SELECT * FROM mata_pelajaran WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); // satu baris data
}


    public function create($id, $nama, $kategori)
    {
        $stmt = $this->conn->prepare("INSERT INTO mata_pelajaran (id, nama, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id, $nama, $kategori);
        return $stmt->execute();
    }

    public function update($id, $nama, $kategori)
{
    $stmt = $this->conn->prepare("UPDATE mata_pelajaran SET nama = ?, kategori = ? WHERE id = ?");
    $stmt->bind_param("sss", $nama, $kategori, $id);
    return $stmt->execute();
}


    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM mata_pelajaran WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

 public function getByKategori($kategoriList) {
    // Jika input adalah string, ubah menjadi array
    if (is_string($kategoriList)) {
        $kategoriList = [$kategoriList];
    }

    // Siapkan placeholder untuk prepared statement
    $placeholders = implode(',', array_fill(0, count($kategoriList), '?'));

    $sql = "SELECT * FROM mata_pelajaran WHERE kategori IN ($placeholders)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($kategoriList);
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}



public function getByJurusanId($jurusanId) {
    $stmt = $this->conn->prepare("SELECT * FROM mata_pelajaran WHERE kategori = (
        SELECT nama FROM jurusan WHERE id = ?
    )");
    $stmt->bind_param("s", $jurusanId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

public function getFilteredMataPelajaran($kelasId, $jurusanId) {
    $filtered = [];

    // Ambil nama jurusan
    $stmt = $this->conn->prepare("SELECT nama FROM jurusan WHERE id = ?");
    $stmt->bind_param("s", $jurusanId);
    $stmt->execute();
    $result = $stmt->get_result();
    $jurusanRow = $result->fetch_assoc();
    $stmt->close();

    if ($jurusanRow) {
        $jurusanNama = strtolower(trim($jurusanRow['nama']));
        $kategoriList = ['umum'];
        if ($jurusanNama === 'ipa') {
            $kategoriList[] = 'ipa';
        } elseif ($jurusanNama === 'ips') {
            $kategoriList[] = 'ips';
        }

        // Bangun placeholders
        $placeholders = implode(',', array_fill(0, count($kategoriList), '?'));

        $query = "
            SELECT mp.id, mp.nama, mp.kategori
            FROM mata_pelajaran mp
            WHERE LOWER(mp.kategori) IN ($placeholders)
            AND mp.id NOT IN (
                SELECT mata_pelajaran_id FROM jadwal_ujian
                WHERE kelas_id = ? AND jurusan_id = ?
            )
            ORDER BY mp.nama ASC
        ";

        $stmt = $this->conn->prepare($query);
        $types = str_repeat('s', count($kategoriList)) . "ss";
        $params = array_merge($kategoriList, [$kelasId, $jurusanId]);
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $filtered[] = $row;
        }

        $stmt->close();
    }

    return $filtered;
}












    
    
}
// End of MataPelajaranModel.php
// This model handles operations related to Mata Pelajaran (Subjects) in the application.