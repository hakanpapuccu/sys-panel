@extends('dashboard.index')

@section('title', 'Mesajlar')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mesajlar</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-3" id="chatTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="direct-tab" data-bs-toggle="tab" data-bs-target="#direct-chat" type="button">
                                    <i class="fas fa-user me-2"></i>Birebir Mesajlar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-chat" type="button">
                                    <i class="fas fa-users me-2"></i>Genel Sohbet
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="chatTabsContent">
                            <!-- Direct Messages Tab -->
                            <div class="tab-pane fade show active" id="direct-chat" role="tabpanel">
                                <div class="row">
                                    <!-- User List -->
                                    <div class="col-lg-3 col-xl-2">
                                        <div class="card-header border-bottom mb-3">
                                            <h4 class="card-title">Kullanıcılar</h4>
                                        </div>
                                        <div class="dlab-scroll chat-users-panel">
                                            <ul class="list-group" id="user-list">
                                                @foreach($users as $user)
                                                    <li class="list-group-item list-group-item-action user-item chat-user-item" data-user-id="{{ $user->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/user.jpg') }}"
                                                                 class="rounded-circle me-2" width="40" height="40" alt="">
                                                            <div>
                                                                <strong>{{ $user->name }}</strong>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Direct Chat Area -->
                                    <div class="col-lg-9 col-xl-10">
                                        <div id="direct-chat-container" class="chat-panel-hidden">
                                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0" id="direct-chat-user-name">Sohbet</h4>
                                            </div>

                                            <div class="card-body chat-messages-panel" id="direct-messages-container">
                                                <!-- Messages will be loaded here -->
                                            </div>

                                            <div class="card-footer">
                                                <form id="send-direct-message-form">
                                                    <div class="input-group">
                                                        <textarea class="form-control" id="direct-message-input" rows="2" placeholder="Mesajınızı yazın..." required></textarea>
                                                        <button class="btn btn-primary" type="submit" aria-label="Birebir mesaj gönder">
                                                            <i class="fa fa-paper-plane"></i> Gönder
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div id="no-direct-chat" class="text-center chat-empty-state">
                                            <i class="fas fa-comments fa-5x text-muted mb-3"></i>
                                            <h3 class="text-muted">Sohbet başlatmak için bir kullanıcı seçin</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- General Chat Tab -->
                            <div class="tab-pane fade" id="general-chat" role="tabpanel">
                                <div class="card-header border-bottom d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title mb-0">Genel Sohbet</h4>
                                    <small class="text-muted">Tüm kullanıcılar bu mesajları görebilir</small>
                                </div>

                                <div class="card-body chat-messages-panel" id="general-messages-container">
                                    <!-- General messages will be loaded here -->
                                </div>

                                <div class="card-footer">
                                    <form id="send-general-message-form">
                                        <div class="input-group">
                                            <textarea class="form-control" id="general-message-input" rows="2" placeholder="Genel mesajınızı yazın..." required></textarea>
                                            <button class="btn btn-success" type="submit" aria-label="Genel mesaj gönder">
                                                <i class="fa fa-paper-plane"></i> Gönder
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/pages/chat-page.js') }}"></script>
<script>
window.SysPanelChatPage.init({
    currentUserId: {{ Auth::id() }},
    routes: {
        messages: '{{ route('chat.messages', '__USER__') }}',
        general: '{{ route('chat.general') }}',
        send: '{{ route('chat.send') }}'
    }
});
</script>
@endpush
@endsection
