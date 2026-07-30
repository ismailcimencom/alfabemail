<?php

namespace App\Services;

use App\Models\MailAktiviteLog;
use App\Models\Ogrenci;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VeliAnalizService
{
    public function analyze(Ogrenci $ogrenci): array
    {
        $logs = MailAktiviteLog::where('ogrenci_id', $ogrenci->id)
            ->get();

        $weeklyRaw = $this->weeklyBreakdown($logs, $ogrenci);
        $insights = $this->generateInsights($logs, $ogrenci);
        $summary = $this->generateSummary($logs, $insights);

        return [
            'weekly_traffic' => $weeklyRaw,
            'insights' => $insights,
            'summary' => $summary,
            'logs' => $logs,
        ];
    }

    private function weeklyBreakdown(Collection $logs, Ogrenci $ogrenci): array
    {
        $days = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
        $weekly = [];

        if ($logs->isEmpty()) {
            foreach ($days as $day) {
                $weekly[] = ['day' => $day, 'count' => 0];
            }
            return $weekly;
        }

        $grouped = $logs->groupBy(fn ($log) => Carbon::parse($log->tarih)->dayOfWeek);

        foreach (range(1, 7) as $i) {
            $dayIndex = $i % 7;
            $weekly[] = [
                'day' => $days[$dayIndex],
                'count' => $grouped->get($dayIndex, collect())->count(),
            ];
        }

        return $weekly;
    }

    private function generateInsights(Collection $logs, Ogrenci $ogrenci): array
    {
        $incomingCount = $logs->where('tip', 'alinan')->count();
        $outgoingCount = $logs->where('tip', 'gonderilen')->count();

        $allContacts = collect();
        foreach ($logs as $log) {
            if ($log->tip === 'gonderilen' && $log->kime) {
                $allContacts->push($log->kime);
            } elseif ($log->tip === 'alinan' && $log->kimden) {
                $allContacts->push($log->kimden);
            }
        }

        $topContacts = $allContacts->countBy()->sortDesc()->take(3);

        $busiestDay = $logs->groupBy(fn ($l) => Carbon::parse($l->tarih)->locale('tr')->dayName)
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        $hourly = $logs->groupBy(fn ($l) => Carbon::parse($l->tarih)->format('H'));
        $busiestHour = $hourly->map->count()->sortDesc()->keys()->first();

        return [
            'total_emails' => $logs->count(),
            'incoming' => $incomingCount,
            'outgoing' => $outgoingCount,
            'top_contacts' => $topContacts->keys()->take(3)->toArray(),
            'busiest_day' => $busiestDay,
            'busiest_hour' => $busiestHour ? $busiestHour . ':00' : null,
            'unique_contacts' => $allContacts->unique()->count(),
            'hourly_distribution' => $hourly->map(fn ($g) => $g->count())->toArray(),
        ];
    }

    private function generateSummary(Collection $logs, array $insights): string
    {
        $total = $insights['total_emails'];
        if ($total === 0) {
            return 'Henüz mail aktivitesi bulunmuyor.';
        }

        $parts = [];
        $parts[] = "Toplam {$total} e-posta aktivitesi tespit edildi.";
        $parts[] = "Gönderilen: {$insights['outgoing']}, Alınan: {$insights['incoming']}.";

        if ($insights['busiest_day']) {
            $parts[] = "En yoğun gün: {$insights['busiest_day']}.";
        }
        if ($insights['busiest_hour']) {
            $parts[] = "En aktif saat dilimi: {$insights['busiest_hour']} civarı.";
        }
        if ($insights['unique_contacts'] > 0) {
            $parts[] = "Farklı {$insights['unique_contacts']} kişiyle iletişim kurulmuş.";
        }

        return implode(' ', $parts);
    }
}
