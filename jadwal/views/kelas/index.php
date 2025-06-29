
<div class="content-header">
    <h1>Daftar Kelas</h1>
    <a href="/kelas/create" class="btn btn-primary">Tambah Kelas</a>
</div>

<div class="content">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kelasList as $kelas): ?>
                <tr>
                    <td><?= htmlspecialchars($kelas['id']) ?></td>
                    <td><?= htmlspecialchars($kelas['nama']) ?></td>
                    <td>
                        <a href="/kelas/edit/<?= $kelas['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="/kelas/delete/<?= $kelas['id'] ?>" method="POST" style="display:inline;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

