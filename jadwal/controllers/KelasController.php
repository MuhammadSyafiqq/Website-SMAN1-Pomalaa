<?php
require_once __DIR__ . '/../models/KelasModel.php';

class KelasController {
    private $model;

    public function __construct($connection) {
        $this->model = new KelasModel($connection);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store($nama) {
        return $this->model->tambahKelas($nama);
    }
    
    public function update($id, $nama) {
        return $this->model->updateKelas($id, $nama);
    }

    public function delete($id) {
        return $this->model->hapusKelas($id);
    }

    public function show($id) {
        return $this->model->getById($id);
    }
}
