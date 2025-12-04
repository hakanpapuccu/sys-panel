@extends('dashboard.index')

@section('title', 'Yeni Rol Ekle')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rol Yönetimi</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Yeni Rol Ekle</a></li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Rol Ekle</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.roles.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Name -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Rol Kodu (İngilizce, boşluksuz)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Örnek: hr_manager">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Label -->
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Rol Adı (Görünecek İsim)</label>
                                    <input type="text" class="form-control @error('label') is-invalid @enderror" name="label" value="{{ old('label') }}" required placeholder="Örnek: İK Yöneticisi">
                                    @error('label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Permissions -->
                                <div class="col-lg-12 mb-3">
                                    <h4 class="mb-3">İzinler</h4>
                                    
                                    <div class="row">
                                        @foreach($permissions as $module => $modulePermissions)
                                            <div class="col-md-4 mb-4">
                                                <div class="card h-100 border">
                                                    <div class="card-header py-2 bg-light">
                                                        <h5 class="card-title mb-0" style="font-size: 1rem;">{{ $module }}</h5>
                                                    </div>
                                                    <div class="card-body pt-3 pb-0">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}">
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                    {{ $permission->label }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">İptal</a>
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
