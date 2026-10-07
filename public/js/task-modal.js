(function () {
  const modal = document.getElementById('task-modal');
  const form = document.getElementById('task-form');
  if (!modal || !form) return;

  const fields = form.elements;
  const title = document.getElementById('tm-title');
  const required = document.getElementById('f-req');
  const dueText = document.getElementById('f-due-text');
  const notification = document.getElementById('task-notification');
  let editing = null;
  let onSave = null;
  let notificationTimer;

  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  function updateDueText(value) {
    const match = value.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2})$/);
    dueText.textContent = match
      ? Number(match[3]) + ' ' + monthNames[Number(match[2]) - 1] + ' ' + match[1] + ', ' + match[4] + ' WIB'
      : 'Pilih tanggal & jam';
  }

  function close() {
    modal.hidden = true;
    editing = null;
    onSave = null;
  }

  function localInputValue(date) {
    const pad = value => String(value).padStart(2, '0');
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
  }

  document.getElementById('tm-close').addEventListener('click', close);
  document.getElementById('tm-cancel').addEventListener('click', close);
  modal.addEventListener('mousedown', event => {
    if (event.target === modal) close();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !modal.hidden) close();
  });
  fields.due.addEventListener('change', () => updateDueText(fields.due.value));
  fields.due.addEventListener('click', () => {
    if (typeof fields.due.showPicker === 'function') fields.due.showPicker();
  });
  fields.title.addEventListener('input', () => {
    fields.title.classList.remove('invalid');
    required.classList.remove('err');
  });

  form.addEventListener('submit', event => {
    event.preventDefault();
    const taskTitle = fields.title.value.trim();
    if (!taskTitle) {
      fields.title.classList.add('invalid');
      required.classList.add('err');
      fields.title.focus();
      return;
    }
    if (!fields.due.value) {
      fields.due.parentNode.style.borderColor = '#e11d48';
      return;
    }

    try {
      onSave({
        title: taskTitle,
        desc: fields.desc.value.trim(),
        category: fields.category.value,
        priority: fields.priority.value,
        due: fields.due.value.replace('T', ' ')
      }, editing);
    } finally {
      close();
    }
  });

  window.TaskModal = {
    notify(message) {
      notification.textContent = message;
      notification.hidden = false;
      clearTimeout(notificationTimer);
      notificationTimer = setTimeout(() => {
        notification.hidden = true;
      }, 3000);
    },
    open(task, saveTask) {
      editing = task || null;
      onSave = saveTask;
      fields.title.classList.remove('invalid');
      required.classList.remove('err');
      fields.title.value = task ? task.title : '';
      fields.desc.value = task ? task.desc : '';
      fields.category.value = task ? task.category : 'Pekerjaan';
      fields.priority.value = task ? task.priority : 'mid';
      fields.due.value = task
        ? task.due.replace(' ', 'T')
        : localInputValue(new Date(Date.now() + 60 * 60 * 1000));
      updateDueText(fields.due.value);
      modal.hidden = false;
      fields.title.focus();
    }
  };
})();
