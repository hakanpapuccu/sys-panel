@extends('dashboard.index')

@section('title', 'Anket Düzenle')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Anketler</a></li>
                <li class="breadcrumb-item active" aria-current="page">Anketi Düzenle</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Anketi Düzenle</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Dikkat:</strong> Anket sorularını ve seçeneklerini düzenlemek, mevcut yanıtlarla tutarsızlık yaratabileceği için şu an devre dışıdır. Sadece temel bilgileri güncelleyebilirsiniz.
                        </div>
                        <form action="{{ route('admin.polls.update', $poll->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Anket Başlığı</label>
                                    <input type="text" class="form-control" name="title" value="{{ $poll->title }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Açıklama</label>
                                    <textarea class="form-control" name="description" rows="1">{{ $poll->description }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ $poll->start_date ? $poll->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ $poll->end_date ? $poll->end_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $poll->is_active ? 'checked' : '' }} id="isActive">
                                        <label class="form-check-label" for="isActive">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
