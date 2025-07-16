document.addEventListener("DOMContentLoaded", function () {
  const kelasSelect = document.getElementById("kelas_id");
  const jurusanSelect = document.getElementById("jurusan_id");
  const mataPelajaranSelect = document.getElementById("mata_pelajaran_id");
  const tanggalInput = document.getElementById("tanggal_input");
  const hariInput = document.getElementById("hari_input");
  const toggleButtons = document.querySelectorAll(".toggle-table");

  // Toggle table visibility
  toggleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const targetId = button.getAttribute("data-target");
      const table = document.getElementById(targetId);

      if (table) {
        if (table.style.display === "none" || table.style.display === "") {
          table.style.display = "table";
          button.textContent = "Sembunyikan Data";
        } else {
          table.style.display = "none";
          button.textContent = "Lihat Data";
        }
      } else {
        console.warn(`Tabel dengan ID ${targetId} tidak ditemukan`);
      }
    });
  });

  // Update mata pelajaran options based on kelas and jurusan
  function updateMataPelajaranOptions() {
    if (!kelasSelect || !jurusanSelect || !mataPelajaranSelect) {
      console.warn("Elemen select tidak ditemukan di halaman");
      return;
    }

    const kelasId = kelasSelect.value.trim();
    const jurusanId = jurusanSelect.value.trim();

    console.log("📝 Kelas ID:", kelasId);
    console.log("📝 Jurusan ID:", jurusanId);

    if (!kelasId || !jurusanId) {
      mataPelajaranSelect.innerHTML =
        '<option value="">-- Pilih Mata Pelajaran --</option>';
      console.warn("Kelas atau jurusan belum dipilih");
      return;
    }

    const url = `handlers/handle_request.php?ajax=filter_mata_pelajaran&kelas_id=${encodeURIComponent(
      kelasId
    )}&jurusan_id=${encodeURIComponent(jurusanId)}`;

    console.log("🌐 Fetch URL:", url);

    mataPelajaranSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(url)
      .then((response) => {
        if (!response.ok) throw new Error("HTTP error " + response.status);
        return response.json();
      })
      .then((data) => {
        console.log("✅ Data mapel diterima:", data);
        mataPelajaranSelect.innerHTML =
          '<option value="">-- Pilih Mata Pelajaran --</option>';

        if (!Array.isArray(data) || data.length === 0) {
          const option = document.createElement("option");
          option.value = "";
          option.textContent = "Tidak ada mata pelajaran tersedia";
          mataPelajaranSelect.appendChild(option);
        } else {
          data.forEach((mp) => {
            const option = document.createElement("option");
            option.value = mp.id;
            option.textContent = `${mp.nama} (${mp.kategori.toUpperCase()})`;
            mataPelajaranSelect.appendChild(option);
          });
        }
      })
      .catch((error) => {
        console.error("Gagal memuat mata pelajaran:", error);
        mataPelajaranSelect.innerHTML =
          '<option value="">Gagal memuat data</option>';
      });
  }

  // Event listeners for select changes
  if (kelasSelect)
    kelasSelect.addEventListener("change", updateMataPelajaranOptions);
  if (jurusanSelect)
    jurusanSelect.addEventListener("change", updateMataPelajaranOptions);

  // Auto-fill day when date is selected
  if (tanggalInput && hariInput) {
    tanggalInput.addEventListener("change", function () {
      const tanggal = new Date(tanggalInput.value);
      if (!isNaN(tanggal)) {
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
      } else {
        hariInput.value = "";
      }
    });
  }

  // Handle action menu clicks
  document.addEventListener("click", function (event) {
    // Close all action menus when clicking outside
    document.querySelectorAll(".action-menu-wrapper").forEach((wrapper) => {
      if (!wrapper.contains(event.target)) {
        wrapper.classList.remove("show");
      }
    });

    // Toggle menu when button is clicked
    if (event.target.classList.contains("action-menu-btn")) {
      const wrapper = event.target.closest(".action-menu-wrapper");
      if (wrapper) {
        wrapper.classList.toggle("show");
      }
    }
  });

  // Add loading to form submissions
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function () {
      showLoading();
    });
  });

  // Modal close when clicking outside
  window.addEventListener("click", function (event) {
    const modals = document.getElementsByClassName("modal");
    for (let i = 0; i < modals.length; i++) {
      if (event.target === modals[i]) {
        modals[i].style.display = "none";
      }
    }
  });

  // Filter form handler (only if the form exists)
  const filterForm = document.getElementById("filterForm");
  if (filterForm) {
    filterForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const kelasId = document.getElementById("filter_kelas_id").value;
      const jurusanId = document.getElementById("filter_jurusan_id").value;

      const response = await fetch("handlers/handle_request.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=filter_jadwal&kelas_id=${kelasId}&jurusan_id=${jurusanId}`,
      });

      const data = await response.json();
      const tableBody = document.querySelector("#jadwalUjianTable tbody");

      if (tableBody) {
        tableBody.innerHTML = "";

        data.forEach((jadwal) => {
          tableBody.innerHTML += `
            <tr>
              <td>${jadwal.tanggal}</td>
              <td>${jadwal.hari}</td>
              <td>${jadwal.kelas_nama}</td>
              <td>${jadwal.jurusan_nama}</td>
              <td>${jadwal.mapel_nama}</td>
              <td>${jadwal.jam_mulai} - ${jadwal.jam_selesai}</td>
              <td>
                <button class="edit" data-id="${jadwal.id}">Edit</button>
                <button class="delete" data-id="${jadwal.id}">Hapus</button>
              </td>
            </tr>
          `;
        });
      }
    });
  }
});

