@extends('dashboard.index')

@section('title', 'Mesajlar')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Mesajlar</a></li>
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
                                        <div class="dlab-scroll" style="height: 600px; overflow-y: auto;">
                                            <ul class="list-group" id="user-list">
                                                @foreach($users as $user)
                                                    <li class="list-group-item list-group-item-action user-item" data-user-id="{{ $user->id }}" style="cursor: pointer;">
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
                                        <div id="direct-chat-container" style="display:none;">
                                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0" id="direct-chat-user-name">Sohbet</h4>
                                            </div>
                                            
                                            <div class="card-body" id="direct-messages-container" style="height: 500px; overflow-y: auto; background: #f8f9fa;">
                                                <!-- Messages will be loaded here -->
                                            </div>

                                            <div class="card-footer">
                                                <form id="send-direct-message-form">
                                                    <div class="input-group">
                                                        <textarea class="form-control" id="direct-message-input" rows="2" placeholder="Mesajınızı yazın..." required></textarea>
                                                        <button class="btn btn-primary" type="submit">
                                                            <i class="fa fa-paper-plane"></i> Gönder
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div id="no-direct-chat" class="text-center" style="padding:100px 0;">
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
                                
                                <div class="card-body" id="general-messages-container" style="height: 500px; overflow-y: auto; background: #f8f9fa;">
                                    <!-- General messages will be loaded here -->
                                </div>

                                <div class="card-footer">
                                    <form id="send-general-message-form">
                                        <div class="input-group">
                                            <textarea class="form-control" id="general-message-input" rows="2" placeholder="Genel mesajınızı yazın..." required></textarea>
                                            <button class="btn btn-success" type="submit">
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
<script>
// Direct chat variables
let currentReceiverId = null;
let directPollInterval = null;
let generalPollInterval = null;
const currentUserId = {{ Auth::id() }};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // General tab listeners
    const generalTab = document.getElementById('general-tab');
    const directTab = document.getElementById('direct-tab');
    
    if (generalTab) {
        generalTab.addEventListener('click', function() {
            setTimeout(() => {
                loadGeneralMessages();
                if (generalPollInterval) clearInterval(generalPollInterval);
                generalPollInterval = setInterval(() => loadGeneralMessages(true), 3000);
                
                // Stop direct polling
                if (directPollInterval) clearInterval(directPollInterval);
            }, 100);
        });
    }
    
    if (directTab) {
        directTab.addEventListener('click', function() {
            // Stop general polling when switching to direct
            if (generalPollInterval) clearInterval(generalPollInterval);
        });
    }
});

//========= DIRECT CHAT FUNCTIONS =========//

// Select user for direct chat
document.addEventListener('click', function(e) {
    const userItem = e.target.closest('.user-item');
    if (!userItem) return;
    
    const userId = userItem.dataset.userId;
    const userName = userItem.querySelector('strong').textContent;
    
    currentReceiverId = userId;
    
    // Update UI
    document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
    userItem.classList.add('active');
    document.getElementById('direct-chat-user-name').textContent = userName;
    document.getElementById('no-direct-chat').style.display = 'none';
    document.getElementById('direct-chat-container').style.display =  'block';
    
    // Load messages
    loadDirectMessages(userId);
    
    // Start polling
    if (directPollInterval) clearInterval(directPollInterval);
    directPollInterval = setInterval(() => loadDirectMessages(userId, true), 3000);
});

