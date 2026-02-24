<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVacationRequest;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\Poll;
use App\Models\Task;
use App\Models\User;
use App\Models\Vacation;
use App\Notifications\VacationCreated;
use App\Notifications\VacationStatusUpdated;
use App\Support\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class VacationsController extends Controller
{
    public function index()
    {

        $vacations = Vacation::orderBy('vacation_date', 'desc')->where('vacation_user_id', '=', Auth::user()->id)->get();

        return view('dashboard.vacations', compact('vacations'));

    }

    public function show()
    {
        $user = Auth::user();

        $vacationScope = Vacation::query()->with('user:id,name');
        if (! $user->is_admin) {
            $vacationScope->where('vacation_user_id', $user->id);
        }

        $vacations = (clone $vacationScope)
            ->latest()
            ->take(5)
            ->get();

        $pendingVacationsCount = (clone $vacationScope)
            ->where('is_verified', Vacation::STATUS_PENDING)
            ->count();

        $taskScope = Task::query();
        if (! $user->is_admin) {
            $taskScope->where(function ($query) use ($user) {
                $query->where('assigned_to_id', $user->id)
                    ->orWhere('created_by_id', $user->id);
            });
        }

        $tasks = (clone $taskScope)
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'priority', 'deadline', 'status', 'created_at']);

        $taskCounts = (clone $taskScope)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingTasksCount = (int) ($taskCounts['pending'] ?? 0);
        $inProgressTasksCount = (int) ($taskCounts['in_progress'] ?? 0);
        $completedTasksCount = (int) ($taskCounts['completed'] ?? 0);
        $totalTasksCount = (int) $taskCounts->sum();

        // Other Stats
        $latestAnnouncements = Announcement::query()
            ->latest()
            ->take(3)
            ->get(['id', 'content', 'created_at']);
        $activePollsCount = Poll::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->count();

        $unreadMessagesCount = Message::query()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        $announcementsCount = Announcement::count();

        $pendingVacations = collect();
        if ($user->is_admin && $user->hasPermission('approve_vacations')) {
            $pendingVacations = Vacation::query()
                ->with('user:id,name')
                ->where('is_verified', Vacation::STATUS_PENDING)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard.content', compact(
            'vacations',
            'pendingVacations',
            'tasks',
            'pendingVacationsCount',
            'pendingTasksCount',
            'inProgressTasksCount',
            'completedTasksCount',
            'totalTasksCount',
            'latestAnnouncements',
            'activePollsCount',
            'unreadMessagesCount',
            'announcementsCount'
        ));
    }

    public function add(StoreVacationRequest $request)
    {
        $validated = $request->validated();

        $vacation = Vacation::create([
            'vacation_date' => $validated['vacation_date'],
            'vacation_why' => $validated['vacation_why'],
            'vacation_start' => $validated['vacation_start'],
            'vacation_end' => $validated['vacation_end'],
            'vacation_user_id' => Auth::id(),
            'is_verified' => Vacation::STATUS_PENDING,
        ]);

        Audit::record('vacation.created', $vacation, [], [
            'vacation_user_id' => $vacation->vacation_user_id,
            'vacation_date' => $vacation->vacation_date,
            'vacation_start' => $vacation->vacation_start,
            'vacation_end' => $vacation->vacation_end,
            'is_verified' => (int) $vacation->is_verified,
        ]);

        // Notify Admins
        $admins = User::where('is_admin', true)->get();
        Notification::send($admins, new VacationCreated($vacation));

        session()->flash('success', 'İzin başarıyla oluşturuldu');

        return redirect()->route('vacations');

    }

    public function verify(Vacation $vacation)
    {
        $this->authorize('approve', $vacation);

        if ((int) $vacation->is_verified !== Vacation::STATUS_PENDING) {
            session()->flash('error', 'Bu izin talebi zaten işlenmiş.');

            return redirect()->route('dashboard');
        }

        $oldValues = [
            'is_verified' => (int) $vacation->is_verified,
            'vacation_verifier_id' => $vacation->vacation_verifier_id,
        ];
        $vacation->is_verified = Vacation::STATUS_APPROVED;
        $vacation->vacation_verifier_id = Auth::id();
        $vacation->save();
        Audit::record('vacation.approved', $vacation, $oldValues, [
            'is_verified' => (int) $vacation->is_verified,
            'vacation_verifier_id' => $vacation->vacation_verifier_id,
        ]);

        // Notify User
        $vacation->user->notify(new VacationStatusUpdated($vacation, 'approved'));

        session()->flash('success', 'İzin onaylandı.');

        return redirect()->route('dashboard');
    }

    public function reject(Vacation $vacation)
    {
        $this->authorize('approve', $vacation);

        if ((int) $vacation->is_verified !== Vacation::STATUS_PENDING) {
            session()->flash('error', 'Bu izin talebi zaten işlenmiş.');

            return redirect()->route('dashboard');
        }

        $oldValues = [
            'is_verified' => (int) $vacation->is_verified,
            'vacation_verifier_id' => $vacation->vacation_verifier_id,
        ];
        $vacation->is_verified = Vacation::STATUS_REJECTED;
        $vacation->vacation_verifier_id = Auth::id();
        $vacation->save();
        Audit::record('vacation.rejected', $vacation, $oldValues, [
            'is_verified' => (int) $vacation->is_verified,
            'vacation_verifier_id' => $vacation->vacation_verifier_id,
        ]);

        // Notify User
        $vacation->user->notify(new VacationStatusUpdated($vacation, 'rejected'));

        session()->flash('success', 'İzin reddedildi.');

        return redirect()->route('dashboard');
    }
}
