<?php
class Kelas
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate next ID for kelas table with given prefix
     * @param string $prefix
     * @return string
     */
    public function generateNextId(string $prefix = 'KL-'): string
    {
        $query = "SELECT id FROM kelas WHERE id LIKE ? ORDER BY CAST(SUBSTRING(id, ?) AS UNSIGNED) DESC LIMIT 1";
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
     * Insert new kelas record
     * @param string $nama
     * @return bool|string Returns new ID on success, false on failure
     */
    public function insert(string $nama)
    {
        $newId = $this->generateNextId();

        $stmt = $this->connection->prepare("INSERT INTO kelas (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $newId, $nama);
        if ($stmt->execute()) {
            $stmt->close();
            return $newId;
        }
        $stmt->close();
        return false;
    }

    /**
     * Update existing kelas record
     * @param string $id
     * @param string $nama
     * @return bool
     */
    public function update(string $id, string $nama): bool
    {
        $stmt = $this->connection->prepare("UPDATE kelas SET nama = ? WHERE id = ?");
        $stmt->bind_param("ss", $nama, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Delete kelas record by ID
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM kelas WHERE id = ?");
        $stmt->bind_param("s", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Get all kelas records
     * @return array
     */
    public function getAll(): array
    {
        $query = "SELECT * FROM kelas ORDER BY nama ASC";
        $result = $this->connection->query($query);
        $kelasList = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $kelasList[] = $row;
            }
        }
        return $kelasList;
    }
}
?>
