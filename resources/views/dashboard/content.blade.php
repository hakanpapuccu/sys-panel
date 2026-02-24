@extends('dashboard.index')

@section('title', 'Anasayfa')

@section('content')
<div class="content-body">
    <div class="container-fluid">

        <!-- Row 1: Key Metrics -->
        <div class="row">
            <!-- Pending Vacations / Tasks -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="widget-stat card bg-warning">
                    <div class="card-body p-4">
                        <div class="media">
                            <span class="me-3">
                                <i class="fas fa-hourglass-half"></i>
                            </span>
                            <div class="media-body text-white text-end">
                                @if(Auth::user()->is_admin)
                                <p class="mb-1">Onay Bekleyen</p>
                                <h3 class="text-white">{{ $pendingVacationsCount }}</h3>
                                @else
                                <p class="mb-1">Bekleyen Görevler</p>
                                <h3 class="text-white">{{ $pendingTasksCount }}</h3>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Polls -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="widget-stat card bg-info">
                    <div class="card-body p-4">
                        <div class="media">
                            <span class="me-3">
                                <i class="fas fa-poll"></i>
                            </span>
                            <div class="media-body text-white text-end">
                                <p class="mb-1">Aktif Anketler</p>
                                <h3 class="text-white">{{ $activePollsCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="widget-stat card bg-primary">
                    <div class="card-body p-4">
                        <div class="media">
                            <span class="me-3">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <div class="media-body text-white text-end">
                                <p class="mb-1">Okunmamış Mesaj</p>
                                <h3 class="text-white">{{ $unreadMessagesCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="widget-stat card bg-secondary">
                    <div class="card-body p-4">
                        <div class="media">
                            <span class="me-3">
                                <i class="fas fa-bullhorn"></i>
                            </span>
                            <div class="media-body text-white text-end">
                                <p class="mb-1">Toplam Duyuru</p>
                                <h3 class="text-white">{{ $announcementsCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        </div>

        <!-- Row 2: Pending Approvals (Admin Only) -->
        @if(Auth::user()->is_admin && Auth::user()->hasPermission('approve_vacations') && $pendingVacations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="fs-20 font-w700">Onay Bekleyen İzinler</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Ad Soyad</th>
                                        <th>Tarih</th>
                                        <th>Saat Aralığı</th>
                                        <th>Sebep</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingVacations as $vacation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="profile-k me-3">
                                                    <span class="bg-warning text-white">{{ substr($vacation->user->name ?? 'U', 0, 1) }}</span>
                                                </div>
                                                <span class="w-space-no">{{ $vacation->user->name ?? 'Bilinmiyor' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $vacation->vacation_date ? $vacation->vacation_date->format('d.m.Y') : '-' }}</td>
                                        <td>{{ $vacation->vacation_start ? $vacation->vacation_start->format('H:i') : '-' }} - {{ $vacation->vacation_end ? $vacation->vacation_end->format('H:i') : '-' }}</td>
                                        <td>{{ $vacation->vacation_why }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <form action="{{ route('vacations.verify', $vacation->id) }}" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success shadow btn-xs sharp">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('vacations.reject', $vacation->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Row 4: Latest Announcements & Recent Vacations -->
        <div class="row">
            <!-- Latest Announcements -->
	            <div class="{{ Auth::user()->hasPermission('access_chat') ? 'col-xl-6' : 'col-xl-12' }} col-lg-12">
	                <div class="card">
                    <div class="card-header border-0">
                        <div>
                            <h4 class="fs-20 font-w700">Son Duyurular</h4>
                        </div>
                        <div>
                            <a href="{{ route('announcements.index') }}" class="btn btn-outline-primary btn-rounded fs-18">Tümünü Gör</a>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        @forelse($latestAnnouncements as $announcement)
                        <div class="d-flex justify-content-between recent-emails mb-4 border-bottom pb-3 px-3">
                            <div class="d-flex">
                                <div class="profile-k">
                                    <span class="bg-info text-white"><i class="fas fa-bullhorn"></i></span>
                                </div>
                                <div class="ms-3">
                                    <h4 class="fs-18 font-w500">{{ $announcement->title }}</h4>
                                    <span class="font-w400 d-block text-muted">{{ Str::limit(strip_tags($announcement->content), 80) }}</span>
                                    <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-4">
                            <p class="text-muted">Henüz duyuru yok.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Vacations -->
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div>
                            <h4 class="fs-20 font-w700">Son İzin Hareketleri</h4>
                        </div>
                        <div>
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('vacations') }}" class="btn btn-outline-primary btn-rounded fs-18">Tümünü Gör</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-0">
                        @foreach($vacations as $vacation)
                        <div class="d-flex justify-content-between recent-emails mb-4 border-bottom pb-3 px-3">
                            <div class="d-flex">
                                <div class="profile-k">
                                    <span class="bg-primary">{{ substr($vacation->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div class="ms-3">
                                    <h4 class="fs-18 font-w500">{{ $vacation->user->name ?? 'Bilinmiyor' }}</h4>
                                    <span class="font-w400 d-block">{{ $vacation->vacation_why }}</span>
                                    <div class="final-badge mt-2">
                                        <x-vacation-status-badge :status="$vacation->is_verified" />
                                        <span class="badge text-black border ms-2">
                                            <i class="far fa-calendar me-2"></i>
                                            {{ $vacation->vacation_date ? $vacation->vacation_date->format('d.m.Y') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 5: Recent Tasks & General Chat -->
        <div class="row">
            <!-- Recent Tasks -->
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div>
                            <h4 class="fs-20 font-w700">Son Görevler</h4>
                        </div>
                        <div>
                            @if(Auth::user()->is_admin)
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-rounded">+ Yeni Görev</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-0">
                        @foreach($tasks as $task)
                        <div class="msg-bx d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 px-3">
                            <div class="msg d-flex align-items-center w-100">
                                <div class="image-box active">
                                    <span class="btn-icon-start text-primary"><i class="fas fa-tasks fa-2x"></i></span>
                                </div>
                                <div class="ms-3 w-100">
                                    <a href="{{ route('tasks.edit', $task->id) }}">
                                        <h4 class="fs-18 font-w600">{{ $task->title }}</h4>
                                    </a>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="me-auto badge badge-xs light badge-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                                            {{ $task->priority == 'high' ? 'Yüksek' : ($task->priority == 'medium' ? 'Orta' : 'Düşük') }}
                                        </span>
                                        <span class="me-4 fs-12">{{ $task->deadline ? $task->deadline->diffForHumans() : '' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

	            <!-- General Chat -->
	            @if(Auth::user()->hasPermission('access_chat'))
	            <div class="col-xl-6">
	                <div class="card">
	                    <div class="card-header border-0">
	                        <h4 class="fs-20 font-w700">Genel Sohbet</h4>
	                    </div>
	                    <div class="card-body">
	                        <div id="dashboard-general-messages" class="dashboard-chat-panel">
	                            <!-- Messages will be loaded here -->
	                        </div>
	                        <form id="dashboard-chat-form">
	                            <div class="input-group">
	                                <input type="text" class="form-control" id="dashboard-chat-input" placeholder="Mesajınızı yazın..." required>
	                                <button class="btn btn-primary" type="submit" aria-label="Mesaj gönder">
	                                    <i class="fa fa-paper-plane"></i>
	                                </button>
	                            </div>
	                        </form>
	                    </div>
	                </div>
	            </div>
	            @endif
        </div>

        <!-- Row 6: Calendar -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="fs-20 font-w700">İş Takvimi</h4>
                    </div>
                    <div class="card-body">
                        <div id='dashboard-calendar'></div>
                    </div>
                </div>
            </div>
        </div>

	        @push('scripts')
	        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
	        <script src="{{ asset('js/pages/dashboard-content.js') }}"></script>
	        <script>
	            window.SysPanelDashboardContent.init({
	                currentUserId: {{ auth()->id() }},
	                routes: {
	                    calendarEvents: '{{ route('calendar.events') }}',
	                    generalMessages: '{{ route('chat.general') }}',
	                    sendMessage: '{{ route('chat.send') }}'
	                }
	            });
	        </script>
	        @endpush

    </div>
</div>
@endsection
