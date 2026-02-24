@extends('dashboard.index')

@section('title', 'Yeni Anket')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Anketler</a></li>
                <li class="breadcrumb-item active" aria-current="page">Yeni Anket</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Anket Oluştur</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.polls.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Anket Başlığı</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Açıklama</label>
                                    <textarea class="form-control" name="description" rows="1"></textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="isActive">
                                        <label class="form-check-label" for="isActive">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h4>Sorular</h4>
                            <div id="questions-container">
                                <!-- Questions will be added here -->
                            </div>

                            <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">+ Soru Ekle</button>
                            <br>
                            <button type="submit" class="btn btn-primary">Anketi Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let questionCount = 0;

    function addQuestion() {
        const container = document.getElementById('questions-container');
        const index = questionCount++;

        const html = `
            <div class="card border mb-3 question-item" id="question-${index}">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5>Soru ${index + 1}</h5>
                        <button type="button" class="btn btn-danger btn-xs" onclick="removeQuestion(${index})">Sil</button>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <input type="text" class="form-control" name="questions[${index}][text]" placeholder="Soru metni" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select class="form-control" name="questions[${index}][type]" onchange="toggleOptions(${index}, this.value)">
                                <option value="text">Kısa Metin</option>
                                <option value="textarea">Uzun Metin</option>
                                <option value="radio">Tek Seçmeli (Radyo)</option>
                                <option value="checkbox">Çok Seçmeli (Onay Kutusu)</option>
                            </select>
                        </div>
                    </div>
                    <div id="options-container-${index}" style="display: none;">
                        <label>Seçenekler</label>
                        <div id="options-list-${index}">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="questions[${index}][options][]" placeholder="Seçenek 1">
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="questions[${index}][options][]" placeholder="Seçenek 2">
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-xs" onclick="addOption(${index})">+ Seçenek Ekle</button>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
    }

    function removeQuestion(index) {
        document.getElementById(`question-${index}`).remove();
    }

    function toggleOptions(index, type) {
        const container = document.getElementById(`options-container-${index}`);
        if (type === 'radio' || type === 'checkbox') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function addOption(qIndex) {
        const list = document.getElementById(`options-list-${qIndex}`);
        const html = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[${qIndex}][options][]" placeholder="Yeni Seçenek">
                <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">X</button>
            </div>
        `;
        list.insertAdjacentHTML('beforeend', html);
    }

    // Add first question by default
    document.addEventListener('DOMContentLoaded', function() {
        addQuestion();
    });
</script>
@endsection
