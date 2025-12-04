@extends('dashboard.index')

@section('title', 'Platform Ayarları')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Admin</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Platform Ayarları</a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Genel Ayarlar</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Site Başlığı</label>
                                <input type="text" class="form-control" name="site_title" value="{{ \App\Models\Setting::get('site_title', 'OIDB Panel') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Site Logosu</label>
                                <input type="file" class="form-control" name="site_logo">
                                @if(\App\Models\Setting::get('site_logo'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . \App\Models\Setting::get('site_logo')) }}" alt="Logo" height="50">
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Favicon</label>
                                <input type="file" class="form-control" name="site_favicon">
                                @if(\App\Models\Setting::get('site_favicon'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . \App\Models\Setting::get('site_favicon')) }}" alt="Favicon" height="32">
                                    </div>
                                @endif
                            </div>

                            <hr>
                            <h4 class="card-title mb-3">Zoom API Ayarları</h4>

                            <div class="mb-3">
                                <label class="form-label">Zoom Account ID</label>
                                <input type="text" class="form-control" name="zoom_account_id" value="{{ \App\Models\Setting::get('zoom_account_id', env('ZOOM_ACCOUNT_ID')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Zoom Client ID</label>
                                <input type="text" class="form-control" name="zoom_client_id" value="{{ \App\Models\Setting::get('zoom_client_id', env('ZOOM_CLIENT_ID')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Zoom Client Secret</label>
                                <input type="text" class="form-control" name="zoom_client_secret" value="{{ \App\Models\Setting::get('zoom_client_secret', env('ZOOM_CLIENT_SECRET')) }}">
                            </div>

                            <hr>
                            <h4 class="card-title mb-3">Microsoft Teams Ayarları</h4>

                            <div class="mb-3">
                                <label class="form-label">Tenant ID</label>
                                <input type="text" class="form-control" name="teams_tenant_id" value="{{ \App\Models\Setting::get('teams_tenant_id', env('TEAMS_TENANT_ID')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client ID</label>
                                <input type="text" class="form-control" name="teams_client_id" value="{{ \App\Models\Setting::get('teams_client_id', env('TEAMS_CLIENT_ID')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Client Secret</label>
                                <input type="text" class="form-control" name="teams_client_secret" value="{{ \App\Models\Setting::get('teams_client_secret', env('TEAMS_CLIENT_SECRET')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">User ID (Toplantı Sahibi)</label>
                                <input type="text" class="form-control" name="teams_user_id" value="{{ \App\Models\Setting::get('teams_user_id', env('TEAMS_USER_ID')) }}">
                                <small class="text-muted">Teams toplantılarını oluşturacak kullanıcının ID'si.</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
