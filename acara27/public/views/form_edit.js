(() => {
  const form = document.querySelector('#edit-student-form');

  if (!form) {
    return;
  }

  const baseUrl = window.location.pathname.replace(/\/views\/form_edit\.html\/?$/, '');
  const apiUrl = `${baseUrl}/api/mahasiswa`;
  const dashboardUrl = `${baseUrl}/dashboard`;
  const studentId = new URLSearchParams(window.location.search).get('id');
  const token = localStorage.getItem('token');
  const message = document.querySelector('#edit-message');
  const saveButton = document.querySelector('#save-student');
  const photoInput = form.elements.namedItem('foto');
  const photoPreview = document.querySelector('#student-photo-preview');
  const viewSupport = window.ViewSupport;

  const request = async (url, options = {}) => {
    const response = await fetch(url, {
      ...options,
      headers: {
        Accept: 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    });
    const result = await response.json();

    if (!response.ok || result.success !== true) {
      throw new Error(result.message || 'Permintaan gagal.');
    }

    return result;
  };

  const showError = (error) => {
    message.textContent = error.message;
    viewSupport.notify(error.message, 'error');
  };

  const loadStudent = async () => {
    if (!studentId || !/^\d+$/.test(studentId)) {
      throw new Error('ID mahasiswa tidak valid.');
    }

    const result = await request(`${apiUrl}/${encodeURIComponent(studentId)}`);
    const student = result.data;
    form.elements.namedItem('nim').value = student.nim ?? '';
    form.elements.namedItem('nama').value = student.nama ?? '';
    form.elements.namedItem('email').value = student.email ?? '';
    form.elements.namedItem('prodi_id').value = String(student.prodi_id ?? '');
    form.elements.namedItem('angkatan').value = String(student.angkatan ?? '');

    if (student.foto) {
      photoPreview.src = `${baseUrl}/uploads/${encodeURIComponent(student.foto)}`;
      photoPreview.hidden = false;
    }

    message.textContent = '';
    saveButton.disabled = false;
  };

  photoInput.addEventListener('change', () => {
    const file = photoInput.files[0];
    message.textContent = '';

    if (!file) {
      return;
    }

    if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 2 * 1024 * 1024) {
      photoInput.value = '';
      message.textContent = 'Pilih foto JPG/PNG dengan ukuran maksimal 2MB.';
      viewSupport.notify(message.textContent, 'error');
      return;
    }

    photoPreview.src = URL.createObjectURL(file);
    photoPreview.hidden = false;
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!viewSupport.validateRequiredForm(form)) {
      return;
    }

    saveButton.disabled = true;
    message.textContent = 'Menyimpan perubahan...';

    try {
      const result = await request(`${apiUrl}/${encodeURIComponent(studentId)}`, {
        method: 'POST',
        body: new FormData(form),
      });
      viewSupport.notifyOnNextPage(result.message);
      window.location.assign(dashboardUrl);
    } catch (error) {
      showError(error);
      saveButton.disabled = false;
    }
  });

  if (!token) {
    showError(new Error('Token admin tidak ditemukan. Silakan login ulang.'));
  } else {
    loadStudent().catch(showError);
  }
})();
