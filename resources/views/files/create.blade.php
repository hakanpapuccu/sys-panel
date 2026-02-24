@extends('dashboard.index')

@section('title', 'Dosya Yükle')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('files.index') }}">Dosya Paylaşımı</a></li>
                <li class="breadcrumb-item active" aria-current="page">Yeni Dosya Yükle</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Dosya Yükle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Başlık</label>
                                    <input type="text" name="title" class="form-control" placeholder="Dosya başlığı" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Açıklama</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Dosya açıklaması" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dosya Seç</label>
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Yükle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
