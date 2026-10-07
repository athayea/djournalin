(function () {
  const { today, tomorrow } = JSON.parse(document.getElementById('journalin-data').textContent);
  const tasks = TaskStore.all(); // saved to the database when logged in, kept in memory only for guests
  const qg = new URLSearchParams(location.search).get('group');
  const PAGE_SIZE = 5;
  const state = { group: ['today', 'tomorrow', 'later'].includes(qg) ? qg : null, priority: '', status: 'all', page: 1 };

  const PRIO = { high: 'Prioritas Tinggi', mid: 'Prioritas Sedang', low: 'Prioritas Rendah' };
  const CAT = { 'Pekerjaan': 'work', 'Pribadi': 'work', 'Kuliah & Belajar': 'work' };
  const GROUPS = [['today', 'Hari Ini'], ['tomorrow', 'Besok'], ['later', 'Minggu Ini & Mendatang']];
  const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
  const $ = id => document.getElementById(id);

  const groupOf = t => { const d = t.due.slice(0, 10); return d === today ? 'today' : d === tomorrow ? 'tomorrow' : 'later'; };
  const timeOf = t => t.due.slice(11, 16).replace(':', ':');
  function whenLabel(t) {
    if (t.done) return ['success', 'Selesai ' + (t.doneAt || timeOf(t)) + ' WIB'];
    const g = groupOf(t);
    if (g === 'today') return [t.priority === 'high' ? 'danger' : 'neutral', 'Hari ini, ' + timeOf(t) + ' WIB'];
    if (g === 'tomorrow') return ['neutral', 'Besok, ' + timeOf(t) + ' WIB'];
    const [y, m, d] = t.due.slice(0, 10).split('-');
    return ['neutral', +d + ' ' + ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m - 1] + ' ' + y];
  }

  const ICON_EDIT = '<svg viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>';
  const ICON_DEL = '<svg viewBox="0 0 24 24"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v5M14 11v5"/></svg>';

  function row(t) {
    const [wc, wl] = whenLabel(t);
    const ptag = t.done ? '<span class="tag gray">Selesai</span>' : `<span class="tag ${t.priority}">${PRIO[t.priority]}</span>`;
    const ctag = `<span class="tag ${t.done ? 'gray' : 'work'}">${esc(t.category)}</span>`;
    const calUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent(t.title) + '&details=' + encodeURIComponent(t.desc);
    return `<div class="task ${t.done ? 'is-done' : ''}" data-id="${t.id}">
      <button type="button" class="check ${t.done ? 'checked' : ''}" data-act="toggle" aria-label="Tandai selesai">${t.done ? '<svg viewBox="0 0 24 24"><path d="m5 12 5 5 9-10"/></svg>' : ''}</button>
      <div class="task-body"><div class="task-title"><strong>${esc(t.title)}</strong>${ptag}${ctag}</div><p>${esc(t.desc)}</p></div>
      <span class="when ${wc}">${wl}</span>
      <div class="task-actions">
        <a href="${calUrl}" target="_blank" class="icon-btn" title="Tambah ke Google Calendar" aria-label="Tambah ke Kalender"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></a>
        <button type="button" class="icon-btn" data-act="edit" aria-label="Edit">${ICON_EDIT}</button>
        <button type="button" class="icon-btn del" data-act="delete" aria-label="Hapus">${ICON_DEL}</button>
      </div>
    </div>`;
  }

  function render() {
    const list = tasks
      .filter(t => (!state.priority || t.priority === state.priority) &&
        (!state.group || groupOf(t) === state.group) &&
        (state.status === 'all' || (state.status === 'completed' ? t.done : !t.done)))
      .sort((a, b) => a.due.localeCompare(b.due));
    const pageCount = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
    state.page = Math.min(state.page, pageCount);
    const pageTasks = list.slice((state.page - 1) * PAGE_SIZE, state.page * PAGE_SIZE);
    let html = '';
    GROUPS.forEach(([key, title]) => {
      const items = pageTasks.filter(t => groupOf(t) === key);
      if (items.length) html += `<h2 class="group-title">${title}</h2>` + items.map(row).join('');
    });
    $('task-list').innerHTML = html || '<div class="empty">Tidak ada tugas yang cocok dengan filter ini.</div>';
    renderPagination(pageCount);
    document.querySelectorAll('#cat-list .sub-item').forEach(b => b.classList.toggle('active', b.dataset.group === state.group));
  }

  function renderPagination(pageCount) {
    const pagination = $('task-pagination');
    pagination.hidden = pageCount <= 1;
    if (pagination.hidden) {
      pagination.innerHTML = '';
      return;
    }

    const pageButtons = Array.from({ length: pageCount }, (_, index) => {
      const page = index + 1;
      const current = page === state.page;
      return `<button type="button" class="page${current ? ' current' : ''}" data-page="${page}"${current ? ' aria-current="page"' : ''}>${page}</button>`;
    }).join('');

    pagination.innerHTML = `<button type="button" class="page" data-page="${state.page - 1}"${state.page === 1 ? ' disabled' : ''}>‹ Sebelumnya</button>${pageButtons}<button type="button" class="page" data-page="${state.page + 1}"${state.page === pageCount ? ' disabled' : ''}>Selanjutnya ›</button>`;
  }

  document.querySelectorAll('.filter-bar .chip[data-status]').forEach(button => {
    button.addEventListener('click', () => {
      state.status = button.dataset.status;
      state.page = 1;
      document.querySelectorAll('.filter-bar .chip[data-status]').forEach(chip => {
        const isActive = chip === button;
        chip.classList.toggle('active', isActive);
        chip.setAttribute('aria-pressed', String(isActive));
      });
      render();
    });
  });

  $('task-pagination').addEventListener('click', event => {
    const button = event.target.closest('button[data-page]');
    if (!button || button.disabled) return;
    state.page = Number(button.dataset.page);
    render();
  });

  // check / edit / delete
  $('task-list').addEventListener('click', async e => {
    const btn = e.target.closest('[data-act]'); if (!btn) return;
    const t = TaskStore.find(+btn.closest('.task').dataset.id); if (!t) return;
    if (btn.dataset.act === 'toggle') {
      const wasDone = t.done;
      if (!(await TaskStore.toggle(t))) return;
      if (!wasDone) TaskStore.notify("You've checked off the task!");
    } else if (btn.dataset.act === 'delete') {
      if (!confirm('Hapus tugas "' + t.title + '"?')) return;
      if (!(await TaskStore.remove(t))) return;
    } else { openEdit(t); return; }
    state.page = 1;
    render();
  });

  // add / edit overlay (shared: public/js/task-modal.js)
  function openEdit(t) {
    TaskModal.open(t, async (data, editing) => {
      const saved = editing ? await TaskStore.update(editing, data) : await TaskStore.create(data);
      if (!saved) return;
      state.page = 1;
      render();
    });
  }
  $('add-btn').addEventListener('click', e => { e.preventDefault(); openEdit(null); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });

  // priority dropdown
  const menu = $('prio-menu'), pbtn = $('prio-btn');
  function closeMenu() { menu.hidden = true; pbtn.setAttribute('aria-expanded', 'false'); }
  pbtn.addEventListener('click', e => { e.stopPropagation(); menu.hidden = !menu.hidden; pbtn.setAttribute('aria-expanded', String(!menu.hidden)); });
  menu.addEventListener('click', e => {
    const li = e.target.closest('li'); if (!li) return;
    state.priority = li.dataset.value;
    state.page = 1;
    $('prio-label').textContent = state.priority ? li.textContent : 'Prioritas';
    menu.querySelectorAll('li').forEach(x => x.classList.toggle('active', x === li && state.priority));
    closeMenu(); render();
  });
  document.addEventListener('click', closeMenu);

  render();
})();