@extends('dashboard.index')

@section('title', 'Görevler')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Görevler</h4>
                        @if(Auth::user()->is_admin)
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Yeni Görev Oluştur</a>
                        @endif
                    </div>
	                    <div class="card-body">
	                        <div class="table-responsive">
	                            <table id="example3" class="display min-table-width-845">
                                <thead>
                                    <tr>
                                        <th>Başlık</th>
                                        <th>Öncelik</th>
                                        <th>Durum</th>
                                        <th>Atanan</th>
                                        <th>Son Tarih</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tasks.edit', $task->id) }}">
                                                <strong>{{ $task->title }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            @if($task->priority == 'high')
                                                <span class="badge badge-danger">Yüksek</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge badge-warning">Orta</span>
                                            @else
                                                <span class="badge badge-success">Düşük</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->status == 'completed')
                                                <span class="badge badge-success">Tamamlandı</span>
                                            @elseif($task->status == 'in_progress')
                                                <span class="badge badge-info">Devam Ediyor</span>
                                            @else
                                                <span class="badge badge-secondary">Bekliyor</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->assignedTo->name }}</td>
	                                        <td>{{ $task->deadline ? $task->deadline->format('d.m.Y H:i') : '-' }}</td>
	                                        <td>
	                                            <div class="table-action-group">
	                                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary shadow btn-xs sharp action-btn" aria-label="{{ $task->title }} görevini düzenle"><i class="fas fa-pencil-alt"></i></a>
	                                                @if(Auth::user()->is_admin)
	                                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Emin misiniz?');">
	                                                    @csrf
	                                                    @method('DELETE')
	                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp action-btn" aria-label="{{ $task->title }} görevini sil"><i class="fa fa-trash"></i></button>
	                                                </form>
	                                                @endif
	                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