// ========== GLOBAL FUNCTIONS ==========

// Confirm delete function
window.confirmDelete = function (type, id, name) {
  if (confirm(`Yakin ingin menghapus ${type} '${name}'?`)) {
    window.location.href = `?delete&table=${type}&id=${id}`;
  }
};

// Modal functions
window.editKelas = function (id, nama) {
  const modal = document.getElementById("editKelasModal");
  if (modal) {
    document.getElementById("edit_kelas_id").value = id;
    document.getElementById("edit_kelas_nama").value = nama;
    modal.style.display = "block";
  }
};

window.editJurusan = function (id, nama) {
  const modal = document.getElementById("editJurusanModal");
  if (modal) {
    document.getElementById("edit_jurusan_id").value = id;
    document.getElementById("edit_jurusan_nama").value = nama;
    modal.style.display = "block";
  }
};

window.editMataPelajaran = function (id, nama, kategori) {
  const modal = document.getElementById("editMataPelajaranModal");
  if (modal) {
    document.getElementById("edit_mp_id").value = id;
    document.getElementById("edit_mp_nama").value = nama;
    document.getElementById("edit_mp_kategori").value = kategori;
    modal.style.display = "block";
  }
};

window.editJadwal = function (id) {
  const modal = document.getElementById("editJadwalUjianModal");
  if (!modal) {
    console.error("Modal edit jadwal tidak ditemukan");
    return;
  }

  // Fetch data jadwal via AJAX
  fetch(`handlers/handle_request.php?ajax=get_jadwal&id=${id}`)
    .then((response) => {
      if (!response.ok) throw new Error("HTTP error " + response.status);
      return response.json();
    })
    .then((jadwal) => {
      if (!jadwal || !jadwal.id) {
        throw new Error("Data jadwal tidak ditemukan");
      }

      // Isi form dengan data jadwal
      document.getElementById("edit_jadwal_id").value = jadwal.id;
      document.getElementById("edit_kelas_id").value = jadwal.kelas_id;
      document.getElementById("edit_jurusan_id").value = jadwal.jurusan_id;
      document.getElementById("edit_mata_pelajaran_id").value =
        jadwal.mata_pelajaran_id;
      document.getElementById("edit_tanggal").value = jadwal.tanggal;
      document.getElementById("edit_jam_mulai").value = jadwal.jam_mulai;
      document.getElementById("edit_jam_selesai").value = jadwal.jam_selesai;

      // Tampilkan modal
      modal.style.display = "block";
    })
    .catch((error) => {
      console.error("Gagal memuat data jadwal:", error);
      alert("Gagal memuat data jadwal. Silakan coba lagi.");
    });
};

