<?php

namespace App\Console\Commands;

use App\Models\GarageVehicle;
use App\Models\ReminderLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class KirimPengingatServis extends Command
{
    protected $signature = 'servis:pengingat';
    protected $description = 'Kirim pengingat WhatsApp untuk kendaraan yang mendekati jadwal servis';

    public function handle(): int
    {
        $sent = 0;
        foreach (GarageVehicle::with('user')->get() as $v) {
            $p = $v->prediksi();
            if ($p['status'] === 'aman' || ! $v->user?->phone) continue;
            if (ReminderLog::where('garage_vehicle_id', $v->id)->where('created_at', '>=', now()->subDays(14))->exists()) continue;

            $pesan = "Halo {$v->user->name}, {$v->name} ({$v->plate}) diperkirakan perlu servis pada "
                . $p['tanggal']->translatedFormat('d F Y') . '. Booking di mejengauto: ' . url('/pelanggan/booking');

            $status = 'simulasi';
            if (config('mejeng.wa_token')) {
                $res = Http::withHeaders(['Authorization' => config('mejeng.wa_token')])
                    ->asForm()->post(config('mejeng.wa_url'), ['target' => $v->user->phone, 'message' => $pesan]);
                $status = $res->successful() ? 'terkirim' : 'gagal';
            }
            ReminderLog::create(['garage_vehicle_id' => $v->id, 'phone' => $v->user->phone, 'message' => $pesan, 'status' => $status]);
            $sent++;
        }
        $this->info("Pengingat diproses: {$sent}");
        return self::SUCCESS;
    }
}