// Load direct messages
function loadDirectMessages(userId, silent = false) {
    fetch(`/chat/messages/${userId}`)
        .then(response => response.json())
        .then(messages => {
            displayDirectMessages(messages, silent);
            if (!silent) {
                scrollToBottom('direct-messages-container');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Display direct messages
function displayDirectMessages(messages, silent) {
    const container = document.getElementById('direct-messages-container');
    let hasNewIncoming = false;
    
    if (!silent) {
        container.innerHTML = '';
    }
    
    messages.forEach(message => {
        if (container.querySelector(`[data-message-id="${message.id}"]`)) {
            return; // Already displayed
        }
        
        const isSender = message.sender_id === currentUserId;
        
        // Check if this is a new incoming message
        if (silent && !isSender) {
            hasNewIncoming = true;
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex justify-content-${isSender ? 'end' : 'start'} mb-3`;
        messageDiv.dataset.messageId = message.id;
        
        messageDiv.innerHTML = `
            <div class="message-bubble ${isSender ? 'bg-primary text-white' : 'bg-white'}" style="max-width: 70%; padding: 10px 15px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <p class="mb-0">${escapeHtml(message.message)}</p>
                <small class="${isSender ? 'text-white-50' : 'text-muted'}" style="font-size: 0.75rem;">
                    ${formatTime(message.created_at)}
                </small>
            </div>
        `;
        
        container.appendChild(messageDiv);
    });
    
    // Show notification for new messages
    if (hasNewIncoming && typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Yeni mesaj geldi!',
            showConfirmButton: false,
            timer: 3000
        });
    }
}

// Send direct message
document.getElementById('send-direct-message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('direct-message-input');
    const message = messageInput.value.trim();
    
    if (!message || !currentReceiverId) return;
    
    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            receiver_id: currentReceiverId,
            message: message,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(() => {
        messageInput.value = '';
        loadDirectMessages(currentReceiverId, true);
        scrollToBottom('direct-messages-container');
    })
    .catch(error => console.error('Error:', error));
});

//========= GENERAL CHAT FUNCTIONS =========//

// Load general messages
function loadGeneralMessages(silent = false) {
    fetch('/chat/general')
        .then(response => response.json())
        .then(messages => {
            displayGeneralMessages(messages, silent);
            if (!silent) {
                scrollToBottom('general-messages-container');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Display general messages
function displayGeneralMessages(messages, silent) {
    const container = document.getElementById('general-messages-container');
    let hasNewIncoming = false;
    
    if (!silent) {
        container.innerHTML = '';
    }
    
    messages.forEach(message => {
        if (container.querySelector(`[data-message-id="${message.id}"]`)) {
            return; // Already displayed
        }
        
        const isSender = message.sender_id === currentUserId;
        
        // Check if this is a new incoming message
        if (silent && !isSender) {
            hasNewIncoming = true;
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex justify-content-${isSender ? 'end' : 'start'} mb-3`;
        messageDiv.dataset.messageId = message.id;
        
        const senderName = message.sender ? message.sender.name : 'Bilinmeyen';
        
        messageDiv.innerHTML = `
            <div class="message-bubble ${isSender ? 'bg-success text-white' : 'bg-white'}" style="max-width: 70%; padding: 10px 15px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                ${!isSender ? `<strong>${escapeHtml(senderName)}</strong><br>` : ''}
                <p class="mb-0">${escapeHtml(message.message)}</p>
                <small class="${isSender ? 'text-white-50' : 'text-muted'}"  style="font-size: 0.75rem;">
                    ${formatTime(message.created_at)}
                </small>
            </div>
        `;
        
        container.appendChild(messageDiv);
    });
    
    // Show notification for new general messages
    if (hasNewIncoming && typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Genel sohbete yeni mesaj geldi!',
            showConfirmButton: false,
            timer: 3000
        });
    }
}

// Send general message
document.getElementById('send-general-message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('general-message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: message,
            is_general: true,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(() => {
        messageInput.value = '';
        loadGeneralMessages(true);
        scrollToBottom('general-messages-container');
    })
    .catch(error => console.error('Error:', error));
});

//========= UTILITY FUNCTIONS =========//

// Scroll to bottom
function scrollToBottom(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Format time
function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Şimdi';
    if (diffMins < 60) return `${diffMins} dakika önce`;
    
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} saat önce`;
    
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (directPollInterval) clearInterval(directPollInterval);
    if (generalPollInterval) clearInterval(generalPollInterval);
});
</script>
@endpush
@endsection
