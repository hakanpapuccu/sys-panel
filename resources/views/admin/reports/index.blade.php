@extends('dashboard.index')

@section('title', 'Raporlama')

@section('content')
@php
    $taskCompletionRate = $metrics['tasks_created'] > 0 ? round(($metrics['tasks_completed'] / $metrics['tasks_created']) * 100, 1) : 0;
    $pollResponsePerPoll = $metrics['polls_created'] > 0 ? round(($metrics['poll_responses'] / $metrics['polls_created']), 1) : 0;
@endphp
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Anasayfa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Raporlama</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->has('from') || $errors->has('to'))
                            <div class="alert alert-danger">
                                {{ $errors->first('from') ?: $errors->first('to') }}
                            </div>
                        @endif

                        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="from" class="form-label">Başlangıç Tarihi</label>
                                <input type="date" name="from" id="from" class="form-control" value="{{ $filters['from'] }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to" class="form-label">Bitiş Tarihi</label>
                                <input type="date" name="to" id="to" class="form-control" value="{{ $filters['to'] }}">
                            </div>
                            <div class="col-md-6 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filtrele</button>
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Sıfırla</a>
                                <a href="{{ route('admin.reports.export', ['from' => $filters['from'], 'to' => $filters['to']]) }}" class="btn btn-outline-success">CSV Dışa Aktar</a>
                            </div>
                        </form>
                        <div class="mt-3">
                            <span class="badge badge-light text-dark">Dönem: {{ $periodLabel }} ({{ $periodDays }} gün)</span>
                            <span class="badge badge-light text-dark">Görev Tamamlama: %{{ $taskCompletionRate }}</span>
                            <span class="badge badge-light text-dark">Anket Başına Yanıt: {{ $pollResponsePerPoll }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Yeni Kullanıcı</p>
                        <h3 class="mb-0">{{ number_format($metrics['users_created'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Oluşturulan Görev</p>
                        <h3 class="mb-0">{{ number_format($metrics['tasks_created'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Tamamlanan Görev</p>
                        <h3 class="mb-0">{{ number_format($metrics['tasks_completed'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Geciken Açık Görev (Anlık)</p>
                        <h3 class="mb-0">{{ number_format($metrics['open_overdue_tasks'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">İzin Talebi</p>
                        <h3 class="mb-0">{{ number_format($metrics['vacation_requests'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Yayınlanan Duyuru</p>
                        <h3 class="mb-0">{{ number_format($metrics['announcements_created'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Yüklenen Dosya</p>
                        <h3 class="mb-0">{{ number_format($metrics['files_uploaded'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Gönderilen Mesaj</p>
                        <h3 class="mb-0">{{ number_format($metrics['messages_sent'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Oluşturulan Anket</p>
                        <h3 class="mb-0">{{ number_format($metrics['polls_created'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Anket Yanıtı</p>
                        <h3 class="mb-0">{{ number_format($metrics['poll_responses'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Aktif Anket (Anlık)</p>
                        <h3 class="mb-0">{{ number_format($metrics['active_polls_now'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-1">Oluşturulan Toplantı</p>
                        <h3 class="mb-0">{{ number_format($metrics['meetings_created'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Görev Durum Dağılımı</h4>
                    </div>
                    <div class="card-body">
                        @forelse($taskStatusBreakdown as $row)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $row['label'] }}</span>
                                    <span>{{ $row['count'] }} (%{{ $row['percentage'] }})</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $row['class'] }}" role="progressbar" style="width: {{ $row['percentage'] }}%" aria-valuenow="{{ $row['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Bu dönemde görev verisi bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">İzin Durum Dağılımı</h4>
                    </div>
                    <div class="card-body">
                        @forelse($vacationStatusBreakdown as $row)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $row['label'] }}</span>
                                    <span>{{ $row['count'] }} (%{{ $row['percentage'] }})</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $row['class'] }}" role="progressbar" style="width: {{ $row['percentage'] }}%" aria-valuenow="{{ $row['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Bu dönemde izin verisi bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Günlük Aktivite Özeti</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Görev</th>
                                        <th>İzin</th>
                                        <th>Mesaj</th>
                                        <th>Duyuru</th>
                                        <th>Dosya</th>
                                        <th>Toplantı</th>
                                        <th>Toplam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyActivity as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['tasks'] }}</td>
                                            <td>{{ $row['vacations'] }}</td>
                                            <td>{{ $row['messages'] }}</td>
                                            <td>{{ $row['announcements'] }}</td>
                                            <td>{{ $row['files'] }}</td>
                                            <td>{{ $row['meetings'] }}</td>
                                            <td><strong>{{ $row['total'] }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">En Çok Görev Açanlar</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Kullanıcı</th>
                                        <th class="text-end">Görev</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topTaskCreators as $item)
                                        <tr>
                                            <td>{{ optional($item->createdBy)->name ?? 'Bilinmiyor' }}</td>
                                            <td class="text-end">{{ $item->total }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted">Bu dönem için veri yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">En Çok Dosya Yükleyenler</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Kullanıcı</th>
                                        <th class="text-end">Dosya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topFileUploaders as $item)
                                        <tr>
                                            <td>{{ optional($item->user)->name ?? 'Bilinmiyor' }}</td>
                                            <td class="text-end">{{ $item->total }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted">Bu dönem için veri yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
