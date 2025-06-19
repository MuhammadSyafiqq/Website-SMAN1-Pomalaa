<?php
// File: controllers/JadwalUjianController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/JadwalUjianModel.php';

$model = new JadwalUjianModel($connection);

if (isset($_POST['add_jadwal'])) {
    $id = generateNextId($connection, 'jadwal_ujian', 'JU-');
    $kelas_id = $_POST['kelas'];
    $jurusan_id = $_POST['jurusan'];
    $mapel_id = $_POST['mapel'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $hari = getHariIndonesia($tanggal);

    $model->create($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
    redirectWithMessage("Jadwal ujian berhasil ditambahkan.");
}

if (isset($_POST['edit_jadwal'])) {
    $id = $_POST['edit_id'];
    $kelas_id = $_POST['kelas'];
    $jurusan_id = $_POST['jurusan'];
    $mapel_id = $_POST['mapel'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $hari = getHariIndonesia($tanggal);

    $model->update($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
    redirectWithMessage("Jadwal ujian berhasil diupdate.");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $model->delete($id);
    redirectWithMessage("Jadwal ujian berhasil dihapus.");
}