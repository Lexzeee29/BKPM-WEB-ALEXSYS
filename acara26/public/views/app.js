(() => {
  const dashboard = document.querySelector('#student-dashboard');

  if (!dashboard) {
    return;
  }

  const apiUrl = dashboard.dataset.apiUrl;
  const uploadsUrl = dashboard.dataset.uploadsUrl;
  const tableBody = dashboard.querySelector('#tabel-mahasiswa tbody');
  const message = dashboard.querySelector('#student-message');
  const search = dashboard.querySelector('#student-search');
  const refreshButton = dashboard.querySelector('#refresh-students');
  const logoutButton = dashboard.querySelector('#logout-button');
    const addButton = dashboard.querySelector('#add-student');
    const studentDialog = dashboard.querySelector('#student-dialog');
    const studentForm = dashboard.querySelector('#student-form');
    const formTitle = dashboard.querySelector('#student-form-title');
    const formMessage = dashboard.querySelector('#student-form-message');
    const isAdmin = dashboard.dataset.userRole === 'admin';
    const apiToken = dashboard.dataset.apiToken;
  let students = [];

    if (isAdmin && apiToken) {
      localStorage.setItem('token', apiToken);
      localStorage.setItem('role', 'admin');
    } else {
      localStorage.removeItem('token');
      localStorage.setItem('role', 'user');
    }

    if (!isAdmin) {
    dashboard.querySelectorAll('[data-admin-only]').forEach((element) => {
      element.hidden = true;
    });
  }

  logoutButton?.addEventListener('click', () => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
  });

  const apiRequest = async (url, options = {}) => {
    const headers = {
      Accept: 'application/json',
      ...(options.body && !(options.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
      ...(apiToken ? { Authorization: `Bearer ${apiToken}` } : {}),
    };
    const response = await fetch(url, { ...options, headers });
    const result = await response.json();

    if (!response.ok || result.success !== true) {
      const errorMessage = response.status === 401
        ? 'Token API kedaluwarsa. Muat ulang dashboard lalu coba lagi.'
        : result.message || 'Permintaan gagal.';
      throw new Error(errorMessage);
    }

    return result;
  };

  const openStudentDialog = (student = null) => {
    studentForm.reset();
    studentForm.dataset.studentId = student ? String(student.id) : '';
    formTitle.textContent = student ? 'Ubah mahasiswa' : 'Tambah mahasiswa';
    formMessage.textContent = '';
    studentForm.elements.namedItem('nim').value = student?.nim ?? '';
    studentForm.elements.namedItem('nama').value = student?.nama ?? '';
    studentForm.elements.namedItem('email').value = student?.email ?? '';
    studentForm.elements.namedItem('prodi_id').value = String(student?.prodi_id ?? 1);
    studentForm.elements.namedItem('angkatan').value = String(student?.angkatan ?? new Date().getFullYear());
    const photoPreview = dashboard.querySelector('#student-photo-preview');
    photoPreview.hidden = !student?.foto;
    photoPreview.src = student?.foto ? `${uploadsUrl}/${encodeURIComponent(student.foto)}` : '';
    studentDialog.showModal();
  };

  studentForm.elements.namedItem('foto').addEventListener('change', (event) => {
    const file = event.target.files[0];
    const photoPreview = dashboard.querySelector('#student-photo-preview');

    if (file) {
      photoPreview.src = URL.createObjectURL(file);
      photoPreview.hidden = false;
    }
  });

  const showRows = () => {
    const query = search.value.trim().toLocaleLowerCase('id');
    const visibleStudents = students.filter((student) =>
      [student.nim, student.nama, student.prodi]
        .some((value) => String(value ?? '').toLocaleLowerCase('id').includes(query))
    );

    tableBody.replaceChildren();

    if (visibleStudents.length === 0) {
      const row = document.createElement('tr');
      const cell = document.createElement('td');
      cell.colSpan = isAdmin ? 7 : 6;
      cell.className = 'table-placeholder';
      cell.textContent = students.length === 0
        ? 'Belum ada data'
        : 'Tidak ada mahasiswa yang cocok dengan pencarian.';
      row.append(cell);
      tableBody.append(row);
      message.textContent = students.length === 0
        ? 'Belum ada data mahasiswa.'
        : 'Tidak ada mahasiswa yang cocok dengan pencarian.';
      return;
    }

    visibleStudents.forEach((student) => {
      const row = document.createElement('tr');
      const photoCell = document.createElement('td');
      if (student.foto) {
        const photo = document.createElement('img');
        photo.className = 'student-photo';
        photo.src = `${uploadsUrl}/${encodeURIComponent(student.foto)}`;
        photo.alt = `Foto ${student.nama}`;
        photo.loading = 'lazy';
        photoCell.append(photo);
      } else {
        photoCell.textContent = '-';
      }
      row.append(photoCell);
      const values = [student.nim, student.nama, student.prodi, student.angkatan];

      values.forEach((value) => {
        const cell = document.createElement('td');
        cell.textContent = String(value ?? '-');
        row.append(cell);
      });

      const statusCell = document.createElement('td');
      const status = document.createElement('span');
      status.className = `status-label status-${String(student.status ?? 'lainnya').toLowerCase()}`;
      status.textContent = String(student.status ?? 'Tidak diketahui');
      statusCell.append(status);
      row.append(statusCell);

      if (isAdmin) {
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.className = 'btn btn-sm btn-outline-primary me-1';
        editButton.type = 'button';
        editButton.dataset.action = 'edit';
        editButton.dataset.studentId = String(student.id);
        editButton.textContent = 'Ubah';

        const deleteButton = document.createElement('button');
        deleteButton.className = 'btn btn-sm btn-outline-danger';
        deleteButton.type = 'button';
        deleteButton.dataset.action = 'delete';
        deleteButton.dataset.studentId = String(student.id);
        deleteButton.textContent = 'Hapus';

        actionsCell.append(editButton, deleteButton);
        row.append(actionsCell);
      }

      tableBody.append(row);
    });

    message.textContent = `Menampilkan ${visibleStudents.length} dari ${students.length} mahasiswa.`;
  };

  const loadStudents = async () => {
    refreshButton.disabled = true;
    message.textContent = 'Memuat data...';

    try {
      const result = await apiRequest(apiUrl);

      if (!Array.isArray(result.data)) {
        throw new Error('Respons API tidak sesuai.');
      }

      students = result.data;
      dashboard.querySelector('#student-total').textContent = String(students.length);
      dashboard.querySelector('#program-total').textContent = String(
        new Set(students.map((student) => student.prodi).filter(Boolean)).size
      );
      dashboard.querySelector('#active-total').textContent = String(
        students.filter((student) => student.status === 'aktif').length
      );
      showRows();
    } catch (error) {
      students = [];
      dashboard.querySelector('#student-total').textContent = '0';
      dashboard.querySelector('#program-total').textContent = '0';
      dashboard.querySelector('#active-total').textContent = '0';
      showRows();
      message.textContent = `Gagal memuat data: ${error.message}`;
    } finally {
      refreshButton.disabled = false;
    }
  };

  addButton?.addEventListener('click', () => openStudentDialog());
  dashboard.querySelector('#cancel-student').addEventListener('click', () => studentDialog.close());

  tableBody.addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-action]');

    if (!button || !isAdmin) {
      return;
    }

    const student = students.find((item) => String(item.id) === button.dataset.studentId);

    if (!student) {
      return;
    }

    if (button.dataset.action === 'edit') {
      openStudentDialog(student);
      return;
    }

    if (!window.confirm(`Hapus data mahasiswa ${student.nama}?`)) {
      return;
    }

    button.disabled = true;

    try {
      const result = await apiRequest(`${apiUrl}/${student.id}`, { method: 'DELETE' });
      await loadStudents();
      message.textContent = result.message;
    } catch (error) {
      message.textContent = `Gagal menghapus data: ${error.message}`;
      button.disabled = false;
    }
  });

  studentForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!isAdmin) {
      return;
    }

    const studentId = studentForm.dataset.studentId;
    const payload = new FormData(studentForm);
    const saveButton = dashboard.querySelector('#save-student');
    saveButton.disabled = true;
    formMessage.textContent = 'Menyimpan data...';

    try {
      const result = await apiRequest(
        studentId ? `${apiUrl}/${studentId}` : apiUrl,
        {
          method: 'POST',
          body: payload,
        }
      );
      studentDialog.close();
      await loadStudents();
      message.textContent = result.message;
    } catch (error) {
      formMessage.textContent = `Gagal menyimpan data: ${error.message}`;
    } finally {
      saveButton.disabled = false;
    }
  });

  search.addEventListener('input', showRows);
  refreshButton.addEventListener('click', loadStudents);
  loadStudents();
})();