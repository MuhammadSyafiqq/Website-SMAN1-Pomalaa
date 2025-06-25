<?php
class MataPelajaranModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM mata_pelajaran ORDER BY nama ASC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM mata_pelajaran WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
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

    public function getByKategori($kategori)
    {
        $stmt = $this->conn->prepare("SELECT * FROM mata_pelajaran WHERE kategori = ?");
        $stmt->bind_param("s", $kategori);
        $stmt->execute();
        return $stmt->get_result();
    }
}
// End of MataPelajaranModel.php
// This model handles operations related to Mata Pelajaran (Subjects) in the application.