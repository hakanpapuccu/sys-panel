@extends('dashboard.index')

@section('title', 'Toplantılar')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Toplantılar</li>
            </ol>
        </div>

        <div class="row">
            @foreach($meetings as $meeting)
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">{{ $meeting->topic }}</h4>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Tarih</strong>
                                    <span class="mb-0">{{ $meeting->start_time->format('d.m.Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Süre</strong>
                                    <span class="mb-0">{{ $meeting->duration }} dk</span>
                                </li>
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Meeting ID</strong>
                                    <span class="mb-0">{{ $meeting->meeting_id }}</span>
                                </li>
                                @if($meeting->password)
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Şifre</strong>
                                    <span class="mb-0">{{ $meeting->password }}</span>
                                </li>
                                @endif
                            </ul>
                            @if($meeting->agenda)
                                <p class="mt-3 text-muted">{{ $meeting->agenda }}</p>
                            @endif
                        </div>
                        <div class="card-footer border-0 pt-0 pb-4">
                            <a href="{{ $meeting->join_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 btn-rounded">
                                <i class="fas fa-video me-2"></i> Toplantıya Katıl
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($meetings->isEmpty())
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <p>Planlanmış toplantı bulunmamaktadır.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
