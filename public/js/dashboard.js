(function () {
  const { today } = JSON.parse(document.getElementById('journalin-data').textContent);
  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const weekdays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
  const $ = id => document.getElementById(id);
  const escapeHtml = value => String(value).replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[char]));
  const tasks = TaskStore.all(); // saved to the database when logged in, kept in memory only for guests
  const todayDate = new Date(today + 'T00:00:00');
  let calendarMonth = new Date(todayDate.getFullYear(), todayDate.getMonth(), 1);

  function renderStats() {
    const doneCount = tasks.filter(task => task.done).length;
    const todayCount = tasks.filter(task => task.due.slice(0, 10) === today).length;
    const completionRate = tasks.length ? Math.round(doneCount / tasks.length * 100) : 0;

    $('st-total').textContent = tasks.length;
    $('st-today').textContent = todayCount;
    $('st-rate').textContent = completionRate + '%';
    document.querySelector('.bar i').style.width = completionRate + '%';
  }

  function taskRow(task) {
    const priorityLabel = { high: 'Prioritas Tinggi', mid: 'Prioritas Sedang', low: 'Prioritas Rendah' }[task.priority];
    const calUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent(task.title) + '&details=' + encodeURIComponent(task.desc);
    return `<div class="task ${task.done ? 'is-done' : ''}" data-id="${task.id}">
      <button type="button" class="check ${task.done ? 'checked' : ''}" data-act="toggle" aria-label="Tandai selesai">${task.done ? '<svg viewBox="0 0 24 24"><path d="m5 12 5 5 9-10"/></svg>' : ''}</button>
      <div class="task-body"><div class="task-title"><strong>${escapeHtml(task.title)}</strong><span class="tag ${task.done ? 'gray' : task.priority}">${task.done ? 'Selesai' : priorityLabel}</span><span class="tag ${task.done ? 'gray' : 'work'}">${escapeHtml(task.category)}</span></div>
      <p>${escapeHtml(task.desc)}</p></div>
      <div class="task-actions">
        <a href="${calUrl}" target="_blank" class="icon-btn" title="Tambah ke Google Calendar" aria-label="Tambah ke Kalender"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></a>
        <button type="button" class="icon-btn" data-act="edit" aria-label="Edit">${'<svg viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>'}</button>
      </div>
    </div>`;
  }

  function renderTodayTasks() {
    const todayTasks = tasks
      .filter(task => task.due.slice(0, 10) === today)
      .sort((first, second) => first.due.localeCompare(second.due));
    $('today-list').innerHTML = todayTasks.length
      ? todayTasks.map(taskRow).join('')
      : '<div class="empty">Tidak ada tugas dengan tenggat hari ini.</div>';
  }

  function renderCalendar() {
    const year = calendarMonth.getFullYear();
    const month = calendarMonth.getMonth();
    $('cal-title').textContent = monthNames[month] + ' ' + year;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPreviousMonth = new Date(year, month, 0).getDate();
    const cells = weekdays.map(day => `<span class="dow">${day}</span>`);

    for (let index = 0; index < 42; index += 1) {
      let date;
      let muted = false;
      if (index < firstDay) {
        date = new Date(year, month - 1, daysInPreviousMonth - firstDay + index + 1);
        muted = true;
      } else if (index >= firstDay + daysInMonth) {
        date = new Date(year, month + 1, index - firstDay - daysInMonth + 1);
        muted = true;
      } else {
        date = new Date(year, month, index - firstDay + 1);
      }

      const dateKey = [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-');
      const hasTasks = tasks.some(task => task.due.slice(0, 10) === dateKey);
      const isToday = dateKey === today;
      cells.push(`<span class="cal-d${muted ? ' muted' : ''}${isToday ? ' today' : ''}"><b>${date.getDate()}</b>${hasTasks ? '<i></i>' : ''}</span>`);
    }
    $('cal-grid').innerHTML = cells.join('');
  }

  function renderCategoryDistribution() {
    const total = tasks.length;
    if (total === 0) {
      $('cat-dist-list').innerHTML = '<div class="empty" style="padding:16px;margin-top:0">Belum ada tugas.</div>';
      return;
    }
    const counts = {};
    tasks.forEach(t => { counts[t.category] = (counts[t.category] || 0) + 1; });
    
    const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    const colors = {
      'Pekerjaan': '#3b82f6',
      'Kuliah & Belajar': '#f59e0b',
      'Pribadi': '#10b981'
    };
    const defaultColors = ['#6366f1', '#ec4899', '#8b5cf6', '#14b8a6'];
    
    let html = '';
    sorted.forEach(([cat, count], i) => {
      const pct = Math.round((count / total) * 100);
      const color = colors[cat] || defaultColors[i % defaultColors.length];
      html += `<div class="dist-row">
        <div><span><i style="background:${color}"></i>${escapeHtml(cat)}</span><em>${pct}%</em></div>
        <div class="track"><b style="width:${pct}%; background:${color}"></b></div>
      </div>`;
    });
    $('cat-dist-list').innerHTML = html;
  }

  function render() {
    renderStats();
    renderTodayTasks();
    renderCalendar();
    renderCategoryDistribution();
  }

  $('today-list').addEventListener('click', async event => {
    const button = event.target.closest('[data-act]');
    if (!button) return;
    const taskElement = button.closest('[data-id]');
    const task = TaskStore.find(Number(taskElement.dataset.id));
    if (!task) return;

    if (button.dataset.act === 'toggle') {
      const wasDone = task.done;
      if (!(await TaskStore.toggle(task))) return;
      if (!wasDone) TaskStore.notify("You've checked off the task!");
      render();
    } else {
      window.TaskModal.open(task, async (data, editing) => {
        if (await TaskStore.update(editing, data)) render();
      });
    }
  });

  $('add-btn').addEventListener('click', event => {
    event.preventDefault();
    window.TaskModal.open(null, async data => {
      if (await TaskStore.create(data)) render();
    });
  });

  $('cal-prev').addEventListener('click', () => {
    calendarMonth = new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() - 1, 1);
    renderCalendar();
  });
  $('cal-next').addEventListener('click', () => {
    calendarMonth = new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() + 1, 1);
    renderCalendar();
  });

  render();
})();
