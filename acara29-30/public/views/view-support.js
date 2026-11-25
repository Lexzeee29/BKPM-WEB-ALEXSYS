(() => {
  let container;

  const getContainer = () => {
    if (!container) {
      container = document.createElement('div');
      container.className = 'view-notifications';
      container.setAttribute('aria-live', 'polite');
      container.setAttribute('aria-relevant', 'additions');
      document.body.append(container);
    }

    return container;
  };

  const notify = (message, type = 'success') => {
    const text = String(message ?? '').trim();
    if (!text) return;

    const notification = document.createElement('section');
    notification.className = `view-notification view-notification-${type === 'error' ? 'error' : 'success'}`;
    notification.setAttribute('role', type === 'error' ? 'alert' : 'status');

    const content = document.createElement('span');
    content.textContent = text;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'view-notification-close';
    closeButton.textContent = 'Tutup';
    closeButton.setAttribute('aria-label', 'Tutup notifikasi');

    const dismiss = () => notification.remove();
    const timeout = window.setTimeout(dismiss, 5500);
    closeButton.addEventListener('click', () => {
      window.clearTimeout(timeout);
      dismiss();
    });

    notification.append(content, closeButton);
    getContainer().prepend(notification);

    while (container.childElementCount > 4) {
      container.lastElementChild.remove();
    }
  };

  const fetchWithRetry = async (url, options = {}) => {
    let retries = 0;

    while (true) {
      try {
        return await fetch(url, options);
      } catch (error) {
        if (!(error instanceof TypeError) || retries >= 1) {
          throw error;
        }

        retries += 1;
        await new Promise((resolve) => window.setTimeout(resolve, 300));
      }
    }
  };

  const validateRequiredForm = (form) => {
    for (const control of Array.from(form.elements).filter((element) => element.willValidate)) {
      const label = control.closest('label')?.querySelector('span')?.textContent?.trim() || control.name;
      const emptyRequiredValue = control.required && control.type !== 'file' && !String(control.value).trim();
      control.setCustomValidity(emptyRequiredValue ? `${label} wajib diisi.` : '');

      if (!control.checkValidity()) {
        control.reportValidity();
        control.focus();
        notify(control.validationMessage || 'Periksa kembali data yang wajib diisi.', 'error');
        return false;
      }
    }

    return true;
  };

  const notifyOnNextPage = (message, type = 'success') => {
    try {
      sessionStorage.setItem('view-notification', JSON.stringify({ message, type }));
    } catch {
      notify(message, type);
    }
  };

  const consumeNextPageNotification = () => {
    try {
      const stored = sessionStorage.getItem('view-notification');
      if (!stored) return;

      sessionStorage.removeItem('view-notification');
      const notification = JSON.parse(stored);
      notify(notification.message, notification.type);
    } catch {
      sessionStorage.removeItem('view-notification');
    }
  };

  window.ViewSupport = {
    notify,
    fetchWithRetry,
    validateRequiredForm,
    notifyOnNextPage,
    consumeNextPageNotification,
  };
})();
