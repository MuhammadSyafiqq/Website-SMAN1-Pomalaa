document.addEventListener("DOMContentLoaded", function () {
  const kelasSelect = document.getElementById("kelas_id");
  const jurusanSelect = document.getElementById("jurusan_id");
  const mataPelajaranSelect = document.getElementById("mata_pelajaran_id");

  const allMataPelajaran = JSON.parse(
    document.getElementById("allMataPelajaranData").textContent
  );

  function updateMataPelajaranOptions() {
    const selectedKelas =
      kelasSelect?.options[kelasSelect.selectedIndex]?.text.toLowerCase();
    const selectedJurusan =
      jurusanSelect?.options[jurusanSelect.selectedIndex]?.text.toLowerCase();

    mataPelajaranSelect.innerHTML =
      '<option value="">-- Pilih Mata Pelajaran --</option>';

    allMataPelajaran.forEach((mp) => {
      if (
        selectedKelas === "x" ||
        mp.kategori === "umum" ||
        mp.kategori === selectedJurusan
      ) {
        const option = document.createElement("option");
        option.value = mp.id;
        option.text = mp.nama;
        mataPelajaranSelect.appendChild(option);
      }
    });
  }

  kelasSelect?.addEventListener("change", updateMataPelajaranOptions);
  jurusanSelect?.addEventListener("change", updateMataPelajaranOptions);

  // Auto isi hari dari tanggal
  document.getElementById("date")?.addEventListener("change", function () {
    const tanggal = new Date(this.value);
    const hariInput = document.getElementById("hari");
    if (hariInput) {
      const hariList = [
        "Minggu",
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jumat",
        "Sabtu",
      ];
      hariInput.value = hariList[tanggal.getDay()];
    }
  });

  // =========== FUNGSI AKSI EDIT DAN DELETE ============= //

  // Notifikasi helper
  function showNotif(message, isSuccess = true) {
    alert((isSuccess ? "✅ Berhasil: " : "❌ Gagal: ") + message);
  }

  // Konfirmasi dan hapus dat

  function confirmDelete(type, id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus ${type} "${nama}"?`)) {
      const formData = new FormData();
      formData.append("action", `delete_${type}`);
      formData.append("id", id);

      fetch("handlers/handler_request.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          showNotif(data.message, data.success); // Tampilkan pesan sukses atau gagal
          if (data.success) {
            location.reload(); // Reload hanya jika berhasil
          }
        })
        .catch(() => {
          alert("Terjadi kesalahan koneksi ke server.");
        });
    }
  }

  // Edit Kelas
  window.editKelas = function (id, nama) {
    const newNama = prompt(`Edit nama kelas (sebelumnya: ${nama})`, nama);
    if (newNama) {
      fetch("handlers/handle_request.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=edit_kelas&id=${encodeURIComponent(
          id
        )}&nama=${encodeURIComponent(newNama)}`,
      })
        .then((res) => res.json())
        .then((data) => {
          showNotif(data.message, data.success);
          if (data.success) location.reload();
        })
        .catch(() => showNotif("Gagal mengedit kelas.", false));
    }
  };

  // Edit Jurusan
  window.editJurusan = function (id, nama) {
    const newNama = prompt(`Edit nama jurusan (sebelumnya: ${nama})`, nama);
    if (newNama) {
      fetch("handlers/handle_request.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=edit_jurusan&id=${encodeURIComponent(
          id
        )}&nama=${encodeURIComponent(newNama)}`,
      })
        .then((res) => res.json())
        .then((data) => {
          showNotif(data.message, data.success);
          if (data.success) location.reload();
        })
        .catch(() => showNotif("Gagal mengedit jurusan.", false));
    }
  };

  // Edit Mata Pelajaran
  window.editMataPelajaran = function (id, nama, kategori) {
    const newNama = prompt(
      `Edit nama mata pelajaran (sebelumnya: ${nama})`,
      nama
    );
    const newKategori = prompt(
      `Edit kategori (umum/ipa/ips) (sebelumnya: ${kategori})`,
      kategori
    );
    if (newNama && newKategori) {
      fetch("handlers/handle_request.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=edit_mapel&id=${encodeURIComponent(
          id
        )}&nama=${encodeURIComponent(newNama)}&kategori=${encodeURIComponent(
          newKategori
        )}`,
      })
        .then((res) => res.json())
        .then((data) => {
          showNotif(data.message, data.success);
          if (data.success) location.reload();
        })
        .catch(() => showNotif("Gagal mengedit mata pelajaran.", false));
    }
  };

  // Edit Jadwal Ujian
  window.editJadwal = function (
    id,
    kelas_id,
    jurusan_id,
    mata_pelajaran_id,
    tanggal,
    jam_mulai,
    jam_selesai
  ) {
    document.getElementById("kelas_id").value = kelas_id;
    document.getElementById("jurusan_id").value = jurusan_id;
    document.getElementById("mata_pelajaran_id").value = mata_pelajaran_id;
    document.getElementById("date").value = tanggal;
    document.getElementById("jam_mulai").value = jam_mulai;
    document.getElementById("jam_selesai").value = jam_selesai;
    document.getElementById("jadwal_form").dataset.editId = id;

    updateMataPelajaranOptions();
  };

  // Submit form jadwal ujian
  const jadwalForm = document.getElementById("jadwal_form");
  if (jadwalForm) {
    jadwalForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(jadwalForm);
      const id = jadwalForm.dataset.editId;
      if (id) {
        formData.append("action", "edit_jadwal");
        formData.append("id", id);
      } else {
        formData.append("action", "add_jadwal");
      }

      fetch("handlers/handle_request.php", {
        method: "POST",
        body: new URLSearchParams(formData),
      })
        .then((res) => res.json())
        .then((data) => {
          showNotif(data.message, data.success);
          if (data.success) location.reload();
        })
        .catch(() => showNotif("Gagal menyimpan jadwal.", false));
    });
  }
});

