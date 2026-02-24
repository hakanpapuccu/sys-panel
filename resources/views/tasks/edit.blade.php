@extends('dashboard.index')

@section('title', 'Görev Düzenle')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Görevi Düzenle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" name="title" class="form-control" value="{{ $task->title }}" {{ !Auth::user()->is_admin ? 'disabled' : 'required' }}>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Durum</label>
                                        <select name="status" class="form-control default-select form-control-wide">
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Öncelik</label>
                                        <select name="priority" class="form-control default-select form-control-wide" {{ !Auth::user()->is_admin ? 'disabled' : '' }}>
                                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Düşük</option>
                                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Orta</option>
                                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>Yüksek</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Atanacak Kişi</label>
                                        <select name="assigned_to_id" class="form-control default-select form-control-wide" {{ !Auth::user()->is_admin ? 'disabled' : '' }}>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $task->assigned_to_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Son Tarih</label>
                                        <input type="datetime-local" name="deadline" class="form-control" value="{{ $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '' }}" {{ !Auth::user()->is_admin ? 'disabled' : '' }}>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Açıklama</label>
                                        @if(Auth::user()->is_admin)
                                            <textarea name="description" id="ckeditor" class="form-control" rows="4">{{ $task->description }}</textarea>
                                        @else
                                            <div class="card-body border rounded" style="background-color: #f8f9fa; min-height: 100px;">
                                                {!! nl2br(e($task->description ?? '')) !!}
                                            </div>
                                        @endif
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
</div>

@if(Auth::user()->is_admin)
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('ckeditor');
</script>
@endif
@endsection
