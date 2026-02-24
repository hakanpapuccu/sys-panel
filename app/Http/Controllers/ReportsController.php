<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\FileShare;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\Task;
use App\Models\User;
use App\Models\Vacation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $filters] = $this->resolveDateRange($request);
        $report = $this->buildReport($from, $to);

        return view('admin.reports.index', array_merge($report, [
            'filters' => $filters,
            'periodLabel' => $from->format('d.m.Y').' - '.$to->format('d.m.Y'),
            'periodDays' => $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1,
        ]));
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $report = $this->buildReport($from, $to);

        $filename = sprintf('raporlama-%s-%s.csv', $from->format('Ymd'), $to->format('Ymd'));

        return response()->streamDownload(function () use ($report, $from, $to): void {
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, ['Rapor Periyodu', $from->format('d.m.Y'), $to->format('d.m.Y')]);
            fputcsv($output, []);

            fputcsv($output, ['Genel Metrikler', 'Deger']);
            foreach ($this->metricRowsForCsv($report['metrics']) as $row) {
                fputcsv($output, $row);
            }

            fputcsv($output, []);
            fputcsv($output, ['Gunluk Aktivite']);
            fputcsv($output, ['Tarih', 'Gorev', 'Izin', 'Mesaj', 'Duyuru', 'Dosya', 'Toplanti', 'Toplam']);
            foreach ($report['dailyActivity'] as $row) {
                fputcsv($output, [
                    $row['date'],
                    $row['tasks'],
                    $row['vacations'],
                    $row['messages'],
                    $row['announcements'],
                    $row['files'],
                    $row['meetings'],
                    $row['total'],
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();

        return [$from, $to, [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]];
    }

    private function buildReport(Carbon $from, Carbon $to): array
    {
        $taskQuery = Task::query()->whereBetween('created_at', [$from, $to]);
        $taskStatusCounts = (clone $taskQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $vacationQuery = Vacation::query()->whereBetween('created_at', [$from, $to]);
        $vacationStatusCounts = (clone $vacationQuery)
            ->selectRaw('is_verified, COUNT(*) as total')
            ->groupBy('is_verified')
            ->pluck('total', 'is_verified');

        $tasksCreated = (int) $taskStatusCounts->sum();
        $tasksCompleted = (int) ($taskStatusCounts['completed'] ?? 0);
        $vacationRequests = (int) $vacationStatusCounts->sum();
        $pollsCreated = Poll::query()->whereBetween('created_at', [$from, $to])->count();
        $pollResponses = PollResponse::query()->whereBetween('created_at', [$from, $to])->count();

        $metrics = [
            'users_created' => User::query()->whereBetween('created_at', [$from, $to])->count(),
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted,
            'open_overdue_tasks' => Task::query()
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'vacation_requests' => $vacationRequests,
            'vacation_approved' => (int) ($vacationStatusCounts[Vacation::STATUS_APPROVED] ?? 0),
            'vacation_pending' => (int) ($vacationStatusCounts[Vacation::STATUS_PENDING] ?? 0),
            'vacation_rejected' => (int) ($vacationStatusCounts[Vacation::STATUS_REJECTED] ?? 0),
            'announcements_created' => Announcement::query()->whereBetween('created_at', [$from, $to])->count(),
            'files_uploaded' => FileShare::query()->whereBetween('created_at', [$from, $to])->count(),
            'messages_sent' => Message::query()->whereBetween('created_at', [$from, $to])->count(),
            'polls_created' => $pollsCreated,
            'poll_responses' => $pollResponses,
            'active_polls_now' => Poll::query()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('start_date')->orWhereDate('start_date', '<=', now()->toDateString());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
                })
                ->count(),
            'meetings_created' => Meeting::query()->whereBetween('created_at', [$from, $to])->count(),
        ];

        $taskStatusBreakdown = $this->buildTaskStatusBreakdown($taskStatusCounts, $tasksCreated);
        $vacationStatusBreakdown = $this->buildVacationStatusBreakdown($vacationStatusCounts, $vacationRequests);

        $topTaskCreators = Task::query()
            ->selectRaw('created_by_id, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('created_by_id')
            ->orderByDesc('total')
            ->with('createdBy:id,name')
            ->limit(5)
            ->get();

        $topFileUploaders = FileShare::query()
            ->selectRaw('user_id, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,name')
            ->limit(5)
            ->get();

        return [
            'metrics' => $metrics,
            'taskStatusBreakdown' => $taskStatusBreakdown,
            'vacationStatusBreakdown' => $vacationStatusBreakdown,
            'dailyActivity' => $this->buildDailyActivity($from, $to),
            'topTaskCreators' => $topTaskCreators,
            'topFileUploaders' => $topFileUploaders,
        ];
    }

    private function buildTaskStatusBreakdown($taskStatusCounts, int $total): array
    {
        $statuses = [
            'pending' => ['label' => 'Bekliyor', 'class' => 'secondary'],
            'in_progress' => ['label' => 'Devam Ediyor', 'class' => 'info'],
            'completed' => ['label' => 'Tamamlandı', 'class' => 'success'],
        ];

        $rows = [];
        foreach ($statuses as $status => $meta) {
            $count = (int) ($taskStatusCounts[$status] ?? 0);
            $rows[] = [
                'label' => $meta['label'],
                'class' => $meta['class'],
                'count' => $count,
                'percentage' => $this->percentage($count, $total),
            ];
        }

        return $rows;
    }

    private function buildVacationStatusBreakdown($vacationStatusCounts, int $total): array
    {
        $statuses = [
            Vacation::STATUS_PENDING => ['label' => 'Beklemede', 'class' => 'warning'],
            Vacation::STATUS_APPROVED => ['label' => 'Onaylandı', 'class' => 'success'],
            Vacation::STATUS_REJECTED => ['label' => 'Reddedildi', 'class' => 'danger'],
        ];

        $rows = [];
        foreach ($statuses as $status => $meta) {
            $count = (int) ($vacationStatusCounts[$status] ?? 0);
            $rows[] = [
                'label' => $meta['label'],
                'class' => $meta['class'],
                'count' => $count,
                'percentage' => $this->percentage($count, $total),
            ];
        }

        return $rows;
    }

    private function buildDailyActivity(Carbon $from, Carbon $to): array
    {
        $taskCounts = $this->countByDay('tasks', $from, $to);
        $vacationCounts = $this->countByDay('vacations', $from, $to);
        $messageCounts = $this->countByDay('messages', $from, $to);
        $announcementCounts = $this->countByDay('announcements', $from, $to);
        $fileCounts = $this->countByDay('files', $from, $to);
        $meetingCounts = $this->countByDay('meetings', $from, $to);

        $rows = [];
        foreach (CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()) as $date) {
            $key = $date->format('Y-m-d');
            $tasks = (int) ($taskCounts[$key] ?? 0);
            $vacations = (int) ($vacationCounts[$key] ?? 0);
            $messages = (int) ($messageCounts[$key] ?? 0);
            $announcements = (int) ($announcementCounts[$key] ?? 0);
            $files = (int) ($fileCounts[$key] ?? 0);
            $meetings = (int) ($meetingCounts[$key] ?? 0);

            $rows[] = [
                'date' => $date->format('d.m.Y'),
                'tasks' => $tasks,
                'vacations' => $vacations,
                'messages' => $messages,
                'announcements' => $announcements,
                'files' => $files,
                'meetings' => $meetings,
                'total' => $tasks + $vacations + $messages + $announcements + $files + $meetings,
            ];
        }

        return $rows;
    }

    private function countByDay(string $table, Carbon $from, Carbon $to): array
    {
        return DB::table($table)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    private function percentage(int $part, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($part / $total) * 100, 1);
    }

    private function metricRowsForCsv(array $metrics): array
    {
        return [
            ['Yeni Kullanici', $metrics['users_created']],
            ['Olusturulan Gorev', $metrics['tasks_created']],
            ['Tamamlanan Gorev', $metrics['tasks_completed']],
            ['Geciken Acik Gorev (Anlik)', $metrics['open_overdue_tasks']],
            ['Izin Talebi', $metrics['vacation_requests']],
            ['Onaylanan Izin', $metrics['vacation_approved']],
            ['Bekleyen Izin', $metrics['vacation_pending']],
            ['Reddedilen Izin', $metrics['vacation_rejected']],
            ['Yayinlanan Duyuru', $metrics['announcements_created']],
            ['Yuklenen Dosya', $metrics['files_uploaded']],
            ['Gonderilen Mesaj', $metrics['messages_sent']],
            ['Olusturulan Anket', $metrics['polls_created']],
            ['Anket Yaniti', $metrics['poll_responses']],
            ['Aktif Anket (Anlik)', $metrics['active_polls_now']],
            ['Olusturulan Toplanti', $metrics['meetings_created']],
        ];
    }
}
