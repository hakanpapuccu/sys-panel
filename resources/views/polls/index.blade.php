@extends('dashboard.index')

@section('title', 'Anketler')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Anketler</li>
            </ol>
        </div>

        <div class="row">
            @forelse($polls as $poll)
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">{{ $poll->title }}</h4>
                    </div>
                    <div class="card-body pb-0">
                        <p>{{ $poll->description }}</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex px-0 justify-content-between">
                                <strong>Bitiş Tarihi:</strong>
                                <span class="mb-0">{{ $poll->end_date ? $poll->end_date->format('d.m.Y') : 'Süresiz' }}</span>
                            </li>
                            <li class="list-group-item d-flex px-0 justify-content-between">
                                <strong>Soru Sayısı:</strong>
                                <span class="mb-0">{{ $poll->questions->count() }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer border-0 pt-0 pb-4">
                        @if($poll->responses->isNotEmpty())
                            <button class="btn btn-success btn-block" disabled>Katıldınız</button>
                        @else
                            <a href="{{ route('polls.show', $poll->id) }}" class="btn btn-primary btn-block">Katıl</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">Şu anda aktif bir anket bulunmamaktadır.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
