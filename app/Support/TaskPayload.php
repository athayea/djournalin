<?php

namespace App\Support;

use App\Models\User;

class TaskPayload
{
    /**
     * The data the Dashboard and Semua Tugas pages hand to public/js/*.js.
     *
     * Logged in users get their own tasks from the database. Guests get demo tasks that
     * live in the browser's memory only, so nothing they do is saved.
     *
     * @return array<string, mixed>
     */
    public static function for(?User $user): array
    {
        $now = now();

        return [
            'tasks' => $user
                ? $user->tasks()->orderBy('due_at')->get()->map->toClientArray()->all()
                : self::sampleTasks(),
            'today' => $now->format('Y-m-d'),
            'tomorrow' => $now->copy()->addDay()->format('Y-m-d'),
            'tasksUrl' => route('tasks.index'),
            'isAuthenticated' => $user !== null,
            'taskEndpoint' => route('tasks.store'),
        ];
    }

    /**
     * Demo tasks shown to guests and used by the database seeder.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function sampleTasks(): array
    {
        $at = fn (int $days, int $hour, int $minute) => now()->addDays($days)->setTime($hour, $minute)->format('Y-m-d H:i');

        return [
            ['id' => 1, 'title' => 'Finalisasi Laporan Keuangan Q2', 'desc' => 'Kompilasi audit neraca laba rugi dan lampiran faktur pajak semester 1 bersama tim akuntansi.', 'priority' => 'high', 'category' => 'Pekerjaan', 'due' => $at(0, 16, 0), 'done' => false, 'doneAt' => null],
            ['id' => 2, 'title' => 'Daily Standup Meeting Tim Frontend', 'desc' => 'Sinkronisasi progres sprint 14 dan mitigasi kendala integrasi API autentikasi.', 'priority' => 'mid', 'category' => 'Pekerjaan', 'due' => $at(0, 9, 30), 'done' => true, 'doneAt' => '09:30'],
            ['id' => 3, 'title' => 'Beli Kebutuhan Rumah & Catatan Bulanan', 'desc' => 'Belanja kebutuhan pokok dapur dan filter air isi ulang di supermarket terdekat.', 'priority' => 'low', 'category' => 'Pribadi', 'due' => $at(1, 19, 0), 'done' => false, 'doneAt' => null],
            ['id' => 4, 'title' => 'Kumpul Tugas Algoritma & Struktur Data', 'desc' => 'Implementasi algoritma Dijkstra & Red-Black Tree dengan bahasa C++ di portal e-learning kampus.', 'priority' => 'mid', 'category' => 'Kuliah & Belajar', 'due' => $at(5, 23, 59), 'done' => false, 'doneAt' => null],
        ];
    }
}
