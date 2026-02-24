@extends('dashboard.index')

@section('title', 'Anket Yönetimi')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Anket Yönetimi</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Anket Listesi</h4>
                        <a href="{{ route('admin.polls.create') }}" class="btn btn-primary">Yeni Anket Oluştur</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Başlık</th>
                                        <th>Başlangıç</th>
                                        <th>Bitiş</th>
                                        <th>Durum</th>
                                        <th>Katılım</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($polls as $poll)
                                    <tr>
                                        <td>{{ $poll->title }}</td>
                                        <td>{{ $poll->start_date ? $poll->start_date->format('d.m.Y') : '-' }}</td>
                                        <td>{{ $poll->end_date ? $poll->end_date->format('d.m.Y') : '-' }}</td>
                                        <td>
                                            @if($poll->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $poll->responses_count }}</td>
                                        <td>
                                            <div class="table-action-group">
                                                <a href="{{ route('admin.polls.show', $poll->id) }}" class="btn btn-info shadow btn-xs sharp action-btn" title="Sonuçlar" aria-label="{{ $poll->title }} anket sonuçlarını görüntüle">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="{{ route('admin.polls.edit', $poll->id) }}" class="btn btn-primary shadow btn-xs sharp action-btn" title="Düzenle" aria-label="{{ $poll->title }} anketini düzenle">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.polls.destroy', $poll->id) }}" method="POST" onsubmit="return confirm('Bu anketi silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp action-btn" aria-label="{{ $poll->title }} anketini sil"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $polls->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
