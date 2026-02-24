@extends('dashboard.index')

@section('title', 'Yeni Departman')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departman Yönetimi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Departman Ekle</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Departman Ekle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('admin.departments.store') }}" method="POST">
                                @csrf
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Departman Adı</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" class="form-control" placeholder="Örn: Bilgi İşlem" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Açıklama</label>
                                    <div class="col-sm-9">
                                        <textarea name="description" class="form-control" rows="4" placeholder="Departman açıklaması..."></textarea>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Kaydet</button>
                                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">İptal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