// Close modal function
window.closeModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.style.display = "none";
};

// Loading functions
window.showLoading = function () {
  const loadingOverlay = document.getElementById("loading-overlay");
  if (loadingOverlay) {
    loadingOverlay.style.display = "flex";
  }
};

window.hideLoading = function () {
  const loadingOverlay = document.getElementById("loading-overlay");
  if (loadingOverlay) {
    loadingOverlay.style.display = "none";
  }
};

// ========== JADWAL UJIAN FILTER FUNCTIONS ==========

// Enhanced filter function
window.filterJadwalEnhanced = function () {
  const kelasId = document.getElementById("filter_kelas_id").value;
  const jurusanId = document.getElementById("filter_jurusan_id").value;
  const resultDiv = document.getElementById("hasil-jadwal-ujian");

  if (!resultDiv) {
    console.error("Element hasil-jadwal-ujian tidak ditemukan");
    return;
  }

  if (!kelasId || !jurusanId) {
    resultDiv.innerHTML =
      '<div class="alert alert-warning">Silakan pilih kelas dan jurusan terlebih dahulu.</div>';
    return;
  }

  // Show loading
  resultDiv.innerHTML = '<div class="loading">Memuat data jadwal...</div>';

  fetch(
    `handlers/handle_request.php?ajax=filter_jadwal_grouped&kelas_id=${encodeURIComponent(
      kelasId
    )}&jurusan_id=${encodeURIComponent(jurusanId)}`
  )
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        displayFilteredSchedules(data.data, data.total);
      } else {
        resultDiv.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      resultDiv.innerHTML =
        '<div class="alert alert-error">Gagal memuat data jadwal ujian.</div>';
    });
};

