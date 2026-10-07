@extends('layouts.app')
@section('title', 'Dashboard - Journalin')
@section('content_class', 'dash')
@section('content')
<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p class="muted">Ringkasan produktivitas dan fokus aktivitas hari ini.</p>
    </div>
    <a href="#" class="btn-primary" id="add-btn"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Tambah Tugas Baru</a>
</div>

<div class="stats">
    <div class="stat"><div><span class="stat-label">TOTAL TUGAS</span><b id="st-total">0</b><small class="pos" id="st-week"></small></div><div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9zM9 12h6M9 16h6"/></svg></div></div>
    <div class="stat"><div><span class="stat-label">TENGGAT HARI INI</span><b id="st-today">0</b><small class="neg" id="st-note"></small></div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 4 3 20h18zM12 10v4"/></svg></div></div>
    <div class="stat"><div><span class="stat-label">TINGKAT SELESAI</span><b id="st-rate">0%</b><div class="bar"><i id="st-bar"></i></div></div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M13 3 5 14h6l-1 7 8-11h-6z"/></svg></div></div>
</div>

<div class="dash-grid">
    <section>
        <div class="panel-head"><h2>Prioritas Hari Ini</h2><a class="link" href="{{ route('tasks.index') }}">Lihat Semua <svg viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg></a></div>
        <div id="today-list"></div>
    </section>
    <div class="side-col">
        <div class="card">
            <div class="cal-head"><span id="cal-title"></span><div><button type="button" id="cal-prev" aria-label="Bulan sebelumnya"><svg viewBox="0 0 24 24"><path d="m15 6-6 6 6 6"/></svg></button><button type="button" id="cal-next" aria-label="Bulan berikutnya"><svg viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg></button></div></div>
            <div class="cal-grid" id="cal-grid"></div>
        </div>
        <div class="card" style="margin-top: 16px;">
            <div class="card-title" style="margin-bottom: 20px;">DISTRIBUSI KATEGORI</div>
            <div id="cat-dist-list"></div>
        </div>
    </div>
</div>
@include('partials.task-modal')
@endsection

@push('scripts')
<script type="application/json" id="journalin-data">@json($journalinData)</script>
<script src="{{ asset('js/task-modal.js') }}"></script>
<script src="{{ asset('js/task-store.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush