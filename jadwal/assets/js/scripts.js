document.addEventListener("DOMContentLoaded", function () {
  const kelasSelect = document.getElementById("kelas_id");
  const jurusanSelect = document.getElementById("jurusan_id");
  const mataPelajaranSelect = document.getElementById("mata_pelajaran_id");
  const dateInput = document.getElementById("date");
  const dayWarning = document.getElementById("day-warning");
  const submitButton = document.querySelector(
    'button[name="add_jadwal_ujian"]'
  );

  const groupedJadwal = window.groupedJadwal || {}; // Optional: may be injected globally for rendering
  const jadwalContainer = document.getElementById("jadwal-container");
  const filterJurusanSelect = document.getElementById("filter_jurusan");

  // Mapping of English day names to Indonesian
  const dayNamesEnglishToIndonesian = {
    Sunday: "Minggu",
    Monday: "Senin",
    Tuesday: "Selasa",
    Wednesday: "Rabu",
    Thursday: "Kamis",
    Friday: "Jumat",
    Saturday: "Sabtu",
  };

  function sanitize(text) {
    if (!text) return "";
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function createTable(jadwals) {
    if (!jadwals || jadwals.length === 0) {
      return "<p>Tidak ada jadwal ujian yang tersedia.</p>";
    }
    let html =
      '<table aria-label="Daftar Jadwal Ujian"><thead><tr><th>ID</th><th>Mata Pelajaran</th><th>Tanggal</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Aksi</th></tr></thead><tbody>';
    jadwals.forEach((item) => {
      const hariIndo = dayNamesEnglishToIndonesian[item.hari] || item.hari;
      html += `<tr>
                <td>${sanitize(item.id)}</td>
                <td>${sanitize(item.mata_pelajaran)}</td>
                <td>${sanitize(item.date)}</td>
                <td>${hariIndo}</td>
                <td>${sanitize(item.jam_mulai)}</td>
                <td>${sanitize(item.jam_selesai)}</td>
                <td>
                    <button class="edit" onclick="editJadwalUjian('${sanitize(
                      item.id
                    )}', '${sanitize(item.kelas_id)}', '${sanitize(
        item.jurusan_id
      )}', '${sanitize(item.mata_pelajaran_id)}', '${sanitize(
        item.date
      )}', '${hariIndo}', '${sanitize(item.jam_mulai)}', '${sanitize(
        item.jam_selesai
      )}')">Edit</button>
                    <button class="delete" onclick="confirmDelete('jadwal_ujian', '${sanitize(
                      item.id
                    )}', '${sanitize(item.mata_pelajaran)}')">Hapus</button>
                </td>
            </tr>`;
    });
    html += "</tbody></table>";
    return html;
  }

  function renderJadwal(filterJurusan) {
    jadwalContainer.innerHTML = "";

    let jurusansToShow =
      filterJurusan === "all" ? Object.keys(groupedJadwal) : [filterJurusan];

    jurusansToShow.forEach((jurusan) => {
      if (!groupedJadwal[jurusan]) return;

      const jurusanDiv = document.createElement("div");
      jurusanDiv.className = "jadwal-group";
      const jurusanHeading = document.createElement("h2");
      jurusanHeading.textContent = `Jurusan: ${jurusan}`;
      jurusanDiv.appendChild(jurusanHeading);

      const kelasList = Object.keys(groupedJadwal[jurusan]).sort();
      kelasList.forEach((kelas) => {
        const kelasDiv = document.createElement("div");
        kelasDiv.className = "jadwal-group";
        const kelasHeading = document.createElement("h3");
        kelasHeading.textContent = `Kelas: ${kelas}`;
        kelasDiv.appendChild(kelasHeading);

        const jadwals = groupedJadwal[jurusan][kelas];

        kelasDiv.innerHTML += createTable(jadwals);
        jurusanDiv.appendChild(kelasDiv);
      });

      jadwalContainer.appendChild(jurusanDiv);
    });

    if (jadwalContainer.children.length === 0) {
      jadwalContainer.innerHTML =
        "<p>Tidak ada jadwal ujian untuk jurusan ini.</p>";
    }
  }

  function updateMataPelajaran(
    kelasSelectElement,
    jurusanSelectElement,
    mataPelajaranSelectElement
  ) {
    const kelasId = kelasSelectElement.value;
    const jurusanId = jurusanSelectElement.value;

    mataPelajaranSelectElement.innerHTML =
      '<option value="">-- Pilih Mata Pelajaran --</option>';
    mataPelajaranSelectElement.disabled = true;

    if (kelasId && jurusanId) {
      mataPelajaranSelectElement.classList.add("loading");
      mataPelajaranSelectElement.innerHTML =
        '<option value="">Loading...</option>';

      fetch(
        `?ajax=filter_mata_pelajaran&kelas_id=${encodeURIComponent(
          kelasId
        )}&jurusan_id=${encodeURIComponent(jurusanId)}`
      )
        .then((response) => {
          if (!response.ok) throw new Error("Network response was not ok");
          return response.json();
        })
        .then((data) => {
          mataPelajaranSelectElement.classList.remove("loading");
          mataPelajaranSelectElement.innerHTML =
            '<option value="">-- Pilih Mata Pelajaran --</option>';

          if (data.length > 0) {
            data.forEach((mp) => {
              const option = document.createElement("option");
              option.value = mp.id;
              option.textContent = `${mp.nama} (${mp.kategori})`;
              mataPelajaranSelectElement.appendChild(option);
            });
            mataPelajaranSelectElement.disabled = false;
          } else {
            const option = document.createElement("option");
            option.value = "";
            option.textContent =
              "Semua mata pelajaran sudah dijadwalkan untuk kelas dan jurusan ini";
            option.style.fontStyle = "italic";
            option.style.color = "#6b7280";
            mataPelajaranSelectElement.appendChild(option);
            mataPelajaranSelectElement.disabled = true;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mataPelajaranSelectElement.classList.remove("loading");
          mataPelajaranSelectElement.innerHTML =
            '<option value="">Error loading data</option>';
          mataPelajaranSelectElement.disabled = true;
        });
    }
  }

  function checkDayAvailability() {
    const selectedDate = dateInput.value;
    const selectedKelasId = kelasSelect.value;

    if (selectedDate && selectedKelasId) {
      const dayObj = new Date(selectedDate);
      const dayEnglish = dayObj.toLocaleDateString("en-US", {
        weekday: "long",
      });
      const hariIndo = dayNamesEnglishToIndonesian[dayEnglish] || dayEnglish;

      fetch(
        `?ajax=check_day_availability&hari=${encodeURIComponent(
          hariIndo
        )}&kelas_id=${selectedKelasId}`
      )
        .then((response) => response.json())
        .then((data) => {
          dayWarning.textContent = data.message;
          dayWarning.className = "day-warning show";

          if (!data.available) {
            dayWarning.style.color = "#dc2626";
            submitButton.disabled = true;
            submitButton.style.opacity = "0.5";
            submitButton.style.cursor = "not-allowed";
          } else {
            dayWarning.style.color =
              data.current_count > 0 ? "#f59e0b" : "#16a34a";
            submitButton.disabled = false;
            submitButton.style.opacity = "1";
            submitButton.style.cursor = "pointer";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          dayWarning.textContent = "Error checking availability";
          dayWarning.className = "day-warning show";
          dayWarning.style.color = "#dc2626";
        });
    } else {
      dayWarning.className = "day-warning";
      submitButton.disabled = false;
      submitButton.style.opacity = "1";
      submitButton.style.cursor = "pointer";
    }
  }

  // Initial render all jadwal
  renderJadwal("all");

  // Filter change event
  if (filterJurusanSelect) {
    filterJurusanSelect.addEventListener("change", function () {
      renderJadwal(this.value);
    });
  }

  // Event listeners for mata pelajaran update
  if (kelasSelect && jurusanSelect && mataPelajaranSelect) {
    kelasSelect.addEventListener("change", function () {
      updateMataPelajaran(kelasSelect, jurusanSelect, mataPelajaranSelect);
      checkDayAvailability();
    });

    jurusanSelect.addEventListener("change", function () {
      updateMataPelajaran(kelasSelect, jurusanSelect, mataPelajaranSelect);
    });
  }

  if (dateInput) {
    dateInput.addEventListener("change", checkDayAvailability);
  }

  // Before submit validation
  const jadwalForm = document.getElementById("jadwalForm");
  if (jadwalForm) {
    jadwalForm.addEventListener("submit", function (e) {
      if (submitButton.disabled) {
        e.preventDefault();
        alert(
          "Tidak dapat menambah jadwal ujian. Hari pada tanggal sudah mencapai batas maksimal (6 jadwal)."
        );
      }
    });
  }
});
/**
 * Functions exposed globally for modal editing and deletion confirmation
 */
function editKelas(id, nama) {
  document.getElementById("edit_kelas_id").value = id;
  document.getElementById("edit_kelas_nama").value = nama;
  document.getElementById("editKelasModal").style.display = "block";
}

function editJurusan(id, nama) {
  document.getElementById("edit_jurusan_id").value = id;
  document.getElementById("edit_jurusan_nama").value = nama;
  document.getElementById("editJurusanModal").style.display = "block";
}

function editMataPelajaran(id, nama, kategori) {
  document.getElementById("edit_mp_id").value = id;
  document.getElementById("edit_mp_nama").value = nama;
  document.getElementById("edit_mp_kategori").value = kategori;
  document.getElementById("editMataPelajaranModal").style.display = "block";
}

function editJadwalUjian(
  id,
  kelasId,
  jurusanId,
  mataPelajaranId,
  date,
  hari,
  jamMulai,
  jamSelesai
) {
  document.getElementById("edit_ju_id").value = id;
  document.getElementById("edit_ju_kelas").value = kelasId;
  document.getElementById("edit_ju_jurusan").value = jurusanId;
  document.getElementById("edit_ju_mata_pelajaran").value = mataPelajaranId;
  document.getElementById("edit_ju_date").value = date;
  document.getElementById("edit_ju_jam_mulai").value = jamMulai;
  document.getElementById("edit_ju_jam_selesai").value = jamSelesai;
  document.getElementById("editJadwalUjianModal").style.display = "block";
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = "none";
  }
}

function confirmDelete(table, id, nama) {
  if (confirm(`Apakah Anda yakin ingin menghapus ${table} "${nama}"?`)) {
    window.location.href = `?delete=true&table=${table}&id=${id}`;
  }
}

setTimeout(() => {
  const flash = document.getElementById("flash-message");
  if (flash) flash.remove();
}, 3000);

// Close modal when clicking outside modal content
window.onclick = function (event) {
  const modals = [
    "editKelasModal",
    "editJurusanModal",
    "editMataPelajaranModal",
    "editJadwalUjianModal",
  ];
  modals.forEach((modalId) => {
    const modal = document.getElementById(modalId);
    if (modal && event.target === modal) {
      modal.style.display = "none";
    }
  });
};

document.getElementById("form-kelas").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append("action", "add_kelas");

  fetch("handlers/handle_request.php", {
    method: "POST",
    body: formData,
  }).then((res) => {
    if (res.ok) {
      alert("Data berhasil ditambahkan");
      location.reload(); // Atau perbarui tabel via JS
    }
  });
});

