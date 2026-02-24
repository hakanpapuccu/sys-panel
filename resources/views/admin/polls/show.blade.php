@extends('dashboard.index')

@section('title', 'Anket Detayı')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Anketler</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sonuçlar</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $poll->title }} - Sonuçlar</h4>
                        <span>Toplam Katılım: {{ $poll->responses->count() }}</span>
                    </div>
                    <div class="card-body">
                        @foreach($poll->questions as $question)
                        <div class="mb-5">
                            <h5>{{ $loop->iteration }}. {{ $question->question }}</h5>

                            @if(in_array($question->type, ['radio', 'checkbox']))
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" style="width: auto;">
                                        <thead>
                                            <tr>
                                                <th>Seçenek</th>
                                                <th>Oy Sayısı</th>
                                                <th>Oran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats[$question->id] as $option => $count)
                                            @php
                                                $total = $poll->responses->count();
                                                $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $option }}</td>
                                                <td>{{ $count }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">{{ $percent }}%</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                    <h6>Son 10 Cevap:</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach($question->answers()->with('response.user')->latest()->take(10)->get() as $answer)
                                        <li class="list-group-item">
                                            <strong>{{ $answer->response->user->name }}:</strong> {{ $answer->answer_text }}
                                        </li>
                                        @endforeach
                                    </ul>
                                    <small class="text-muted">Toplam {{ $stats[$question->id] }} cevap verildi.</small>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
