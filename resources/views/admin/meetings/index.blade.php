@extends('dashboard.index')

@section('title', 'Toplantı Yönetimi')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Toplantı Yönetimi</a></li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Toplantılar</h4>
                        <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary">Yeni Toplantı Oluştur</a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                <strong>Başarılı!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                <strong>Hata!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th><strong>KONU</strong></th>
                                        <th><strong>TARİH & SAAT</strong></th>
                                        <th><strong>SÜRE</strong></th>
                                        <th><strong>DURUM</strong></th>
                                        <th><strong>İŞLEMLER</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($meetings as $meeting)
                                        <tr>
                                            <td>{{ $meeting->topic }}</td>
                                            <td>{{ $meeting->start_time->format('d.m.Y H:i') }}</td>
                                            <td>{{ $meeting->duration }} dk</td>
                                            <td>
                                                @if($meeting->start_time->isPast())
                                                    <span class="badge badge-secondary">Tamamlandı</span>
                                                @else
                                                    <span class="badge badge-success">Planlandı</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @if($meeting->start_url)
                                                        <a href="{{ $meeting->start_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-success shadow btn-xs sharp me-1" title="Toplantıyı Başlat">
                                                            <i class="fas fa-video"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('admin.meetings.destroy', $meeting) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu toplantıyı silmek istediğinize emin misiniz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $meetings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
