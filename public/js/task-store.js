// Shared task storage for the Dashboard and Semua Tugas pages.
// Logged in: every change is saved to the database through the /tasks endpoints.
// Guest: changes only live in memory (the demo tasks reset on reload) and are never saved.
(function () {
  const data = JSON.parse(document.getElementById('journalin-data').textContent);
  const tasks = data.tasks.slice();
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  let guestHintShown = false;

  async function request(method, url, body) {
    const response = await fetch(url, {
      method,
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: body ? JSON.stringify(body) : undefined
    });
    if (response.status === 401 || response.status === 419) {
      window.location.href = '/login';
      throw new Error('Session expired');
    }
    if (!response.ok) throw new Error('Request failed: ' + response.status);
    return response.status === 204 ? null : response.json();
  }

  function guestHint() {
    if (data.isAuthenticated || guestHintShown) return;
    guestHintShown = true;
    window.TaskModal.notify('Kamu belum login — perubahan tidak akan tersimpan.');
  }

  // Runs a change; returns true when it worked, false (after showing a message) when it failed.
  async function run(change) {
    try {
      await change();
      return true;
    } catch (error) {
      if (data.isAuthenticated) window.TaskModal.notify('Gagal menyimpan perubahan. Coba lagi.');
      return false;
    }
  }

  window.TaskStore = {
    all: () => tasks,
    find: id => tasks.find(task => task.id === id),

    // Same toast as TaskModal.notify, plus a one-time reminder for guests.
    notify(message) {
      if (data.isAuthenticated || guestHintShown) return window.TaskModal.notify(message);
      guestHintShown = true;
      window.TaskModal.notify(message + ' Login agar tugasmu tersimpan.');
    },

    create: values => run(async () => {
      const task = data.isAuthenticated
        ? await request('POST', data.taskEndpoint, values)
        : { id: Math.max(0, ...tasks.map(item => item.id)) + 1, done: false, doneAt: null, ...values };
      tasks.push(task);
      guestHint();
    }),

    update: (task, values) => run(async () => {
      const saved = data.isAuthenticated
        ? await request('PUT', data.taskEndpoint + '/' + task.id, values)
        : values;
      Object.assign(task, saved);
      guestHint();
    }),

    toggle: task => run(async () => {
      const saved = data.isAuthenticated
        ? await request('PATCH', data.taskEndpoint + '/' + task.id + '/toggle')
        : { done: !task.done, doneAt: task.done ? null : new Date().toTimeString().slice(0, 5) };
      Object.assign(task, saved);
    }),

    remove: task => run(async () => {
      if (data.isAuthenticated) await request('DELETE', data.taskEndpoint + '/' + task.id);
      tasks.splice(tasks.indexOf(task), 1);
      guestHint();
    })
  };
})();
