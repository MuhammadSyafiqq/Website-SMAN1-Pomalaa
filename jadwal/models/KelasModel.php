<?php
class KelasModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM kelas ORDER BY nama ASC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($id, $nama)
    {
        $stmt = $this->conn->prepare("INSERT INTO kelas (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $nama);
        return $stmt->execute();
    }

    public function update($id, $nama)
    {
        $stmt = $this->conn->prepare("UPDATE kelas SET nama = ? WHERE id = ?");
        $stmt->bind_param("ss", $nama, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM kelas WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}
