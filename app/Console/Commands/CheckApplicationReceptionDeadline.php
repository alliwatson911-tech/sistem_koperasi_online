<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;

class CheckApplicationReceptionDeadline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'koperasi:check-reception-deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa pengajuan pinjaman yang belum diterima teller melebihi batas waktu toleransi dan kirimkan pengingat ke pengurus/manajer';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deadlineHours = (int) Setting::get('application_reception_deadline_hours', 24);
        $threshold = now()->subHours($deadlineHours);

        $this->info("Memeriksa pengajuan pinjaman yang diajukan sebelum {$threshold->toDateTimeString()} (batas: {$deadlineHours} jam)...");

        $overdueApplications = LoanApplication::where('status', LoanApplication::STATUS_SUBMITTED)
            ->where('submitted_at', '<=', $threshold)
            ->with(['member.user', 'assignedTo'])
            ->get();

        if ($overdueApplications->isEmpty()) {
            $this->info('Tidak ada pengajuan pinjaman yang melewati batas waktu penerimaan teller.');
            return Command::SUCCESS;
        }

        $supervisors = User::whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->get();

        $notifiedCount = 0;

        foreach ($overdueApplications as $app) {
            $applicantName = $app->member->user->name ?? 'Anggota';
            $assignedInfo = $app->assignedTo ? "ditugaskan ke {$app->assignedTo->name} ({$app->assignedTo->employee_code_formatted})" : 'masih dalam antrean terbuka';
            $link = route('loans.applications.show', $app->id);

            // Cek apakah notifikasi pengingat sudah pernah dikirim dalam 12 jam terakhir untuk mencegah duplikasi
            $alreadyNotified = Notification::where('link', $link)
                ->where('title', 'like', '%Batas Waktu Penerimaan Teller%')
                ->where('created_at', '>=', now()->subHours(12))
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            foreach ($supervisors as $supervisor) {
                Notification::create([
                    'user_id' => $supervisor->id,
                    'title' => "Peringatan: Batas Waktu Penerimaan Teller Terlewati",
                    'message' => "Pengajuan pinjaman #{$app->application_no} ({$applicantName}) belum diterima oleh Teller melebihi batas waktu {$deadlineHours} jam ({$assignedInfo}). Silakan tinjau atau alihkan penugasan.",
                    'type' => 'warning',
                    'link' => $link,
                ]);
            }

            $notifiedCount++;
            $this->warn("Pengingat dikirim untuk pengajuan #{$app->application_no} ({$applicantName}) ke " . $supervisors->count() . " pengurus.");
        }

        $this->info("Pemeriksaan selesai. Sebanyak {$notifiedCount} pengajuan yang tertunda telah diberikan pengingat.");

        return Command::SUCCESS;
    }
}

