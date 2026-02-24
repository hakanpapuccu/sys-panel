(function (window, document) {
    'use strict';

    var ACTIVE_POLL_INTERVAL_MS = 3000;
    var HIDDEN_POLL_INTERVAL_MS = 15000;

    function getPollDelay() {
        return document.hidden ? HIDDEN_POLL_INTERVAL_MS : ACTIVE_POLL_INTERVAL_MS;
    }

    function getCsrfToken() {
        var tokenElement = document.querySelector('meta[name="csrf-token"]');
        return tokenElement ? tokenElement.getAttribute('content') : '';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        var date = new Date(timestamp);
        var now = new Date();
        var diffMs = now - date;
        var diffMins = Math.floor(diffMs / 60000);

        if (diffMins < 1) {
            return 'Şimdi';
        }

        if (diffMins < 60) {
            return diffMins + ' dakika önce';
        }

        var diffHours = Math.floor(diffMins / 60);

        if (diffHours < 24) {
            return diffHours + ' saat önce';
        }

        return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function isNearBottom(container, threshold) {
        if (!container) {
            return false;
        }

        var offset = typeof threshold === 'number' ? threshold : 40;
        return container.scrollHeight - container.scrollTop - container.clientHeight <= offset;
    }

    function scrollToBottom(container) {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function showToast(title, icon) {
        if (typeof window.Swal === 'undefined') {
            return;
        }

        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon || 'info',
            title: title,
            showConfirmButton: false,
            timer: 3000
        });
    }

    function init(options) {
        var settings = options || {};
        var routes = settings.routes || {};
        var currentUserId = Number(settings.currentUserId || 0);

        var directContainer = document.getElementById('direct-messages-container');
        var generalContainer = document.getElementById('general-messages-container');
        var directForm = document.getElementById('send-direct-message-form');
        var generalForm = document.getElementById('send-general-message-form');
        var noDirectChat = document.getElementById('no-direct-chat');
        var directChatContainer = document.getElementById('direct-chat-container');
        var directChatUserName = document.getElementById('direct-chat-user-name');
        var generalTab = document.getElementById('general-tab');
        var directTab = document.getElementById('direct-tab');

        if (!directContainer || !generalContainer || !directForm || !generalForm || !noDirectChat || !directChatContainer) {
            return;
        }

        var directPollTimer = null;
        var generalPollTimer = null;
        var currentReceiverId = null;
        var isGeneralTabActive = false;

        function clearDirectPollTimer() {
            if (directPollTimer) {
                window.clearTimeout(directPollTimer);
                directPollTimer = null;
            }
        }

        function clearGeneralPollTimer() {
            if (generalPollTimer) {
                window.clearTimeout(generalPollTimer);
                generalPollTimer = null;
            }
        }

        function scheduleDirectPoll() {
            clearDirectPollTimer();

            if (!currentReceiverId || isGeneralTabActive) {
                return;
            }

            directPollTimer = window.setTimeout(function () {
                loadDirectMessages(currentReceiverId, true).finally(scheduleDirectPoll);
            }, getPollDelay());
        }

        function scheduleGeneralPoll() {
            clearGeneralPollTimer();

            if (!isGeneralTabActive) {
                return;
            }

            generalPollTimer = window.setTimeout(function () {
                loadGeneralMessages(true).finally(scheduleGeneralPoll);
            }, getPollDelay());
        }

        function getJson(url) {
            return window.fetch(url).then(function (response) {
                if (!response.ok) {
                    throw new Error('İstek başarısız');
                }

                return response.json();
            });
        }

        function postJson(url, payload) {
            return window.fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Gönderim başarısız');
                }

                return response.json().catch(function () {
                    return {};
                });
            });
        }

        function displayDirectMessages(messages, silent) {
            var hasNewIncoming = false;
            var shouldStickToBottom = !silent || isNearBottom(directContainer);

            if (!silent) {
                directContainer.innerHTML = '';
            }

            messages.forEach(function (message) {
                if (silent && directContainer.querySelector('[data-message-id="' + message.id + '"]')) {
                    return;
                }

                var isSender = Number(message.sender_id) === currentUserId;

                if (silent && !isSender) {
                    hasNewIncoming = true;
                }

                var messageDiv = document.createElement('div');
                messageDiv.className = 'd-flex justify-content-' + (isSender ? 'end' : 'start') + ' mb-3';
                messageDiv.setAttribute('data-message-id', message.id);

                messageDiv.innerHTML =
                    '<div class="app-message-bubble ' + (isSender ? 'bg-primary text-white' : 'bg-white') + '">' +
                        '<p class="mb-0 app-message-text">' + escapeHtml(message.message) + '</p>' +
                        '<small class="' + (isSender ? 'text-white-50' : 'text-muted') + ' app-message-time">' + formatTime(message.created_at) + '</small>' +
                    '</div>';

                directContainer.appendChild(messageDiv);
            });

            if (hasNewIncoming) {
                showToast('Yeni mesaj geldi!', 'info');
            }

            if (shouldStickToBottom) {
                scrollToBottom(directContainer);
            }
        }

        function displayGeneralMessages(messages, silent) {
            var hasNewIncoming = false;
            var shouldStickToBottom = !silent || isNearBottom(generalContainer);

            if (!silent) {
                generalContainer.innerHTML = '';
            }

            messages.forEach(function (message) {
                if (silent && generalContainer.querySelector('[data-message-id="' + message.id + '"]')) {
                    return;
                }

                var isSender = Number(message.sender_id) === currentUserId;

                if (silent && !isSender) {
                    hasNewIncoming = true;
                }

                var senderName = message.sender && message.sender.name ? message.sender.name : 'Bilinmeyen';
                var senderHtml = isSender ? '' : '<strong>' + escapeHtml(senderName) + '</strong><br>';

                var messageDiv = document.createElement('div');
                messageDiv.className = 'd-flex justify-content-' + (isSender ? 'end' : 'start') + ' mb-3';
                messageDiv.setAttribute('data-message-id', message.id);

                messageDiv.innerHTML =
                    '<div class="app-message-bubble ' + (isSender ? 'bg-success text-white' : 'bg-white') + '">' +
                        senderHtml +
                        '<p class="mb-0 app-message-text">' + escapeHtml(message.message) + '</p>' +
                        '<small class="' + (isSender ? 'text-white-50' : 'text-muted') + ' app-message-time">' + formatTime(message.created_at) + '</small>' +
                    '</div>';

                generalContainer.appendChild(messageDiv);
            });

            if (hasNewIncoming) {
                showToast('Genel sohbete yeni mesaj geldi!', 'success');
            }

            if (shouldStickToBottom) {
                scrollToBottom(generalContainer);
            }
        }

        function loadDirectMessages(userId, silent) {
            if (!routes.messages) {
                return Promise.resolve();
            }

            var requestUrl = routes.messages.replace('__USER__', String(userId));
            return getJson(requestUrl)
                .then(function (messages) {
                    displayDirectMessages(messages, !!silent);
                })
                .catch(function (error) {
                    console.error('Direct messages error:', error);
                });
        }

        function loadGeneralMessages(silent) {
            if (!routes.general) {
                return Promise.resolve();
            }

            return getJson(routes.general)
                .then(function (messages) {
                    displayGeneralMessages(messages, !!silent);
                })
                .catch(function (error) {
                    console.error('General messages error:', error);
                });
        }

        document.addEventListener('click', function (event) {
            var userItem = event.target.closest('.user-item');

            if (!userItem) {
                return;
            }

            currentReceiverId = userItem.getAttribute('data-user-id');

            document.querySelectorAll('.user-item').forEach(function (item) {
                item.classList.remove('active');
            });

            userItem.classList.add('active');

            if (directChatUserName) {
                var titleEl = userItem.querySelector('strong');
                directChatUserName.textContent = titleEl ? titleEl.textContent : 'Sohbet';
            }

            noDirectChat.classList.add('chat-panel-hidden');
            directChatContainer.classList.remove('chat-panel-hidden');

            loadDirectMessages(currentReceiverId, false).finally(scheduleDirectPoll);
        });

        directForm.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!currentReceiverId || !routes.send) {
                return;
            }

            var messageInput = document.getElementById('direct-message-input');
            var message = messageInput ? messageInput.value.trim() : '';

            if (!message) {
                return;
            }

            postJson(routes.send, {
                receiver_id: currentReceiverId,
                message: message
            }).then(function () {
                if (messageInput) {
                    messageInput.value = '';
                }

                return loadDirectMessages(currentReceiverId, true);
            }).then(function () {
                scrollToBottom(directContainer);
            }).catch(function (error) {
                console.error('Send direct message error:', error);
            });
        });

        generalForm.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!routes.send) {
                return;
            }

            var messageInput = document.getElementById('general-message-input');
            var message = messageInput ? messageInput.value.trim() : '';

            if (!message) {
                return;
            }

            postJson(routes.send, {
                message: message,
                is_general: true
            }).then(function () {
                if (messageInput) {
                    messageInput.value = '';
                }

                return loadGeneralMessages(true);
            }).then(function () {
                scrollToBottom(generalContainer);
            }).catch(function (error) {
                console.error('Send general message error:', error);
            });
        });

        function activateGeneralTab() {
            isGeneralTabActive = true;
            clearDirectPollTimer();
            loadGeneralMessages(false).finally(scheduleGeneralPoll);
        }

        function activateDirectTab() {
            isGeneralTabActive = false;
            clearGeneralPollTimer();
            scheduleDirectPoll();
        }

        if (generalTab) {
            generalTab.addEventListener('shown.bs.tab', activateGeneralTab);
            generalTab.addEventListener('click', function () {
                window.setTimeout(activateGeneralTab, 100);
            });
        }

        if (directTab) {
            directTab.addEventListener('shown.bs.tab', activateDirectTab);
            directTab.addEventListener('click', activateDirectTab);
        }

        document.addEventListener('visibilitychange', function () {
            if (isGeneralTabActive) {
                scheduleGeneralPoll();

                if (!document.hidden) {
                    loadGeneralMessages(true);
                }

                return;
            }

            scheduleDirectPoll();

            if (!document.hidden && currentReceiverId) {
                loadDirectMessages(currentReceiverId, true);
            }
        });

        window.addEventListener('beforeunload', function () {
            clearDirectPollTimer();
            clearGeneralPollTimer();
        });
    }

    window.SysPanelChatPage = {
        init: init
    };
})(window, document);
