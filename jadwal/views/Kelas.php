<?php

require_once __DIR__ . '/../controllers/KelasController.php';

$kelasController = new KelasController();
$result = $kelasController->getAllKelas();
$kelasList = $result['success'] ? $result['data'] : [];

// Mendapatkan pesan dari session jika ada
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Judul dan Pesan -->
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Manajemen Kelas</h1>
    
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?= $messageType === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form Tambah Kelas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Kelas Baru</h2>
            
            <form id="formTambahKelas" method="POST" action="/Website-SMAN1-Pomalaa/jadwal/contrlollers/KelasController.php?action=addKelas">
                <div class="mb-4">
                    <label for="kelas_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" id="kelas_nama" name="kelas_nama" 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none"
                           placeholder="Contoh: X IPA 1" maxlength="10" required>
                    <p class="mt-1 text-sm text-gray-500">Masukkan nama kelas (contoh: X, XI IPA, XII IPS)</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="add_kelas"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Tambah Kelas
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Kelas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Kelas</h2>
                <div class="relative">
                    <input type="text" placeholder="Cari kelas..." 
                           class="pl-8 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none"
                           id="searchKelas">
                    <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

            <?php if (empty($kelasList)): ?>
                <div class="text-center py-8 text-gray-500">
                    Tidak ada data kelas yang tersedia
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($kelasList as $kelas): ?>
                                <tr class="hover:bg-gray-50" id="kelas-<?= $kelas['id'] ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($kelas['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($kelas['nama']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openEditModal('<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Edit
                                        </button>
                                        <button onclick="confirmDelete('<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')"
                                                class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit Kelas</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="formEditKelas" method="POST" action="/controllers/KelasController.php?action=update">
                <input type="hidden" id="edit_id" name="edit_id">
                
                <div class="mb-4">
                    <label for="edit_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" id="edit_nama" name="edit_nama"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none"
                           maxlength="10" required>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" name="edit_kelas"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="text-gray-700 mb-6">Anda yakin ingin menghapus kelas <span id="kelasToDeleteName" class="font-semibold"></span>?</p>
            <p class="text-sm text-red-600 mb-6">*Data yang sudah dihapus tidak dapat dikembalikan</p>
            
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <form id="formDeleteKelas" method="POST" action="/controllers/KelasController.php?action=delete">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <button type="submit"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mencari kelas
    document.getElementById('searchKelas').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const namaKelas = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (namaKelas.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
 }
        });
    });

    // Fungsi untuk membuka modal edit
    function openEditModal(id, nama) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('editModal').classList.remove('hidden');
    }

    // Fungsi untuk menutup modal edit
    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Fungsi untuk membuka modal konfirmasi hapus
    function confirmDelete(id, nama) {
        document.getElementById('delete_id').value = id;
        document.getElementById('kelasToDeleteName').textContent = nama;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    // Fungsi untuk menutup modal konfirmasi hapus
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Event listener untuk form tambah
    document.getElementById('formTambahKelas').addEventListener('submit', function(e) {
        const namaKelas = document.getElementById('kelas_nama').value.trim();
        if (!namaKelas) {
            e.preventDefault();
            alert('Nama kelas harus diisi');
        }
    });

    // Event listener untuk form edit
    document.getElementById('formEditKelas').addEventListener('submit', function(e) {
        const namaKelas = document.getElementById('edit_nama').value.trim();
        if (!namaKelas) {
            e.preventDefault();
            alert('Nama kelas harus diisi');
        }
    });
</script>

