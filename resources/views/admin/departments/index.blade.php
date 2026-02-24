@extends('dashboard.index')

@section('title', 'Departman Yönetimi')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Departman Yönetimi</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Departmanlar</h4>
                        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Departman Ekle</a>
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
                                        <th><strong>AD</strong></th>
                                        <th><strong>AÇIKLAMA</strong></th>
                                        <th><strong>KULLANICI SAYISI</strong></th>
                                        <th><strong>İŞLEMLER</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $department)
                                        <tr>
                                            <td>{{ $department->name }}</td>
                                            <td>{{ Str::limit($department->description, 50) }}</td>
	                                            <td><span class="badge badge-info">{{ $department->users_count }}</span></td>
	                                            <td>
	                                                <div class="table-action-group">
	                                                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary shadow btn-xs sharp action-btn" aria-label="{{ $department->name }} departmanını düzenle">
	                                                        <i class="fas fa-pencil-alt"></i>
	                                                    </a>
	                                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu departmanı silmek istediğinize emin misiniz?');">
	                                                        @csrf
	                                                        @method('DELETE')
	                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp action-btn" aria-label="{{ $department->name }} departmanını sil">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
