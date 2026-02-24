<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PollResponseController extends Controller
{
    public function index()
    {
        $polls = Poll::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->with(['responses' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->get();

        return view('polls.index', compact('polls'));
    }

    public function show(Poll $poll)
    {
        if (! $this->isPollOpen($poll)) {
            return redirect()->route('polls.index')->with('error', 'Bu anket şu anda aktif değil.');
        }

        // Check if user already responded
        if ($poll->responses()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('polls.index')->with('error', 'Bu ankete zaten katıldınız.');
        }

        $poll->load('questions.options');
        return view('polls.show', compact('poll'));
    }

    public function store(Request $request, Poll $poll)
    {
        if (! $this->isPollOpen($poll)) {
            return redirect()->route('polls.index')->with('error', 'Bu anket şu anda aktif değil.');
        }

        // Check if user already responded
        if ($poll->responses()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('polls.index')->with('error', 'Bu ankete zaten katıldınız.');
        }

        $rules = [];
        foreach ($poll->questions as $question) {
            if ($question->type == 'checkbox') {
                $rules["q_{$question->id}"] = 'nullable|array';
                $rules["q_{$question->id}.*"] = [
                    'integer',
                    Rule::exists('poll_options', 'id')->where(function ($query) use ($question) {
                        $query->where('poll_question_id', $question->id);
                    }),
                ];
            } elseif ($question->type == 'radio') {
                $rules["q_{$question->id}"] = [
                    'required',
                    'integer',
                    Rule::exists('poll_options', 'id')->where(function ($query) use ($question) {
                        $query->where('poll_question_id', $question->id);
                    }),
                ];
            } else {
                $rules["q_{$question->id}"] = 'required|string|max:1000';
            }
        }
        $request->validate($rules);

        DB::transaction(function () use ($request, $poll) {
            $response = $poll->responses()->create([
                'user_id' => auth()->id(),
            ]);

            foreach ($poll->questions as $question) {
                $inputName = "q_{$question->id}";
                $value = $request->input($inputName);

                if ($question->type == 'checkbox' && is_array($value)) {
                    foreach ($value as $optionId) {
                        $response->answers()->create([
                            'poll_question_id' => $question->id,
                            'poll_option_id' => $optionId,
                        ]);
                    }
                } elseif ($question->type == 'radio') {
                    $response->answers()->create([
                        'poll_question_id' => $question->id,
                        'poll_option_id' => $value,
                    ]);
                } else {
                    $response->answers()->create([
                        'poll_question_id' => $question->id,
                        'answer_text' => $value,
                    ]);
                }
            }
        });

        return redirect()->route('polls.index')->with('success', 'Anket yanıtınız kaydedildi. Teşekkürler!');
    }

    private function isPollOpen(Poll $poll): bool
    {
        if (! $poll->is_active) {
            return false;
        }

        if ($poll->start_date && $poll->start_date->isFuture()) {
            return false;
        }

        if ($poll->end_date && $poll->end_date->isPast()) {
            return false;
        }

        return true;
    }
}
