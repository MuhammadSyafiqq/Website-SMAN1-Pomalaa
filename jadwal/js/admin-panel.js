document.addEventListener("DOMContentLoaded", function () {
  const kelasSelect = document.getElementById("kelas_id");
  const jurusanSelect = document.getElementById("jurusan_id");
  const mataPelajaranSelect = document.getElementById("mata_pelajaran_id");

  // Data mata pelajaran dikategorikan berdasarkan kategori
  const allMataPelajaran = JSON.parse(
    document.getElementById("allMataPelajaranData").textContent
  );

  function updateMataPelajaranOptions() {
    const selectedKelas =
      kelasSelect.options[kelasSelect.selectedIndex].text.toLowerCase();
    const selectedJurusan =
      jurusanSelect.options[jurusanSelect.selectedIndex]?.text.toLowerCase();

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

  kelasSelect.addEventListener("change", updateMataPelajaranOptions);
  jurusanSelect.addEventListener("change", updateMataPelajaranOptions);

  // Mengisi otomatis hari dari tanggal yang dipilih
  const tanggalInput = document.getElementById("date");
  tanggalInput?.addEventListener("change", function () {
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

  // Konfirmasi Hapus
  window.confirmDelete = function (type, id, name) {
    if (confirm(`Yakin ingin menghapus ${type} '${name}'?`)) {
      window.location.href = `?delete_${type}=${id}`;
    }
  };

  // Prefill Edit
  window.editKelas = function (id, nama) {
    const input = document.getElementById("kelas_nama");
    input.value = nama;
    input.focus();
    input.form.action = `?edit_kelas=${id}`;
  };

  window.editJurusan = function (id, nama) {
    const input = document.getElementById("jurusan_nama");
    input.value = nama;
    input.focus();
    input.form.action = `?edit_jurusan=${id}`;
  };

  window.editMataPelajaran = function (id, nama, kategori) {
    const namaInput = document.getElementById("mata_pelajaran_nama");
    const kategoriSelect = document.getElementById("kategori");
    namaInput.value = nama;
    kategoriSelect.value = kategori;
    namaInput.focus();
    namaInput.form.action = `?edit_mata_pelajaran=${id}`;
  };
});
