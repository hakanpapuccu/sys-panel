@extends('dashboard.index')

@section('title', 'Yeni Kullanıcı')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kullanıcı Yönetimi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kullanıcı Ekle</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Kullanıcı Ekle</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Departman</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" name="department_id">
                                        <option value="">Seçiniz...</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Şifre</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Şifre Tekrar</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_admin">
                                            Süper Admin Yetkisi Ver (Tüm yetkilere sahip olur)
                                        </label>
                                    </div>
                                    @error('is_admin')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror

                                    <label class="form-label">Roller</label>
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Kullanıcı Oluştur</button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">İptal</a>
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
