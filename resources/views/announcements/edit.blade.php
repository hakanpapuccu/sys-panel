@extends('dashboard.index')

@section('title', 'Duyuru Düzenle')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item"><a href="{{ route('announcements.index') }}">Duyurular</a></li>
                <li class="breadcrumb-item active" aria-current="page">Düzenle</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-tab">
                            <div class="custom-tab-1">
                                <div class="tab-content">
                                    <div id="my-posts" class="tab-pane fade active show">
                                        <div class="my-post-content pt-3">

                                            <!-- Post Input -->
                                            <div class="post-input mb-4">
                                                <h4 class="text-primary mb-3">Duyuruyu Düzenle</h4>
                                                <form action="{{ route('announcements.update', $announcement->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="content" id="announcement-content" cols="30" rows="5" class="form-control">{{ $announcement->content }}</textarea>
                                                    <div class="mt-3 text-end">
                                                        <a href="{{ route('announcements.index') }}" class="btn btn-secondary me-2">İptal</a>
                                                        <button type="submit" class="btn btn-primary">Güncelle</button>
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
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script>
ClassicEditor
    .create(document.querySelector('#announcement-content'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
            ]
        }
    })
    .then(function (editor) {
        window.editor = editor;
    })
    .catch(function (error) {
        console.error(error);
    });
</script>
@endpush
@endsection
