@extends('dashboard.index')

@section('title', 'Görev Detayı')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ $task->title }}</h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">Geri Dön</a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Öncelik:</strong>
                            <span class="badge badge-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                {{ strtoupper($task->priority) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Durum:</strong> {{ $task->status }}
                        </div>
                        <div class="mb-3">
                            <strong>Atanan:</strong> {{ $task->assignedTo->name ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <strong>Oluşturan:</strong> {{ $task->createdBy->name ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <strong>Son Tarih:</strong> {{ $task->deadline ? $task->deadline->format('d.m.Y H:i') : '-' }}
                        </div>
                        <div class="mb-0">
                            <strong>Açıklama:</strong>
                            <div class="border rounded p-3 mt-2 bg-light">
                                {!! nl2br(e($task->description ?? '-')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
