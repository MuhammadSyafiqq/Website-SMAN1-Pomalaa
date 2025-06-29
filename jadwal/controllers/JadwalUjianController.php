<?php
require_once __DIR__ . '/../models/JadwalUjianModel.php';

class JadwalUjianController {
    private $model;

    public function __construct($connection) {
        $this->model = new JadwalUjianModel($connection);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store($data) {
        return $this->model->tambah($data);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    public function delete($id) {
        return $this->model->hapus($id);
    }

    public function show($id) {
        return $this->model->getById($id);
    }

    public function getGrouped() {
        return $this->model->getGrouped();
    }

    public function isBentrok($kelas_id, $tanggal, $jam_mulai, $jam_selesai, $ignore_id = null) {
        return $this->model->isBentrok($kelas_id, $tanggal, $jam_mulai, $jam_selesai, $ignore_id);
    }

    public function countByTanggal($kelas_id, $tanggal, $ignore_id = null) {
        return $this->model->countByTanggal($kelas_id, $tanggal, $ignore_id);
    }
}
