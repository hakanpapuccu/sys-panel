@extends('dashboard.index')

@section('title', 'Rol Yönetimi')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rol Yönetimi</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Roller</h4>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Yeni Rol Ekle</a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                <strong>Başarılı!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th><strong>ROL ADI</strong></th>
                                        <th><strong>ETİKET</strong></th>
                                        <th><strong>İZİN SAYISI</strong></th>
                                        <th><strong>İŞLEMLER</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->label }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $role->permissions->count() }} İzin</span>
	                                            </td>
	                                            <td>
	                                                <div class="table-action-group">
	                                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary shadow btn-xs sharp action-btn" aria-label="{{ $role->label }} rolünü düzenle">
	                                                        <i class="fas fa-pencil-alt"></i>
	                                                    </a>
	                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu rolü silmek istediğinize emin misiniz?');">
	                                                        @csrf
	                                                        @method('DELETE')
	                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp action-btn" aria-label="{{ $role->label }} rolünü sil">
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
