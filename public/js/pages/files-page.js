(function (window, document) {
    'use strict';

    function init(options) {
        var settings = options || {};
        var moveRouteTemplate = settings.moveRouteTemplate || '';

        function showModal(modalId) {
            var modalElement = document.getElementById(modalId);

            if (!modalElement) {
                return null;
            }

            var modal = new bootstrap.Modal(modalElement);
            modal.show();
            return modal;
        }

        function openMoveModal(fileId, fileTitle) {
            var moveForm = document.getElementById('moveFileForm');
            var moveFileTitle = document.getElementById('moveFileTitle');

            if (!moveForm || !moveFileTitle || !moveRouteTemplate) {
                return;
            }

            moveFileTitle.textContent = fileTitle || '';
            moveForm.action = moveRouteTemplate.replace('__ID__', String(fileId));
            showModal('moveFileModal');
        }

        function previewFile(previewUrl, downloadUrl, title, extension) {
            var previewTitle = document.getElementById('previewTitle');
            var previewBody = document.getElementById('previewBody');

            if (!previewTitle || !previewBody) {
                return;
            }

            previewTitle.textContent = title || 'Önizleme';

            var lowerExtension = String(extension || '').toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif'].indexOf(lowerExtension) !== -1) {
                previewBody.innerHTML = '<img src="' + previewUrl + '" class="img-fluid file-preview-image" alt="Dosya önizleme">';
            } else if (lowerExtension === 'pdf') {
                previewBody.innerHTML = '<iframe src="' + previewUrl + '" class="file-preview-frame" title="PDF önizleme"></iframe>';
            } else {
                previewBody.innerHTML =
                    '<div class="p-5">' +
                        '<i class="fa fa-file fa-4x text-muted mb-3"></i>' +
                        '<p>Bu dosya türü için önizleme kullanılamıyor.</p>' +
                        '<a href="' + downloadUrl + '" class="btn btn-primary">İndir</a>' +
                    '</div>';
            }

            showModal('previewModal');
        }

        document.addEventListener('click', function (event) {
            var previewTrigger = event.target.closest('.js-preview-file');
            if (previewTrigger) {
                previewFile(
                    previewTrigger.getAttribute('data-preview-url'),
                    previewTrigger.getAttribute('data-download-url'),
                    previewTrigger.getAttribute('data-file-title'),
                    previewTrigger.getAttribute('data-file-ext')
                );
                return;
            }

            var moveTrigger = event.target.closest('.js-open-move-modal');
            if (moveTrigger) {
                openMoveModal(
                    moveTrigger.getAttribute('data-file-id'),
                    moveTrigger.getAttribute('data-file-title')
                );
            }
        });
    }

    window.SysPanelFilesPage = {
        init: init
    };
})(window, document);
