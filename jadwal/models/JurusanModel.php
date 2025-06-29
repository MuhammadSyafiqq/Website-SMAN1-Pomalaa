<?php
class JurusanModel {
    private $conn;
    private $table = 'jurusan';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    private function generateId() {
        $query = "SELECT MAX(RIGHT(id, 3)) AS max_id FROM {$this->table}";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $max = $row['max_id'] ?? '000';
        $next = (int)$max + 1;
        return 'JR-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function tambahJurusan($nama) {
        $id = $this->generateId();
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $nama);
        return $stmt->execute();
    }

    public function updateJurusan($id, $nama) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nama = ? WHERE id = ?");
        $stmt->bind_param("ss", $nama, $id);
        return $stmt->execute();
    }

    public function hapusJurusan($id) {
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
}
