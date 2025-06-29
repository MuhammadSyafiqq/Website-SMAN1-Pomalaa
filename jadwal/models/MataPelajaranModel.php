<?php
class MataPelajaranModel {
    private $conn;
    private $table = 'mata_pelajaran';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    private function generateId() {
        $query = "SELECT MAX(RIGHT(id, 3)) AS max_id FROM {$this->table}";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $max = $row['max_id'] ?? '000';
        $next = (int)$max + 1;
        return 'MP-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function tambahMataPelajaran($nama, $kategori) {
        $id = $this->generateId();
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id, nama, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id, $nama, $kategori);
        return $stmt->execute();
    }

<<<<<<< Updated upstream
    public function update($id, $nama, $kategori)
{
    $stmt = $this->conn->prepare("UPDATE mata_pelajaran SET nama = ?, kategori = ? WHERE id = ?");
    $stmt->bind_param("sss", $nama, $kategori, $id);
    return $stmt->execute();
}

=======
    public function updateMataPelajaran($id, $nama, $kategori) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nama = ?, kategori = ? WHERE id = ?");
        $stmt->bind_param("sss", $nama, $kategori, $id);
        return $stmt->execute();
    }
>>>>>>> Stashed changes

    public function hapusMataPelajaran($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM {$this->table} ORDER BY nama ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getByKategori($kategori) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE kategori = ? ORDER BY nama ASC");
        $stmt->bind_param("s", $kategori);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
