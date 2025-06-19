<?php
// File: models/JadwalUjianModel.php

class JadwalUjianModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM jadwal_ujian ORDER BY tanggal ASC, jam_mulai ASC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM jadwal_ujian WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai)
    {
        $stmt = $this->conn->prepare("INSERT INTO jadwal_ujian (id, kelas_id, jurusan_id, mata_pelajaran_id, tanggal, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
        return $stmt->execute();
    }

    public function update($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai)
    {
        $stmt = $this->conn->prepare("UPDATE jadwal_ujian SET kelas_id=?, jurusan_id=?, mata_pelajaran_id=?, tanggal=?, hari=?, jam_mulai=?, jam_selesai=? WHERE id=?");
        $stmt->bind_param("ssssssss", $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM jadwal_ujian WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}