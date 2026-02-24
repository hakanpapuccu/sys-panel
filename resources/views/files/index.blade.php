@extends('dashboard.index')

@section('title', 'Dosya Paylaşımı')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('files.index') }}">Ana Dizin</a></li>
                @foreach($breadcrumbs as $crumb)
                    <li class="breadcrumb-item active"><a href="{{ route('files.index', ['folder_id' => $crumb->id]) }}">{{ $crumb->name }}</a></li>
                @endforeach
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Dosyalar</h4>
                        @if(Auth::user()->hasPermission('upload_files'))
                        <div>
                            <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                                <i class="fa fa-folder-plus me-1"></i> Yeni Klasör
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                                <i class="fa fa-upload me-1"></i> Dosya Yükle
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                            </div>
                        @endif

                        <!-- Unified Table -->
                        <div class="table-responsive">
                            <table class="table table-responsive-md table-hover">
                                <thead>
                                    <tr>
                                        <th><strong>AD</strong></th>
                                        <th><strong>TÜR</strong></th>
                                        <th><strong>SAHİBİ</strong></th>
                                        <th><strong>TARİH</strong></th>
                                        <th><strong>İŞLEMLER</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Folders -->
                                    @foreach($folders as $folder)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-folder fa-lg me-3 text-warning"></i>
                                                <a href="{{ route('files.index', ['folder_id' => $folder->id]) }}" class="text-dark fw-bold">
                                                    {{ $folder->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>Klasör</td>
                                        <td>{{ $folder->user->name }}</td>
                                        <td>{{ $folder->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('files.index', ['folder_id' => $folder->id]) }}" class="btn btn-primary shadow btn-xs sharp" title="Aç">
                                                <i class="fa fa-folder-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach

                                    <!-- Files -->
                                    @foreach ($files as $file)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $ext = pathinfo($file->file_name, PATHINFO_EXTENSION);
                                                        $icon = 'fa-file';
                                                        if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image';
                                                        elseif(in_array(strtolower($ext), ['pdf'])) $icon = 'fa-file-pdf';
                                                        elseif(in_array(strtolower($ext), ['doc', 'docx'])) $icon = 'fa-file-word';
                                                        elseif(in_array(strtolower($ext), ['xls', 'xlsx'])) $icon = 'fa-file-excel';
                                                    @endphp
                                                    <i class="fa {{ $icon }} fa-lg me-3 text-primary"></i>
                                                    <a href="javascript:void(0)" onclick='previewFile({{ \Illuminate\Support\Js::from(route("files.preview", $file->id)) }}, {{ \Illuminate\Support\Js::from(route("files.download", $file->id)) }}, {{ \Illuminate\Support\Js::from($file->file_name) }}, {{ \Illuminate\Support\Js::from($ext) }})' class="text-primary fw-bold">
                                                        {{ $file->title }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ strtoupper($ext) }} Dosyası</td>
                                            <td>{{ $file->user->name }}</td>
                                            <td>{{ $file->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary shadow btn-xs sharp me-1" title="İndir">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    
                                                    @if(Auth::user()->hasPermission('delete_files') && Auth::id() == $file->user_id)
                                                    <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu dosyayı silmek istediğinize emin misiniz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp me-1" title="Sil">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    @if(Auth::user()->is_admin && Auth::user()->hasPermission('delete_files'))
                                                    <button type="button" class="btn btn-warning shadow btn-xs sharp" title="Taşı" onclick='openMoveModal({{ \Illuminate\Support\Js::from($file->id) }}, {{ \Illuminate\Support\Js::from($file->title) }})'>
                                                        <i class="fa fa-folder-open"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if($folders->count() == 0 && $files->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Bu klasör boş.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->is_admin && Auth::user()->hasPermission('delete_files'))
<!-- Move File Modal -->
<div class="modal fade" id="moveFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dosya Taşı: <span id="moveFileTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="moveFileForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hedef Klasör</label>
                        <select name="target_folder_id" class="form-control">
                            <option value="">Ana Dizin</option>
                            @foreach(\App\Models\Folder::query()->when(!Auth::user()->is_admin, function ($q) { $q->where('user_id', Auth::id()); })->get() as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Taşı</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(Auth::user()->hasPermission('upload_files'))
<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Klasör Oluştur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.storeFolder') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $currentFolder ? $currentFolder->id : '' }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Klasör Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Oluştur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dosya Yükle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="folder_id" value="{{ $currentFolder ? $currentFolder->id : '' }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya Seç</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Yükle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Önizleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0" id="previewBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openMoveModal(fileId, fileTitle) {
        const modal = new bootstrap.Modal(document.getElementById('moveFileModal'));
        document.getElementById('moveFileTitle').textContent = fileTitle;
        document.getElementById('moveFileForm').action = `/files/${fileId}/move`;
        modal.show();
    }

    function previewFile(previewUrl, downloadUrl, title, ext) {
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        document.getElementById('previewTitle').textContent = title;
        const body = document.getElementById('previewBody');
        body.innerHTML = '';

        const lowerExt = ext.toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(lowerExt)) {
            body.innerHTML = `<img src="${previewUrl}" class="img-fluid" style="max-height: 80vh;">`;
        } else if (lowerExt === 'pdf') {
            body.innerHTML = `<iframe src="${previewUrl}" style="width: 100%; height: 80vh;" frameborder="0"></iframe>`;
        } else {
            body.innerHTML = `<div class="p-5">
                                <i class="fa fa-file fa-4x text-muted mb-3"></i>
                                <p>Bu dosya türü için önizleme kullanılamıyor.</p>
                                <a href="${downloadUrl}" class="btn btn-primary">İndir</a>
                              </div>`;
        }

        modal.show();
    }
</script>
<style>
    .folder-card {
        transition: transform 0.2s;
    }
    .folder-card:hover {
        transform: translateY(-5px);
        background-color: #f8f9fa;
    }
</style>
@endpush
@endsection
