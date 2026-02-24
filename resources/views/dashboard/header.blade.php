<!DOCTYPE html>
<html lang="tr">
<head>
	    <meta charset="utf-8">
	    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:title" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:image" content="https://fillow.dexignlab.com/xhtml/social-image.png" />
	<meta name="format-detection" content="telephone=no">

		<!-- PAGE TITLE HERE -->
		<title>{{ $site_title }}</title>
		<!-- FAVICONS ICON -->
		<link rel="shortcut icon" type="image/png" href="{{ $site_favicon ? asset('storage/' . $site_favicon) : asset('images/logo.png') }}" />

		@if(request()->routeIs('vacations', 'tasks.index'))
		<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
		@endif

		@if(request()->routeIs('vacations'))
		<link href="{{ asset('vendor/clockpicker/css/bootstrap-clockpicker.min.css') }}" rel="stylesheet">
		@endif

		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
		<link href="{{ asset('vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
	    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
	    <link href="{{ asset('css/app-ui.css') }}" rel="stylesheet">
		@stack('styles')

	</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
   <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

	      <!--**********************************
	        Main wrapper start
	    ***********************************-->
	    <div id="main-wrapper">

	        <!--**********************************
	            Nav header start
	        ***********************************-->
	       <div class="nav-header">
	            <a href="{{ route('dashboard') }}" class="brand-logo">
					<img src="{{ $site_logo ? asset('storage/' . $site_logo) : asset('images/logo.png') }}" alt="logo" width="75">
					<div class="brand-title">
						<h3 class="">{{ $site_title }}</h3>
					<span class="brand-sub-title">{{ auth()->user()->is_admin ? 'Yönetici' : 'Personel' }}</span>
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
	        <!--**********************************
	            Nav header end
	        ***********************************-->

			<!--**********************************
	            Header start
	        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="dashboard_bar">
                                @yield('title', 'Anasayfa')
                            </div>

                        </div>
                        <ul class="navbar-nav header-right">


								<li class="nav-item dropdown notification_dropdown">
	                                <button class="nav-link btn btn-link border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Bildirimler">
										<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M23.3333 19.8333H23.1187C23.2568 19.4597 23.3295 19.065 23.3333 18.6666V12.8333C23.3294 10.7663 22.6402 8.75902 21.3735 7.12565C20.1068 5.49228 18.3343 4.32508 16.3333 3.80679V3.49996C16.3333 2.88112 16.0875 2.28763 15.6499 1.85004C15.2123 1.41246 14.6188 1.16663 14 1.16663C13.3812 1.16663 12.7877 1.41246 12.3501 1.85004C11.9125 2.28763 11.6667 2.88112 11.6667 3.49996V3.80679C9.66574 4.32508 7.89317 5.49228 6.6265 7.12565C5.35983 8.75902 4.67058 10.7663 4.66667 12.8333V18.6666C4.67053 19.065 4.74316 19.4597 4.88133 19.8333H4.66667C4.35725 19.8333 4.0605 19.9562 3.84171 20.175C3.62292 20.3938 3.5 20.6905 3.5 21C3.5 21.3094 3.62292 21.6061 3.84171 21.8249C4.0605 22.0437 4.35725 22.1666 4.66667 22.1666H23.3333C23.6428 22.1666 23.9395 22.0437 24.1583 21.8249C24.3771 21.6061 24.5 21.3094 24.5 21C24.5 20.6905 24.3771 20.3938 24.1583 20.175C23.9395 19.9562 23.6428 19.8333 23.3333 19.8333Z" fill="#717579"/>
											<path d="M9.9819 24.5C10.3863 25.2088 10.971 25.7981 11.6766 26.2079C12.3823 26.6178 13.1838 26.8337 13.9999 26.8337C14.816 26.8337 15.6175 26.6178 16.3232 26.2079C17.0288 25.7981 17.6135 25.2088 18.0179 24.5H9.9819Z" fill="#717579"/>
										</svg>
	                                    @if(auth()->user()->unreadNotifications->count() > 0)
	                                        <span class="badge light text-white bg-warning rounded-circle">{{ auth()->user()->unreadNotifications->count() }}</span>
	                                    @endif
	                                </button>
	                                <div class="dropdown-menu dropdown-menu-end">
	                                    <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3 notification-scroll">
											<ul class="timeline">
	                                            @forelse(auth()->user()->unreadNotifications as $notification)
												<li>
												<div class="timeline-panel">
													<div class="media me-2 media-info">
														<i class="fa fa-bell"></i>
													</div>
													<div class="media-body">
														<h6 class="mb-1"><a href="{{ $notification->data['url'] ?? '#' }}">{{ $notification->data['message'] }}</a></h6>
														<small class="d-block">{{ $notification->created_at->diffForHumans() }}</small>
													</div>
												</div>
											</li>
                                            @empty
                                            <li>
                                                <div class="timeline-panel">
                                                    <div class="media-body">
                                                        <h6 class="mb-1">Yeni bildirim yok.</h6>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforelse
										</ul>
									</div>
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                        @csrf
                                        <button type="submit" class="all-notification border-0 bg-transparent w-100 text-start">
                                            Tümünü Okundu İşaretle <i class="ti-arrow-end"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>

									<li class="nav-item dropdown  header-profile">
										<button class="nav-link btn btn-link border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Kullanıcı menüsü">
											<img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/user.png') }}" width="56" alt=""/>
									</button>
									<div class="dropdown-menu dropdown-menu-end">
                                    <div class="header-profile-info p-3 border-bottom mb-2">
                                        <div class="d-flex align-items-center">
                                             <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/user.png') }}" width="50" height="50" class="rounded-circle me-3" alt=""/>
                                             <div>
                                                 <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                                 <span class="fs-12 text-muted d-block">{{ auth()->user()->email }}</span>
                                                 <div class="fs-12 text-primary font-w600">{{ auth()->user()->department->name ?? 'Departman Yok' }}</div>
                                             </div>
                                        </div>
                                    </div>
										<a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
											<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
											<span class="ms-2">Profil </span>
										</a>
	                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start">
                                            <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            <span class="ms-2">Çıkış </span>
                                        </button>
                                    </form>
								</div>
							</li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>

        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->