// ============ EDIT KELAS ============
function editKelas(id, nama) {
  document.getElementById("kelas_id").value = id;
  document.getElementById("kelas_nama").value = nama;
  document.getElementById("kelas_action").value = "edit_kelas";
}

// ============ EDIT JURUSAN ============
function editJurusan(id, nama) {
  document.getElementById("jurusan_id").value = id;
  document.getElementById("jurusan_nama").value = nama;
  document.getElementById("jurusan_action").value = "edit_jurusan";
}

// ============ EDIT MATA PELAJARAN ============
function editMataPelajaran(id, nama, kategori) {
  document.getElementById("mata_pelajaran_id").value = id;
  document.getElementById("mata_pelajaran_nama").value = nama;
  document.getElementById("kategori").value = kategori;
  document.getElementById("mata_pelajaran_action").value =
    "edit_mata_pelajaran";
}

// ============ DELETE KONFIRMASI ============
function confirmDelete(type, id, nama) {
  if (confirm(`Apakah Anda yakin ingin menghapus ${type} "${nama}"?`)) {
    const formData = new FormData();
    formData.append("action", `delete_${type}`);
    formData.append("id", id);

    fetch("handlers/handler_request.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then(() => location.reload());
  }
}

// ============ AUTO UPDATE HARI ============
document.getElementById("date").addEventListener("change", function () {
  const tanggal = new Date(this.value);
  const options = { weekday: "long" };
  const hari = tanggal.toLocaleDateString("id-ID", options);
  document.getElementById("hari").value = hari;
});

// ============ FILTER MATA PELAJARAN BERDASARKAN JURUSAN ============
document.getElementById("jurusan_id").addEventListener("change", function () {
  const jurusan = this.options[this.selectedIndex].text.toLowerCase();
  const semuaMapel = JSON.parse(
    document.getElementById("allMataPelajaranData").textContent
  );
  const select = document.getElementById("mata_pelajaran_id");

  select.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
  semuaMapel.forEach((mp) => {
    if (mp.kategori === "umum" || mp.kategori === jurusan) {
      const opt = document.createElement("option");
      opt.value = mp.id;
      opt.textContent = mp.nama;
      select.appendChild(opt);
    }
  });
});
