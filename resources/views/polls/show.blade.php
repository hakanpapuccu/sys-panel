@extends('dashboard.index')

@section('title', 'Anket Cevapla')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('polls.index') }}">Anketler</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $poll->title }}</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $poll->title }}</h4>
                    </div>
                    <div class="card-body">
                        @if($poll->description)
                            <p class="mb-4">{{ $poll->description }}</p>
                        @endif

                        <form action="{{ route('polls.store', $poll->id) }}" method="POST">
                            @csrf

                            @foreach($poll->questions as $question)
                            <div class="mb-4 p-3 border rounded">
                                <h5 class="mb-3">{{ $loop->iteration }}. {{ $question->question }}</h5>

                                @if($question->type == 'text')
                                    <input type="text" class="form-control" name="q_{{ $question->id }}" required>
                                @elseif($question->type == 'textarea')
                                    <textarea class="form-control" name="q_{{ $question->id }}" rows="3" required></textarea>
                                @elseif($question->type == 'radio')
                                    @foreach($question->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q_{{ $question->id }}" id="opt_{{ $option->id }}" value="{{ $option->id }}" required>
                                        <label class="form-check-label" for="opt_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                @elseif($question->type == 'checkbox')
                                    @foreach($question->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="q_{{ $question->id }}[]" id="opt_{{ $option->id }}" value="{{ $option->id }}">
                                        <label class="form-check-label" for="opt_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary btn-lg">Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
