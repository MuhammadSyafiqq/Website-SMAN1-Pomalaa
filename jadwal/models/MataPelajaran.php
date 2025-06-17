<?php
class MataPelajaran
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate next ID for mata_pelajaran table with given prefix
     * @param string $prefix
     * @return string
     */
    public function generateNextId(string $prefix = 'MP-'): string
    {
        $query = "SELECT id FROM mata_pelajaran WHERE id LIKE ? ORDER BY CAST(SUBSTRING(id, ?) AS UNSIGNED) DESC LIMIT 1";
        $likePattern = $prefix . '%';
        $prefixLength = strlen($prefix) + 1;

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $likePattern, $prefixLength);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['id'];
            $number = (int)substr($lastId, strlen($prefix));
            $nextNumber = $number + 1;
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
        return $prefix . '001';
    }

    /**
     * Insert new mata_pelajaran record
     * @param string $nama
     * @param string $kategori
     * @return bool|string Returns new ID on success, false on failure
     */
    public function insert(string $nama, string $kategori)
    {
        $newId = $this->generateNextId();

        $stmt = $this->connection->prepare("INSERT INTO mata_pelajaran (id, nama, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $newId, $nama, $kategori);
        if ($stmt->execute()) {
            $stmt->close();
            return $newId;
        }
        $stmt->close();
        return false;
    }

    /**
     * Update existing mata_pelajaran record
     * @param string $id
     * @param string $nama
     * @param string $kategori
     * @return bool
     */
    public function update(string $id, string $nama, string $kategori): bool
    {
        $stmt = $this->connection->prepare("UPDATE mata_pelajaran SET nama = ?, kategori = ? WHERE id = ?");
        $stmt->bind_param("sss", $nama, $kategori, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Delete mata_pelajaran record by ID
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM mata_pelajaran WHERE id = ?");
        $stmt->bind_param("s", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Get all mata_pelajaran records optionally filtered by kategori
     * @param string|null $kategori_filter
     * @return array
     */
    public function getAll(?string $kategori_filter = null): array
    {
        if ($kategori_filter && $kategori_filter !== 'all') {
            $stmt = $this->connection->prepare("SELECT * FROM mata_pelajaran WHERE kategori = ? ORDER BY nama ASC");
            $stmt->bind_param("s", $kategori_filter);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $result = $this->connection->query("SELECT * FROM mata_pelajaran ORDER BY kategori, nama ASC");
        }

        $mataPelajaranList = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $mataPelajaranList[] = $row;
            }
        }
        return $mataPelajaranList;
    }

    /**
     * Get mata_pelajaran grouped by kategori
     * @return array
     */
    public function getByCategory(): array
    {
        $query = "SELECT * FROM mata_pelajaran ORDER BY kategori, nama ASC";
        $result = $this->connection->query($query);

        $mataPelajaranByCategory = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $mataPelajaranByCategory[$row['kategori']][] = $row;
            }
        }
        return $mataPelajaranByCategory;
    }
}
?>
