<div class="ov" id="task-modal" role="dialog" aria-modal="true" aria-labelledby="tm-title" hidden>
    <div class="ov-card">
        <form id="task-form">
            <div class="ov-body">
                <button type="button" class="ov-close" id="tm-close" aria-label="Tutup">
                    <svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
                <h2 id="tm-title">Tambah Tugas</h2>
                <p class="ov-sub">Lengkapi detail tugas yang ingin kamu catat.</p>

                <div class="ov-label">
                    <label for="task-title">Nama Tugas</label>
                    <span class="ov-req" id="f-req">Wajib diisi</span>
                </div>
                <input class="ov-input" id="task-title" type="text" name="title" placeholder="Contoh: Menyelesaikan laporan" autocomplete="off">

                <div class="ov-label">
                    <label for="task-desc">Deskripsi</label>
                </div>
                <textarea class="ov-input ov-area" id="task-desc" name="desc" rows="3" placeholder="Tambahkan catatan tugas (opsional)"></textarea>

                <div class="ov-grid">
                    <div>
                        <div class="ov-label">
                            <label for="task-category">Kategori</label>
                        </div>
                        <div class="ov-select">
                            <select id="task-category" name="category">
                                <option value="Pekerjaan">Pekerjaan</option>
                                <option value="Pribadi">Pribadi</option>
                                <option value="Kuliah & Belajar">Kuliah &amp; Belajar</option>
                            </select>
                            <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>
                    <div>
                        <div class="ov-label">
                            <span>Prioritas</span>
                        </div>
                        <div class="ov-prio">
                            <label>
                                <input type="radio" name="priority" value="low">
                                <span class="low"><i></i>Rendah</span>
                            </label>
                            <label>
                                <input type="radio" name="priority" value="mid" checked>
                                <span class="mid"><i></i>Sedang</span>
                            </label>
                            <label>
                                <input type="radio" name="priority" value="high">
                                <span class="high"><i></i>Tinggi</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="ov-label">
                    <label for="task-due">Tanggal &amp; Jam</label>
                </div>
                <label class="ov-date">
                    <svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/></svg>
                    <span id="f-due-text">Pilih tanggal &amp; jam</span>
                    <input id="task-due" type="datetime-local" name="due">
                </label>
            </div>

            <div class="ov-foot">
                <button type="button" class="ov-cancel" id="tm-cancel">Batal</button>
                <button type="submit" class="ov-save"><svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>Simpan Tugas</button>
            </div>
        </form>
    </div>
</div>
<div class="task-notification" id="task-notification" role="status" aria-live="polite" hidden></div>