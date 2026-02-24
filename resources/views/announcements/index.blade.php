@extends('dashboard.index')

@section('title', 'Duyurular')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Duyurular</li>
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
                                            @if(auth()->user()->hasPermission('create_announcements'))
                                                <div class="post-input mb-4">
                                                    <h4 class="text-primary mb-3">Yeni Duyuru Paylaş</h4>
                                                    <form action="{{ route('announcements.store') }}" method="POST">
                                                        @csrf
                                                        <textarea name="content" id="announcement-content" cols="30" rows="5" class="form-control"></textarea>
                                                        <div class="mt-3 text-end">
                                                            <button type="submit" class="btn btn-primary">Paylaş</button>
                                                        </div>
                                                    </form>
                                                </div>

                                                <hr>
                                            @endif

                                            <!-- Announcements Feed -->
                                            <h4 class="text-primary mb-4 mt-4">Son Duyurular</h4>
                                            @foreach($announcements as $announcement)
                                            <div class="card shadow-sm border mb-3">
	                                                <div class="card-body p-3">
	                                                    <div class="profile-uoloaded-post">
	                                                        <div class="media mb-3 align-items-center">
	                                                            <img src="{{ $announcement->user->profile_image ? asset('storage/' . $announcement->user->profile_image) : asset('images/profile/profile.png') }}" alt="" class="img-fluid rounded-circle me-3 avatar-40">
	                                                            <div class="media-body">
	                                                                <h4 class="text-black mb-0 fs-16">{{ $announcement->user->name }}</h4>
	                                                                <span class="text-muted fs-12">{{ $announcement->created_at->diffForHumans() }}</span>
	                                                            </div>
	                                                            @if(auth()->id() === $announcement->user_id || auth()->user()->is_admin)
	                                                            <div class="dropdown ms-auto">
	                                                                <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Duyuru işlemleri"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></button>
	                                                                <ul class="dropdown-menu dropdown-menu-end">
	                                                                    @if(auth()->user()->hasPermission('edit_announcements'))
	                                                                        <li><a href="{{ route('announcements.edit', $announcement->id) }}" class="dropdown-item"><i class="fa fa-pencil text-primary me-2"></i> Düzenle</a></li>
                                                                    @endif
                                                                    @if(auth()->user()->hasPermission('delete_announcements'))
                                                                        <li>
                                                                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="dropdown-item"><i class="fa fa-trash text-danger me-2"></i> Sil</button>
                                                                            </form>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="post-content mb-3">
                                                            {!! nl2br(e($announcement->content)) !!}
                                                        </div>

	                                                        <div class="d-flex border-top pt-2 align-items-center">
	                                                            <button class="btn btn-primary btn-xs me-2" type="button" aria-label="Duyuruyu beğen"><span class="me-2"><i class="fa fa-heart"></i></span>Beğen</button>
	                                                            <button class="btn btn-secondary btn-xs" type="button" data-bs-toggle="collapse" data-bs-target="#comment-{{ $announcement->id }}" aria-label="Yorum alanını aç"><span class="me-2"><i class="fa fa-reply"></i></span>Yanıtla</button>
	                                                        </div>

                                                        <!-- Comment Input -->
                                                        <div class="mt-3 collapse" id="comment-{{ $announcement->id }}">
                                                            <form action="{{ route('comments.store') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="announcement_id" value="{{ $announcement->id }}">
	                                                                <div class="input-group">
	                                                                    <input type="text" name="content" class="form-control" placeholder="Yorum yaz..." required>
	                                                                    <button type="submit" class="btn btn-primary" aria-label="Yorumu gönder"><i class="fa fa-paper-plane"></i></button>
	                                                                </div>
	                                                            </form>
	                                                        </div>

                                                        <!-- Comments Display -->
                                                        @if($announcement->comments->count() > 0)
	                                                        <div class="mt-3">
	                                                            @foreach($announcement->comments as $comment)
	                                                            <div class="d-flex mb-2">
	                                                                <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : asset('images/profile/profile.png') }}" alt="" class="img-fluid rounded-circle me-2 avatar-30">
	                                                                <div class="flex-grow-1">
	                                                                    <div class="bg-light p-2 rounded">
	                                                                        <strong class="fs-12">{{ $comment->user->name }}</strong>
                                                                        <p class="mb-0 fs-12">{{ $comment->content }}</p>
                                                                    </div>
                                                                    <small class="text-muted fs-10">{{ $comment->created_at->diffForHumans() }}</small>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

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

@if(auth()->user()->hasPermission('create_announcements'))
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
@endif
@endsection