// Display filtered schedules
function displayFilteredSchedules(schedules, total) {
  const resultDiv = document.getElementById("hasil-jadwal-ujian");

  if (!resultDiv) {
    console.error("Element hasil-jadwal-ujian tidak ditemukan");
    return;
  }

  if (schedules.length === 0) {
    resultDiv.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">📅</div>
        <h3>Tidak ada jadwal ujian</h3>
        <p>Tidak ditemukan jadwal ujian untuk kelas dan jurusan yang dipilih.</p>
      </div>
    `;
    return;
  }

  let html = `
    <div class="results-header">
      <h3>Hasil Filter Jadwal Ujian</h3>
      <span class="results-count">${total} jadwal ditemukan</span>
    </div>
    <div class="schedule-container">
      <table class="schedule-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Mata Pelajaran</th>
            <th>Jam</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
  `;

  schedules.forEach((jadwal, index) => {
    html += `
      <tr>
        <td>${index + 1}</td>
        <td><span class="date-badge">${formatDate(jadwal.tanggal)}</span></td>
        <td><span class="day-badge">${jadwal.hari}</span></td>
        <td><strong>${jadwal.mata_pelajaran_nama}</strong></td>
        <td><span class="time-badge">${jadwal.jam_mulai} - ${
      jadwal.jam_selesai
    }</span></td>
        <td>${jadwal.kelas_nama}</td>
        <td>${jadwal.jurusan_nama}</td>
        <td>
          <button class="btn-small btn-edit" onclick="editJadwal('${
            jadwal.id
          }')">Edit</button>
          <button class="btn-small btn-delete" onclick="confirmDelete('jadwal', '${
            jadwal.id
          }', '${jadwal.mata_pelajaran_nama}')">Hapus</button>
        </td>
      </tr>
    `;
  });

  html += `
        </tbody>
      </table>
    </div>
  `;

  resultDiv.innerHTML = html;
}

// Show all schedules grouped
window.showAllSchedulesGrouped = function () {
  const resultDiv = document.getElementById("hasil-jadwal-ujian");

  if (!resultDiv) {
    console.error("Element hasil-jadwal-ujian tidak ditemukan");
    return;
  }

  // Show loading
  resultDiv.innerHTML = '<div class="loading">Memuat semua jadwal...</div>';

  fetch("handlers/handle_request.php?ajax=get_all_schedules_grouped")
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        displayGroupedSchedules(data.data);
      } else {
        resultDiv.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      resultDiv.innerHTML =
        '<div class="alert alert-error">Gagal memuat data jadwal ujian.</div>';
    });
};

// Display grouped schedules
function displayGroupedSchedules(groupedData) {
  const resultDiv = document.getElementById("hasil-jadwal-ujian");

  if (!resultDiv) {
    console.error("Element hasil-jadwal-ujian tidak ditemukan");
    return;
  }

  if (Object.keys(groupedData).length === 0) {
    resultDiv.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">📅</div>
        <h3>Tidak ada jadwal ujian</h3>
        <p>Belum ada jadwal ujian yang terdaftar.</p>
      </div>
    `;
    return;
  }

  let html = `
    <div class="results-header">
      <h3>Semua Jadwal Ujian (Dikelompokkan)</h3>
      <span class="results-count">${
        Object.keys(groupedData).length
      } kelompok</span>
    </div>
  `;

  Object.keys(groupedData).forEach((groupKey) => {
    const schedules = groupedData[groupKey];
    html += `
      <div class="schedule-group">
        <div class="group-header">
          <h4>${groupKey}</h4>
          <small>${schedules.length} jadwal ujian</small>
        </div>
        <table class="schedule-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Hari</th>
              <th>Mata Pelajaran</th>
              <th>Jam</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
    `;

    schedules.forEach((jadwal, index) => {
      html += `
        <tr>
          <td>${index + 1}</td>
          <td><span class="date-badge">${formatDate(jadwal.tanggal)}</span></td>
          <td><span class="day-badge">${jadwal.hari}</span></td>
          <td><strong>${jadwal.mata_pelajaran_nama}</strong></td>
          <td><span class="time-badge">${jadwal.jam_mulai} - ${
        jadwal.jam_selesai
      }</span></td>
          <td>
            <button class="btn-small btn-edit" onclick="editJadwal('${
              jadwal.id
            }')">Edit</button>
            <button class="btn-small btn-delete" onclick="confirmDelete('jadwal', '${
              jadwal.id
            }', '${jadwal.mata_pelajaran_nama}')">Hapus</button>
          </td>
        </tr>
      `;
    });

    html += `
          </tbody>
        </table>
      </div>
    `;
  });

  resultDiv.innerHTML = html;
}

// Reset filter
window.resetFilter = function () {
  const kelasSelect = document.getElementById("filter_kelas_id");
  const jurusanSelect = document.getElementById("filter_jurusan_id");
  const resultDiv = document.getElementById("hasil-jadwal-ujian");

  if (kelasSelect) kelasSelect.value = "";
  if (jurusanSelect) jurusanSelect.value = "";
  if (resultDiv) resultDiv.innerHTML = "";
};

// Format date helper
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

document.getElementById('hamburger').addEventListener('click', function () {
  document.getElementById('navLinks').classList.toggle('active');
});

const dropdown = document.querySelector('.dropdown');
dropdown.addEventListener('click', function (e) {
  if (window.innerWidth <= 768) {
    e.preventDefault();
    dropdown.classList.toggle('open');
  }
});
