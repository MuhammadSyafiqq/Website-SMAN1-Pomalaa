<?php
class JadwalUjian
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate next ID for jadwal_ujian table with given prefix
     * @param string $prefix
     * @return string
     */
    public function generateNextId(string $prefix = 'JU-'): string
    {
        $query = "SELECT id FROM jadwal_ujian WHERE id LIKE ? ORDER BY CAST(SUBSTRING(id, ?) AS UNSIGNED) DESC LIMIT 1";
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
     * Insert new jadwal_ujian record
     * @param array $data
     * @return bool|string Returns new ID on success, false on failure
     */
    public function insert(array $data)
    {
        $newId = $this->generateNextId();

        $stmt = $this->connection->prepare("INSERT INTO jadwal_ujian (id, kelas_id, jurusan_id, mata_pelajaran_id, date, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssss",
            $newId,
            $data['kelas_id'],
            $data['jurusan_id'],
            $data['mata_pelajaran_id'],
            $data['date'],
            $data['hari'],
            $data['jam_mulai'],
            $data['jam_selesai']
        );
        if ($stmt->execute()) {
            $stmt->close();
            return $newId;
        }
        $stmt->close();
        return false;
    }

    /**
     * Update existing jadwal_ujian record
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $stmt = $this->connection->prepare("UPDATE jadwal_ujian SET kelas_id = ?, jurusan_id = ?, mata_pelajaran_id = ?, date = ?, hari = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?");
        $stmt->bind_param(
            "ssssssss",
            $data['kelas_id'],
            $data['jurusan_id'],
            $data['mata_pelajaran_id'],
            $data['date'],
            $data['hari'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $id
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Delete jadwal_ujian record by ID
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM jadwal_ujian WHERE id = ?");
        $stmt->bind_param("s", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Get all jadwal_ujian records with related data joined
     * @return array
     */
    public function getAll(): array
    {
        $query = "
            SELECT 
                ju.id,
                ju.kelas_id,
                ju.jurusan_id,
                ju.mata_pelajaran_id,
                k.nama AS kelas,
                j.nama AS jurusan,
                mp.nama AS mata_pelajaran,
                ju.date,
                ju.hari,
                ju.jam_mulai,
                ju.jam_selesai
            FROM
                jadwal_ujian ju
            JOIN kelas k ON ju.kelas_id = k.id
            JOIN jurusan j ON ju.jurusan_id = j.id
            JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
            ORDER BY ju.date, ju.jam_mulai;
        ";
        $result = $this->connection->query($query);
        $jadwalList = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $jadwalList[] = $row;
            }
        }
        return $jadwalList;
    }

    /**
     * Validate jadwal_ujian data before insert/update
     * @param array $data
     * @param string|null $excludeId Exclude this ID from conflict checks (for updates)
     * @return array Array of error messages; empty if no errors
     */
    public function validate(array $data, ?string $excludeId = null): array
    {
        $errors = [];

        // 1. Validate jam_mulai < jam_selesai
        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            $errors[] = "Jam mulai harus lebih kecil dari jam selesai.";
            // No need to continue other validations if time invalid
            return $errors;
        }

        // 2. Check mata_pelajaran uniqueness for kelas and jurusan
        $query = "SELECT COUNT(*) AS count FROM jadwal_ujian WHERE kelas_id = ? AND jurusan_id = ? AND mata_pelajaran_id = ?";
        if ($excludeId) {
            $query .= " AND id != ?";
        }
        $stmt = $this->connection->prepare($query);
        if ($excludeId) {
            $stmt->bind_param("ssss", $data['kelas_id'], $data['jurusan_id'], $data['mata_pelajaran_id'], $excludeId);
        } else {
            $stmt->bind_param("sss", $data['kelas_id'], $data['jurusan_id'], $data['mata_pelajaran_id']);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] > 0) {
            $errors[] = "Mata pelajaran ini sudah dijadwalkan untuk kelas dan jurusan yang dipilih.";
        }

        // 3. Check maximum 6 jadwal per hari
        $query_day_limit = "SELECT COUNT(*) as total_schedules FROM jadwal_ujian WHERE hari = ?";
        if ($excludeId) {
            $query_day_limit .= " AND id != ?";
        }
        $stmt = $this->connection->prepare($query_day_limit);
        if ($excludeId) {
            $stmt->bind_param("ss", $data['hari'], $excludeId);
        } else {
            $stmt->bind_param("s", $data['hari']);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $dayRow = $result->fetch_assoc();
        $stmt->close();

        if ($dayRow['total_schedules'] >= 6) {
            $errors[] = "Hari {$data['hari']} sudah memiliki 6 jadwal ujian (maksimal).";
        }

        return $errors;
    }
}
?>

