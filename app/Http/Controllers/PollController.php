<?php

namespace App\Http\Controllers;

use App\Models\PollAnswer;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polls = Poll::withCount('responses')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.polls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:text,textarea,radio,checkbox',
            'questions.*.options' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $poll = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => auth()->id(),
                'is_active' => $request->has('is_active'),
            ]);

            foreach ($request->questions as $index => $qData) {
                $question = $poll->questions()->create([
                    'question' => $qData['text'],
                    'type' => $qData['type'],
                    'order' => $index,
                ]);

                if (in_array($qData['type'], ['radio', 'checkbox']) && ! empty($qData['options'])) {
                    foreach ($qData['options'] as $optionText) {
                        if (! empty($optionText)) {
                            $question->options()->create(['option_text' => $optionText]);
                        }
                    }
                }
            }
        });

        return redirect()->route('admin.polls.index')->with('success', 'Anket başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Poll $poll)
    {
        $poll->load(['questions.options']);
        $questionIds = $poll->questions->pluck('id');
        $totalResponses = $poll->responses()->count();

        $optionCountRows = PollAnswer::query()
            ->whereIn('poll_question_id', $questionIds)
            ->whereNotNull('poll_option_id')
            ->selectRaw('poll_question_id, poll_option_id, COUNT(*) as total')
            ->groupBy('poll_question_id', 'poll_option_id')
            ->get()
            ->groupBy('poll_question_id')
            ->map(function ($rows) {
                return $rows->keyBy('poll_option_id');
            });

        $textCounts = PollAnswer::query()
            ->whereIn('poll_question_id', $questionIds)
            ->whereNull('poll_option_id')
            ->selectRaw('poll_question_id, COUNT(*) as total')
            ->groupBy('poll_question_id')
            ->pluck('total', 'poll_question_id');

        $textQuestionIds = $poll->questions
            ->whereIn('type', ['text', 'textarea'])
            ->pluck('id');

        $latestAnswersByQuestion = collect();
        if ($textQuestionIds->isNotEmpty()) {
            $latestAnswersByQuestion = PollAnswer::query()
                ->with(['response.user:id,name'])
                ->whereIn('poll_question_id', $textQuestionIds)
                ->whereNotNull('answer_text')
                ->latest()
                ->get(['id', 'poll_response_id', 'poll_question_id', 'answer_text', 'created_at'])
                ->groupBy('poll_question_id')
                ->map(function ($answers) {
                    return $answers->take(10)->values();
                });
        }

        // Calculate stats
        $stats = [];
        foreach ($poll->questions as $question) {
            if (in_array($question->type, ['radio', 'checkbox'])) {
                $optionCounts = [];
                $countsByOptionId = $optionCountRows->get($question->id, collect());

                foreach ($question->options as $option) {
                    $count = (int) optional($countsByOptionId->get($option->id))->total;
                    $optionCounts[$option->option_text] = $count;
                }
                $stats[$question->id] = $optionCounts;
            } else {
                $stats[$question->id] = (int) ($textCounts[$question->id] ?? 0);
            }
        }

        return view('admin.polls.show', compact('poll', 'stats', 'totalResponses', 'latestAnswersByQuestion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poll $poll)
    {
        $poll->load('questions.options');

        return view('admin.polls.edit', compact('poll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poll $poll)
    {
        // For simplicity, we might just update basic info and maybe add new questions.
        // Updating existing questions structure when responses exist is complex.
        // Here we'll allow full update but warn that it might affect data if implemented fully.
        // For now, let's just update basic info.

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $poll->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.polls.index')->with('success', 'Anket güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poll $poll)
    {
        $poll->delete();

        return redirect()->route('admin.polls.index')->with('success', 'Anket silindi.');
    }
}
