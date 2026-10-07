@extends('layouts.app')
@section('title', 'Semua Tugas - Journalin')
@section('content')
<div class="page-head">
    <div>
        <h1>Semua Tugas</h1>
        <p class="muted">Kelola dan atur kegiatan harian dengan lebih terstruktur.</p>
    </div>
    <a href="#" class="btn-primary" id="add-btn"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Tambah Tugas Baru</a>
</div>

<div class="filter-bar">
    <span class="filter-label">FILTER:</span>
    <button type="button" class="chip active" data-status="all" aria-pressed="true">Semua</button>
    <button type="button" class="chip" data-status="pending" aria-pressed="false">Belum Selesai</button>
    <button type="button" class="chip" data-status="completed" aria-pressed="false">Selesai</button>
    <div class="dropdown" id="prio-dd">
        <button type="button" class="select" id="prio-btn" aria-expanded="false"><span id="prio-label">Prioritas</span> <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></button>
        <ul class="menu" id="prio-menu" hidden>
            <li data-value="">Semua Prioritas</li>
            <li data-value="high">Tinggi</li>
            <li data-value="mid">Sedang</li>
            <li data-value="low">Rendah</li>
        </ul>
    </div>
</div>

<div id="task-list"></div>

<div class="pagination" id="task-pagination" aria-label="Navigasi halaman tugas" hidden></div>

@include('partials.task-modal')
@endsection

@push('scripts')
<script type="application/json" id="journalin-data">@json($journalinData)</script>
<script src="{{ asset('js/task-modal.js') }}"></script>
<script src="{{ asset('js/task-store.js') }}"></script>
<script src="{{ asset('js/tasks.js') }}"></script>
@endpush