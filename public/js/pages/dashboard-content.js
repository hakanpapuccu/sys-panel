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
        return date.toLocaleTimeString('tr-TR', {
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

    function init(options) {
        var settings = options || {};
        var routes = settings.routes || {};
        var currentUserId = Number(settings.currentUserId || 0);

        var calendarContainer = document.getElementById('dashboard-calendar');
        var messagesContainer = document.getElementById('dashboard-general-messages');
        var chatForm = document.getElementById('dashboard-chat-form');
        var chatInput = document.getElementById('dashboard-chat-input');

        if (calendarContainer && window.FullCalendar && routes.calendarEvents) {
            var calendar = new window.FullCalendar.Calendar(calendarContainer, {
                initialView: 'dayGridMonth',
                locale: 'tr',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: ''
                },
                height: 500,
                events: routes.calendarEvents
            });

            calendar.render();
        }

        if (!messagesContainer || !chatForm || !chatInput) {
            return;
        }

        var pollTimer = null;

        function clearPollTimer() {
            if (pollTimer) {
                window.clearTimeout(pollTimer);
                pollTimer = null;
            }
        }

        function schedulePolling() {
            clearPollTimer();

            pollTimer = window.setTimeout(function () {
                loadMessages(true).finally(schedulePolling);
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

        function renderMessages(messages, silent) {
            var shouldStickToBottom = !silent || isNearBottom(messagesContainer);

            messagesContainer.innerHTML = '';

            messages.forEach(function (message) {
                var isSender = Number(message.sender_id) === currentUserId;
                var senderName = message.sender && message.sender.name ? message.sender.name : 'Bilinmeyen';

                var messageWrapper = document.createElement('div');
                messageWrapper.className = 'd-flex justify-content-' + (isSender ? 'end' : 'start') + ' mb-2';

                messageWrapper.innerHTML =
                    '<div class="dashboard-message-bubble ' + (isSender ? 'bg-primary text-white' : 'bg-white text-dark') + '">' +
                        (isSender ? '' : '<small class="fw-bold d-block mb-1 app-message-sender">' + escapeHtml(senderName) + '</small>') +
                        '<p class="mb-0 app-message-text">' + escapeHtml(message.message) + '</p>' +
                        '<small class="' + (isSender ? 'text-white-50' : 'text-muted') + ' d-block text-end mt-1 dashboard-message-time">' + formatTime(message.created_at) + '</small>' +
                    '</div>';

                messagesContainer.appendChild(messageWrapper);
            });

            if (shouldStickToBottom) {
                scrollToBottom(messagesContainer);
            }
        }

        function loadMessages(silent) {
            if (!routes.generalMessages) {
                return Promise.resolve();
            }

            return getJson(routes.generalMessages)
                .then(function (messages) {
                    renderMessages(messages, !!silent);
                })
                .catch(function (error) {
                    console.error('Dashboard messages error:', error);
                });
        }

        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var message = chatInput.value.trim();

            if (!message || !routes.sendMessage) {
                return;
            }

            postJson(routes.sendMessage, {
                message: message,
                is_general: true
            }).then(function () {
                chatInput.value = '';
                return loadMessages(true);
            }).then(function () {
                scrollToBottom(messagesContainer);
            }).catch(function (error) {
                console.error('Dashboard send message error:', error);
            });
        });

        document.addEventListener('visibilitychange', function () {
            schedulePolling();

            if (!document.hidden) {
                loadMessages(true);
            }
        });

        window.addEventListener('beforeunload', function () {
            clearPollTimer();
        });

        loadMessages(false).finally(schedulePolling);
    }

    window.SysPanelDashboardContent = {
        init: init
    };
})(window, document);
