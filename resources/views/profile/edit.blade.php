@extends('dashboard.index')

@section('content')
<style>
    .profile-photo, .cover-photo {
        position: relative;
        cursor: pointer;
    }
    .profile-photo:hover .edit-icon, .cover-photo:hover .edit-icon {
        opacity: 1;
    }
    .edit-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 10px;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none; /* Let clicks pass through to the parent */
    }
    .cover-photo .edit-icon {
        border-radius: 5px; /* Different shape for banner */
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil</li>
            </ol>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="profile card card-body px-3 pt-3 pb-0">
                    <div class="profile-head">
                        <div class="photo-content">
                            <div class="cover-photo rounded" style="background-image: url('{{ $user->banner_image ? asset('storage/' . $user->banner_image) : '' }}'); background-size: cover; background-position: center;" data-bs-toggle="modal" data-bs-target="#bannerModal">
                                <div class="edit-icon"><i class="fa fa-camera"></i> Düzenle</div>
                            </div>
                        </div>
                        <div class="profile-info">
                            <div class="profile-photo" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/profile/profile.png') }}" class="img-fluid rounded-circle" alt="">
                                <div class="edit-icon"><i class="fa fa-camera"></i></div>
                            </div>
                            <div class="profile-details">
                                <div class="profile-name px-3 pt-2">
                                    <h4 class="text-primary mb-0">{{ $user->name }}</h4>
                                    <p>Kullanıcı</p>
                                </div>
                                <div class="profile-email px-2 pt-2">
                                    <h4 class="text-muted mb-0">{{ $user->email }}</h4>
                                    <p>E-posta</p>
                                </div>
                                <div class="dropdown ms-auto">
                                    <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Profil işlemleri"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="dropdown-item"><i class="fa fa-user-circle text-primary me-2"></i> Profili Görüntüle</li>
                                        <li class="dropdown-item"><i class="fa fa-users text-primary me-2"></i> Arkadaşlara Ekle</li>
                                        <li class="dropdown-item"><i class="fa fa-plus text-primary me-2"></i> Gruba Ekle</li>
                                        <li class="dropdown-item"><i class="fa fa-ban text-primary me-2"></i> Engelle</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-3">
                            <div class="settings-form">
                                <h4 class="text-primary">Hesap Ayarları</h4>

                                @if (session('status') === 'profile-updated')
                                    <div class="alert alert-success">
                                        {{ __('Profil başarıyla güncellendi.') }}
                                    </div>
                                @endif

                                <form method="post" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('patch')
                                    @if(isset($isEditingOtherUser) && $isEditingOtherUser)
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>Şu anda <strong>{{ $user->name }}</strong> kullanıcısını düzenliyorsunuz.
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Ad Soyad</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">E-posta</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Address Fields (Active) -->
                                    <div class="mb-3">
                                        <label class="form-label">Adres</label>
                                        <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Adresiniz" class="form-control">
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Şehir</label>
                                            <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control">
                                            @error('city')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <label class="form-label">İlçe/Eyalet</label>
                                            <input type="text" name="state" value="{{ old('state', $user->state) }}" class="form-control">
                                            @error('state')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-2">
                                            <label class="form-label">Posta Kodu</label>
                                            <input type="text" name="zip" value="{{ old('zip', $user->zip) }}" class="form-control">
                                            @error('zip')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- End Address Fields -->

                                    @if(Auth::user()->is_admin && isset($isEditingOtherUser) && $isEditingOtherUser)
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin_checkbox" value="1" {{ $user->is_admin ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_admin_checkbox">
                                                    <strong>Admin Yetkisi</strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <button class="btn btn-primary" type="submit">Profili Kaydet</button>
                                    @if(isset($isEditingOtherUser) && $isEditingOtherUser)
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Kullanıcı Listesine Dön</a>
                                    @endif
                                </form>

                                <h4 class="text-primary mt-4">Şifre Güncelle</h4>

                                @if (session('status') === 'password-updated')
                                    <div class="alert alert-success">
                                        {{ __('Şifre başarıyla güncellendi.') }}
                                    </div>
                                @endif

                                <form method="post" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Mevcut Şifre</label>
                                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                                            @error('current_password', 'updatePassword')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Yeni Şifre</label>
                                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                                            @error('password', 'updatePassword')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Şifre Tekrarı</label>
                                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                            @error('password_confirmation', 'updatePassword')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Şifreyi Güncelle</button>
                                </form>

                                @if($showSecurityPanel)
                                <h4 class="text-primary mt-4">Güvenlik</h4>

                                @if(session('security_message'))
                                    <div class="alert alert-success">
                                        {{ session('security_message') }}
                                    </div>
                                @endif

                                @if(session('two_factor.recovery_codes_plain'))
                                    <div class="alert alert-warning">
                                        <strong>Kurtarma kodları (yalnızca bir kez gösterilir):</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach(session('two_factor.recovery_codes_plain', []) as $recoveryCode)
                                                <li><code>{{ $recoveryCode }}</code></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="card-title">İki Adımlı Doğrulama (2FA)</h5>
                                        <p class="text-muted">
                                            Durum:
                                            @if($user->hasTwoFactorEnabled())
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Pasif</span>
                                            @endif
                                        </p>
                                        @if($user->hasTwoFactorEnabled())
                                            <p class="mb-2">Kalan kurtarma kodu: <strong>{{ $user->recoveryCodesCount() }}</strong></p>
                                        @endif

                                        @if(!$user->hasTwoFactorEnabled())
                                            @if($pendingTwoFactorSecret)
                                                <div class="alert alert-info">
                                                    <p class="mb-2"><strong>Kurulum Anahtarı:</strong> <code>{{ $pendingTwoFactorSecret }}</code></p>
                                                    <p class="mb-0"><strong>OTP URI:</strong> <code>{{ $pendingTwoFactorOtpAuthUrl }}</code></p>
                                                </div>
                                                <form method="POST" action="{{ route('security.two-factor.confirm') }}" class="row g-2">
                                                    @csrf
                                                    <div class="col-md-8">
                                                        <input type="text" name="code" class="form-control" placeholder="Uygulamadaki 6 haneli kod" required>
                                                        @error('code')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button class="btn btn-success w-100" type="submit">2FA Aktifleştir</button>
                                                    </div>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('security.two-factor.setup') }}" class="row g-2">
                                                    @csrf
                                                    <div class="col-md-8">
                                                        <input type="password" name="current_password" class="form-control" placeholder="Mevcut şifre" required autocomplete="current-password">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button class="btn btn-primary w-100" type="submit">Kurulumu Başlat</button>
                                                    </div>
                                                </form>
                                            @endif
                                        @else
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <form method="POST" action="{{ route('security.two-factor.recovery-codes') }}">
                                                        @csrf
                                                        <div class="input-group">
                                                            <input type="password" name="current_password" class="form-control" placeholder="Mevcut şifre" required autocomplete="current-password">
                                                            <button class="btn btn-warning" type="submit">Kurtarma Kodlarını Yenile</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-md-6">
                                                    <form method="POST" action="{{ route('security.two-factor.disable') }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="input-group">
                                                            <input type="password" name="current_password" class="form-control" placeholder="Mevcut şifre" required autocomplete="current-password">
                                                            <button class="btn btn-danger" type="submit">2FA Kapat</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Aktif Oturumlar</h5>
                                            <form method="POST" action="{{ route('security.sessions.revoke-others') }}" class="d-flex gap-2">
                                                @csrf
                                                <input type="password" name="current_password" class="form-control form-control-sm" placeholder="Mevcut şifre" required autocomplete="current-password">
                                                <button class="btn btn-outline-danger btn-sm" type="submit">Diğerlerini Sonlandır</button>
                                            </form>
                                        </div>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th>Cihaz</th>
                                                        <th>IP</th>
                                                        <th>Giriş</th>
                                                        <th>Son Aktivite</th>
                                                        <th>İşlem</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($activeSessions as $activeSession)
                                                        <tr>
                                                            <td>
                                                                {{ $activeSession->device_label }}
                                                                @if($activeSession->session_id === $currentSessionId)
                                                                    <span class="badge badge-success ms-2">Bu Cihaz</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $activeSession->ip_address ?? '-' }}</td>
                                                            <td>{{ optional($activeSession->login_at)->format('d.m.Y H:i') ?? '-' }}</td>
                                                            <td>{{ optional($activeSession->last_activity_at)->diffForHumans() ?? '-' }}</td>
                                                            <td>
                                                                <form method="POST" action="{{ route('security.sessions.revoke', $activeSession->id) }}">
                                                                    @csrf
                                                                    <button class="btn btn-outline-danger btn-xs" type="submit">
                                                                        {{ $activeSession->session_id === $currentSessionId ? 'Bu Oturumu Kapat' : 'Sonlandır' }}
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-muted">Aktif oturum bulunamadı.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="card-title">IP / Cihaz Geçmişi</h5>
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th>Olay</th>
                                                        <th>Cihaz</th>
                                                        <th>IP</th>
                                                        <th>Zaman</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($securityEvents as $event)
                                                        <tr>
                                                            <td><code>{{ $event->event }}</code></td>
                                                            <td>{{ $event->device_label }}</td>
                                                            <td>{{ $event->ip_address ?? '-' }}</td>
                                                            <td>{{ $event->created_at->format('d.m.Y H:i:s') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-muted">Güvenlik geçmişi bulunamadı.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Kritik İşlem Logları</h5>
                                        <div class="table-responsive">
                                            <table class="table table-responsive-md">
                                                <thead>
                                                    <tr>
                                                        <th>Event</th>
                                                        <th>IP</th>
                                                        <th>Zaman</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($criticalAuditLogs as $log)
                                                        <tr>
                                                            <td><code>{{ $log->event }}</code></td>
                                                            <td>{{ $log->ip_address ?? '-' }}</td>
                                                            <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-muted">Kritik log kaydı bulunamadı.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Image Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Resmini Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.upload-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($isEditingOtherUser) && $isEditingOtherUser)
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profileImageInput" class="form-label">Yeni Profil Resmi Seç</label>
                        <input class="form-control" type="file" id="profileImageInput" name="profile_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Banner Image Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kapak Resmini Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.upload-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($isEditingOtherUser) && $isEditingOtherUser)
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bannerImageInput" class="form-label">Yeni Kapak Resmi Seç</label>
                        <input class="form-control" type="file" id="bannerImageInput" name="banner_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
