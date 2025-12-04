@extends('dashboard.index')

@section('title', 'Yeni Toplantı Oluştur')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.meetings.index') }}">Toplantı Yönetimi</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Yeni Toplantı</a></li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Zoom Toplantısı</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.meetings.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Topic -->
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Toplantı Konusu</label>
                                    <input type="text" class="form-control @error('topic') is-invalid @enderror" name="topic" value="{{ old('topic') }}" required autofocus placeholder="Örnek: Haftalık Değerlendirme Toplantısı">
                                    @error('topic')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Start Time -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Başlangıç Tarihi ve Saati</label>
                                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Süre (Dakika)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" name="duration" value="{{ old('duration', 60) }}" required min="1">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Agenda -->
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Gündem / Açıklama</label>
                                    <textarea class="form-control @error('agenda') is-invalid @enderror" name="agenda" rows="4">{{ old('agenda') }}</textarea>
                                    @error('agenda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Toplantı Oluştur</button>
                                    <a href="{{ route('admin.meetings.index') }}" class="btn btn-light">İptal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
